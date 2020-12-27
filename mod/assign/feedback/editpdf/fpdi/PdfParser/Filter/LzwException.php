<?php
/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2019 Setasign - Jan Slabon (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi\PdfParser\Filter;

/**
 * Exception for LZW filter class
 *
 * @package setasign\Fpdi\PdfParser\Filter
 */
class LzwException extends FilterException
{
    /**
     * @var integer
     */
    const LZW_FLAVOUR_NOT_SUPPORTED = 0x0501;
}
