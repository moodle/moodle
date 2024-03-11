<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2023 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi;

/**
 * Trait FpdfTplTrait
 *
 * This trait adds a templating feature to FPDF and tFPDF.
 */
trait FpdfTplTrait
{
    /**
     * Data of all created templates.
     *
     * @var array
     */
    protected $templates = [];

    /**
     * The template id for the currently created template.
     *
     * @var null|int
     */
    protected $currentTemplateId;

    /**
     * A counter for template ids.
     *
     * @var int
     */
    protected $templateId = 0;

    /**
     * Set the page format of the current page.
     *
     * @param array $size An array with two values defining the size.
     * @param string $orientation "L" for landscape, "P" for portrait.
     * @throws \BadMethodCallException
     */
    public function setPageFormat($size, $orientation)
    {
        if ($this->currentTemplateId !== null) {
            throw new \BadMethodCallException('The page format cannot be changed when writing to a template.');
        }

        if (!\in_array($orientation, ['P', 'L'], true)) {
            throw new \InvalidArgumentException(\sprintf(
                'Invalid page orientation "%s"! Only "P" and "L" are allowed!',
                $orientation
            ));
        }

        $size = $this->_getpagesize($size);

        if (
            $orientation != $this->CurOrientation
            || $size[0] != $this->CurPageSize[0]
            || $size[1] != $this->CurPageSize[1]
        ) {
            // New size or orientation
            if ($orientation === 'P') {
                $this->w = $size[0];
                $this->h = $size[1];
            } else {
                $this->w = $size[1];
                $this->h = $size[0];
            }
            $this->wPt = $this->w * $this->k;
            $this->hPt = $this->h * $this->k;
            $this->PageBreakTrigger = $this->h - $this->bMargin;
            $this->CurOrientation = $orientation;
            $this->CurPageSize = $size;

            $this->PageInfo[$this->page]['size'] = array($this->wPt, $this->hPt);
        }
    }

    /**
     * Draws a template onto the page or another template.
     *
     * Give only one of the size parameters (width, height) to calculate the other one automatically in view to the
     * aspect ratio.
     *
     * @param mixed $tpl The template id
     * @param array|float|int $x The abscissa of upper-left corner. Alternatively you could use an assoc array
     *                           with the keys "x", "y", "width", "height", "adjustPageSize".
     * @param float|int $y The ordinate of upper-left corner.
     * @param float|int|null $width The width.
     * @param float|int|null $height The height.
     * @param bool $adjustPageSize
     * @return array The size
     * @see FpdfTplTrait::getTemplateSize()
     */
    public function useTemplate($tpl, $x = 0, $y = 0, $width = null, $height = null, $adjustPageSize = false)
    {
        if (!isset($this->templates[$tpl])) {
            throw new \InvalidArgumentException('Template does not exist!');
        }

        if (\is_array($x)) {
            unset($x['tpl']);
            \extract($x, EXTR_IF_EXISTS);
            /** @noinspection NotOptimalIfConditionsInspection */
            /** @noinspection PhpConditionAlreadyCheckedInspection */
            if (\is_array($x)) {
                $x = 0;
            }
        }

        $template = $this->templates[$tpl];

        $originalSize = $this->getTemplateSize($tpl);
        $newSize = $this->getTemplateSize($tpl, $width, $height);
        if ($adjustPageSize) {
            $this->setPageFormat($newSize, $newSize['orientation']);
        }

        $this->_out(
        // reset standard values, translate and scale
            \sprintf(
                'q 0 J 1 w 0 j 0 G 0 g %.4F 0 0 %.4F %.4F %.4F cm /%s Do Q',
                ($newSize['width'] / $originalSize['width']),
                ($newSize['height'] / $originalSize['height']),
                $x * $this->k,
                ($this->h - $y - $newSize['height']) * $this->k,
                $template['id']
            )
        );

        return $newSize;
    }

    /**
     * Get the size of a template.
     *
     * Give only one of the size parameters (width, height) to calculate the other one automatically in view to the
     * aspect ratio.
     *
     * @param mixed $tpl The template id
     * @param float|int|null $width The width.
     * @param float|int|null $height The height.
     * @return array|bool An array with following keys: width, height, 0 (=width), 1 (=height), orientation (L or P)
     */
    public function getTemplateSize($tpl, $width = null, $height = null)
    {
        if (!isset($this->templates[$tpl])) {
            return false;
        }

        if ($width === null && $height === null) {
            $width = $this->templates[$tpl]['width'];
            $height = $this->templates[$tpl]['height'];
        } elseif ($width === null) {
            $width = $height * $this->templates[$tpl]['width'] / $this->templates[$tpl]['height'];
        }

        if ($height === null) {
            $height = $width * $this->templates[$tpl]['height'] / $this->templates[$tpl]['width'];
        }

        if ($height <= 0. || $width <= 0.) {
            throw new \InvalidArgumentException('Width or height parameter needs to be larger than zero.');
        }

        return [
            'width' => $width,
            'height' => $height,
            0 => $width,
            1 => $height,
            'orientation' => $width > $height ? 'L' : 'P'
        ];
    }

    /**
     * Begins a new template.
     *
     * @param float|int|null $width The width of the template. If null, the current page width is used.
     * @param float|int|null $height The height of the template. If null, the current page height is used.
     * @param bool $groupXObject Define the form XObject as a group XObject to support transparency (if used).
     * @return int A template identifier.
     */
    public function beginTemplate($width = null, $height = null, $groupXObject = false)
    {
        if ($width === null) {
            $width = $this->w;
        }

        if ($height === null) {
            $height = $this->h;
        }

        $templateId = $this->getNextTemplateId();

        // initiate buffer with current state of FPDF
        $buffer = "2 J\n"
            . \sprintf('%.2F w', $this->LineWidth * $this->k) . "\n";

        if ($this->FontFamily) {
            $buffer .= \sprintf("BT /F%d %.2F Tf ET\n", $this->CurrentFont['i'], $this->FontSizePt);
        }

        if ($this->DrawColor !== '0 G') {
            $buffer .= $this->DrawColor . "\n";
        }
        if ($this->FillColor !== '0 g') {
            $buffer .= $this->FillColor . "\n";
        }

        if ($groupXObject && \version_compare('1.4', $this->PDFVersion, '>')) {
            $this->PDFVersion = '1.4';
        }

        $this->templates[$templateId] = [
            'objectNumber' => null,
            'id' => 'TPL' . $templateId,
            'buffer' => $buffer,
            'width' => $width,
            'height' => $height,
            'groupXObject' => $groupXObject,
            'state' => [
                'x' => $this->x,
                'y' => $this->y,
                'AutoPageBreak' => $this->AutoPageBreak,
                'bMargin' => $this->bMargin,
                'tMargin' => $this->tMargin,
                'lMargin' => $this->lMargin,
                'rMargin' => $this->rMargin,
                'h' => $this->h,
                'hPt' => $this->hPt,
                'w' => $this->w,
                'wPt' => $this->wPt,
                'FontFamily' => $this->FontFamily,
                'FontStyle' => $this->FontStyle,
                'FontSizePt' => $this->FontSizePt,
                'FontSize' => $this->FontSize,
                'underline' => $this->underline,
                'TextColor' => $this->TextColor,
                'DrawColor' => $this->DrawColor,
                'FillColor' => $this->FillColor,
                'ColorFlag' => $this->ColorFlag
            ]
        ];

        $this->SetAutoPageBreak(false);
        $this->currentTemplateId = $templateId;

        $this->h = $height;
        $this->hPt = $height / $this->k;
        $this->w = $width;
        $this->wPt = $width / $this->k;

        $this->SetXY($this->lMargin, $this->tMargin);
        $this->SetRightMargin($this->w - $width + $this->rMargin);

        return $templateId;
    }

    /**
     * Ends a template.
     *
     * @return bool|int|null A template identifier.
     */
    public function endTemplate()
    {
        if ($this->currentTemplateId === null) {
            return false;
        }

        $templateId = $this->currentTemplateId;
        $template = $this->templates[$templateId];

        $state = $template['state'];
        $this->SetXY($state['x'], $state['y']);
        $this->tMargin = $state['tMargin'];
        $this->lMargin = $state['lMargin'];
        $this->rMargin = $state['rMargin'];
        $this->h = $state['h'];
        $this->hPt = $state['hPt'];
        $this->w = $state['w'];
        $this->wPt = $state['wPt'];
        $this->SetAutoPageBreak($state['AutoPageBreak'], $state['bMargin']);

        $this->FontFamily = $state['FontFamily'];
        $this->FontStyle = $state['FontStyle'];
        $this->FontSizePt = $state['FontSizePt'];
        $this->FontSize = $state['FontSize'];

        $this->TextColor = $state['TextColor'];
        $this->DrawColor = $state['DrawColor'];
        $this->FillColor = $state['FillColor'];
        $this->ColorFlag = $state['ColorFlag'];

        $this->underline = $state['underline'];

        $fontKey = $this->FontFamily . $this->FontStyle;
        if ($fontKey) {
            $this->CurrentFont =& $this->fonts[$fontKey];
        } else {
            unset($this->CurrentFont);
        }

        $this->currentTemplateId = null;

        return $templateId;
    }

    /**
     * Get the next template id.
     *
     * @return int
     */
    protected function getNextTemplateId()
    {
        return $this->templateId++;
    }

    /* overwritten FPDF methods: */

    /**
     * @inheritdoc
     */
    public function AddPage($orientation = '', $size = '', $rotation = 0)
    {
        if ($this->currentTemplateId !== null) {
            throw new \BadMethodCallException('Pages cannot be added when writing to a template.');
        }
        parent::AddPage($orientation, $size, $rotation);
    }

    /**
     * @inheritdoc
     */
    public function Link($x, $y, $w, $h, $link)
    {
        if ($this->currentTemplateId !== null) {
            throw new \BadMethodCallException('Links cannot be set when writing to a template.');
        }
        parent::Link($x, $y, $w, $h, $link);
    }

    /**
     * @inheritdoc
     */
    public function SetLink($link, $y = 0, $page = -1)
    {
        if ($this->currentTemplateId !== null) {
            throw new \BadMethodCallException('Links cannot be set when writing to a template.');
        }
        return parent::SetLink($link, $y, $page);
    }

    /**
     * @inheritdoc
     */
    public function SetDrawColor($r, $g = null, $b = null)
    {
        parent::SetDrawColor($r, $g, $b);
        if ($this->page === 0 && $this->currentTemplateId !== null) {
            $this->_out($this->DrawColor);
        }
    }

    /**
     * @inheritdoc
     */
    public function SetFillColor($r, $g = null, $b = null)
    {
        parent::SetFillColor($r, $g, $b);
        if ($this->page === 0 && $this->currentTemplateId !== null) {
            $this->_out($this->FillColor);
        }
    }

    /**
     * @inheritdoc
     */
    public function SetLineWidth($width)
    {
        parent::SetLineWidth($width);
        if ($this->page === 0 && $this->currentTemplateId !== null) {
            $this->_out(\sprintf('%.2F w', $width * $this->k));
        }
    }

    /**
     * @inheritdoc
     */
    public function SetFont($family, $style = '', $size = 0)
    {
        parent::SetFont($family, $style, $size);
        if ($this->page === 0 && $this->currentTemplateId !== null) {
            $this->_out(\sprintf('BT /F%d %.2F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
        }
    }

    /**
     * @inheritdoc
     */
    public function SetFontSize($size)
    {
        parent::SetFontSize($size);
        if ($this->page === 0 && $this->currentTemplateId !== null) {
            $this->_out(sprintf('BT /F%d %.2F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
        }
    }

    protected function _putimages()
    {
        parent::_putimages();

        foreach ($this->templates as $key => $template) {
            $this->_newobj();
            $this->templates[$key]['objectNumber'] = $this->n;

            $this->_put('<</Type /XObject /Subtype /Form /FormType 1');
            $this->_put(\sprintf(
                '/BBox[0 0 %.2F %.2F]',
                $template['width'] * $this->k,
                $template['height'] * $this->k
            ));
            $this->_put('/Resources 2 0 R'); // default resources dictionary of FPDF

            if ($this->compress) {
                $buffer = \gzcompress($template['buffer']);
                $this->_put('/Filter/FlateDecode');
            } else {
                $buffer = $template['buffer'];
            }

            $this->_put('/Length ' . \strlen($buffer));

            if ($template['groupXObject']) {
                $this->_put('/Group <</Type/Group/S/Transparency>>');
            }

            $this->_put('>>');
            $this->_putstream($buffer);
            $this->_put('endobj');
        }
    }

    /**
     * @inheritdoc
     */
    protected function _putxobjectdict()
    {
        foreach ($this->templates as $key => $template) {
            $this->_put('/' . $template['id'] . ' ' . $template['objectNumber'] . ' 0 R');
        }

        parent::_putxobjectdict();
    }

    /**
     * @inheritdoc
     */
    public function _out($s)
    {
        if ($this->currentTemplateId !== null) {
            $this->templates[$this->currentTemplateId]['buffer'] .= $s . "\n";
        } else {
            parent::_out($s);
        }
    }
}
