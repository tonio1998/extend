<?php

/**
 * This file is part of FPDI
 *
 * @copyright Copyright (c) 2024 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace App\Library\FPDI\src;

/**
 * Base exception class for the FPDI package.
 */
class FpdiException extends \Exception {}

if (! class_exists('setasign\Fpdi\FpdiException', false)) {
    class_alias(FpdiException::class, 'setasign\Fpdi\FpdiException');
}
