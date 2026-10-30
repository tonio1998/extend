<?php

/**
 * This file is part of FPDI
 *
 * @copyright Copyright (c) 2024 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace App\Library\FPDI\src;

use Codedge\Fpdf\Fpdf\Fpdf;

/**
 * Class FpdfTpl
 *
 * This class adds a templating feature to FPDF.
 */
class FpdfTpl extends Fpdf
{
    use FpdfTplTrait;
}
