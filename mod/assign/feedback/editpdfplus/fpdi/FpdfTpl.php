<?php
/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2019 Setasign - Jan Slabon (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi;

/**
 * Class FpdfTpl
 *
 * This class adds a templating feature to FPDF.
 *
 * @package setasign\Fpdi
 */
class FpdfTpl extends \FPDF
{
    use FpdfTplTrait;
}
