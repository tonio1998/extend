<?php

/**
 * This file is part of FPDI
 *
 * @copyright Copyright (c) 2024 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace App\Library\FPDI\src;

/**
 * Class Fpdi
 *
 * This class let you import pages of existing PDF documents into a reusable structure for FPDF.
 */
class Fpdi extends FpdfTpl
{
    use FpdfTrait;
    use FpdiTrait;

    /**
     * FPDI version
     *
     * @string
     */
    const VERSION = '2.6.3';
}
