<?php

namespace App\Helpers;

class PdfFixer
{
    /**
     * Automatically repairs and decompresses a PDF file so FPDI can parse it cleanly.
     *
     * @param  string  $filePath
     * @return bool
     */
    public static function fix($filePath)
    {
        if (! file_exists($filePath) || filesize($filePath) <= 0) {
            return false;
        }

        $content = file_get_contents($filePath);
        if (! $content) {
            return false;
        }

        // Check if PDF uses Compressed XRef or ObjStm
        $hasCompressedXref = (strpos($content, '/Type /XRef') !== false || strpos($content, '/Type/XRef') !== false);
        $hasObjStm = (strpos($content, '/ObjStm') !== false);

        if (! $hasCompressedXref && ! $hasObjStm) {
            // Already a standard uncompressed PDF
            return true;
        }

        try {
            $fixed = self::decompressPdf($content);
            if ($fixed && strlen($fixed) > 100) {
                file_put_contents($filePath, $fixed);

                return true;
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return false;
    }

    /**
     * Decompress PDF streams and ObjStm containers
     */
    private static function decompressPdf($content)
    {
        // 1. Uncompress all /FlateDecode streams in the PDF
        $pattern = '/<<[^>]*\/Filter\s*\/FlateDecode[^>]*>>\s*stream[\r\n]+(.*?)[\r\n]+endstream/s';

        $fixedContent = preg_replace_callback($pattern, function ($matches) {
            $header = $matches[0];
            $streamData = $matches[1];

            $decompressed = @gzuncompress($streamData);
            if ($decompressed === false) {
                $decompressed = @gzinflate($streamData);
            }
            if ($decompressed === false) {
                $decompressed = @zlib_decode($streamData);
            }

            if ($decompressed !== false) {
                $dict = substr($header, 0, strpos($header, 'stream'));
                $newDict = preg_replace('/\/Filter\s*\/FlateDecode/i', '', $dict);
                $newDict = preg_replace('/\/Length\s+\d+/i', '/Length '.strlen($decompressed), $newDict);

                return trim($newDict)."\r\nstream\r\n".$decompressed."\r\nendstream";
            }

            return $matches[0];
        }, $content);

        // 2. Expand /ObjStm streams into individual top-level PDF objects
        $objStmPattern = '/(\d+)\s+(\d+)\s+obj\s*<<[^>]*\/Type\s*\/ObjStm[^>]*>>\s*stream[\r\n]+(.*?)[\r\n]+endstream\s*endobj/s';

        $extraObjects = '';
        $fixedContent = preg_replace_callback($objStmPattern, function ($m) use (&$extraObjects) {
            $stream = $m[3];

            preg_match('/^\s*(\d+(?:\s+\d+)+)/', $stream, $headerMatch);
            if ($headerMatch) {
                $nums = preg_split('/\s+/', trim($headerMatch[0]));
                $count = count($nums) / 2;
                $firstOffset = intval($nums[1]);

                $body = substr($stream, $firstOffset);

                for ($i = 0; $i < $count; $i++) {
                    $objNum = $nums[$i * 2];
                    $startPos = intval($nums[$i * 2 + 1]);
                    $endPos = ($i < $count - 1) ? intval($nums[($i + 1) * 2 + 1]) : strlen($body);
                    $objBody = trim(substr($body, $startPos, $endPos - $startPos));
                    if ($objBody) {
                        $extraObjects .= "\r\n{$objNum} 0 obj\r\n{$objBody}\r\nendobj\r\n";
                    }
                }
            }

            return '';
        }, $fixedContent);

        if (! empty($extraObjects)) {
            $fixedContent .= $extraObjects;
        }

        // 3. Rebuild standard xref table
        return self::rebuildXref($fixedContent);
    }

    /**
     * Rebuild traditional ASCII xref section and trailer
     */
    private static function rebuildXref($content)
    {
        preg_match_all('/(\d+)\s+(\d+)\s+obj/i', $content, $matches, PREG_OFFSET_CAPTURE);
        if (empty($matches[0])) {
            return $content;
        }

        $objects = [];
        $maxObjNum = 0;

        foreach ($matches[0] as $idx => $match) {
            $objNum = intval($matches[1][$idx][0]);
            $genNum = intval($matches[2][$idx][0]);
            $offset = $match[1];

            $objects[$objNum] = [
                'offset' => $offset,
                'gen' => $genNum,
            ];
            if ($objNum > $maxObjNum) {
                $maxObjNum = $objNum;
            }
        }

        if (empty($objects)) {
            return $content;
        }

        // Strip old xref and startxref sections
        $cleanContent = preg_replace('/xref\s*\d+\s+\d+.*$/s', '', $content);
        $cleanContent = preg_replace('/startxref\s*\d+.*$/s', '', $cleanContent);
        $cleanContent = rtrim($cleanContent);

        $rootRef = '1 0 R';
        if (preg_match('/\/Root\s+(\d+\s+\d+\s+R)/i', $content, $rootMatch)) {
            $rootRef = $rootMatch[1];
        }

        $xrefOffset = strlen($cleanContent) + 2;

        $xref = "\r\nxref\r\n0 ".($maxObjNum + 1)."\r\n";
        $xref .= "0000000000 65535 f \r\n";

        for ($i = 1; $i <= $maxObjNum; $i++) {
            if (isset($objects[$i])) {
                $offStr = sprintf('%010d', $objects[$i]['offset']);
                $genStr = sprintf('%05d', $objects[$i]['gen']);
                $xref .= "{$offStr} {$genStr} n \r\n";
            } else {
                $xref .= "0000000000 65535 f \r\n";
            }
        }

        $xref .= "trailer\r\n<<\r\n/Size ".($maxObjNum + 1)."\r\n/Root {$rootRef}\r\n>>\r\n";
        $xref .= "startxref\r\n{$xrefOffset}\r\n%%EOF\r\n";

        return $cleanContent.$xref;
    }
}
