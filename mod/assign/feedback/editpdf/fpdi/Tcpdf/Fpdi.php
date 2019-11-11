<?php

namespace setasign\Fpdi\Tcpdf;

use setasign\Fpdi\FpdiTrait;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\AsciiHex;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfHexString;
use setasign\Fpdi\PdfParser\Type\PdfIndirectObject;
use setasign\Fpdi\PdfParser\Type\PdfNull;
use setasign\Fpdi\PdfParser\Type\PdfNumeric;
use setasign\Fpdi\PdfParser\Type\PdfStream;
use setasign\Fpdi\PdfParser\Type\PdfString;
use setasign\Fpdi\PdfParser\Type\PdfType;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;

/**
 * Class Fpdi
 *
 * This class let you import pages of existing PDF documents into a reusable structure for TCPDF.
 *
 * @package setasign\Fpdi
 */
class Fpdi extends \pdf
{
    use FpdiTrait {
        writePdfType as fpdiWritePdfType;
        useImportedPage as fpdiUseImportedPage;
    }

    /**
     * FPDI version
     *
     * @string
     */
    const VERSION = '2.2.0';

    /**
     * A counter for template ids.
     *
     * @var int
     */
    protected $templateId = 0;

    /**
     * The currently used object number.
     *
     * @var int
     */
    protected $currentObjectNumber;

    protected function _enddoc()
    {
        parent::_enddoc();
        $this->cleanUp();
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

    /**
     * Draws an imported page onto the page or another template.
     *
     * Give only one of the size parameters (width, height) to calculate the other one automatically in view to the
     * aspect ratio.
     *
     * @param mixed $tpl The template id
     * @param float|int|array $x The abscissa of upper-left corner. Alternatively you could use an assoc array
     *                           with the keys "x", "y", "width", "height", "adjustPageSize".
     * @param float|int $y The ordinate of upper-left corner.
     * @param float|int|null $width The width.
     * @param float|int|null $height The height.
     * @param bool $adjustPageSize
     * @return array The size
     * @see FpdiTrait::getTemplateSize()
     */
    public function useTemplate($tpl, $x = 0, $y = 0, $width = null, $height = null, $adjustPageSize = false)
    {
        return $this->useImportedPage($tpl, $x, $y, $width, $height, $adjustPageSize);
    }

    /**
     * Draws an imported page onto the page.
     *
     * Give only one of the size parameters (width, height) to calculate the other one automatically in view to the
     * aspect ratio.
     *
     * @param mixed $pageId The page id
     * @param float|int|array $x The abscissa of upper-left corner. Alternatively you could use an assoc array
     *                           with the keys "x", "y", "width", "height", "adjustPageSize".
     * @param float|int $y The ordinate of upper-left corner.
     * @param float|int|null $width The width.
     * @param float|int|null $height The height.
     * @param bool $adjustPageSize
     * @return array The size.
     * @see Fpdi::getTemplateSize()
     */
    public function useImportedPage($pageId, $x = 0, $y = 0, $width = null, $height = null, $adjustPageSize = false)
    {
        $size = $this->fpdiUseImportedPage($pageId, $x, $y, $width, $height, $adjustPageSize);
        if ($this->inxobj) {
            $importedPage = $this->importedPages[$pageId];
            $this->xobjects[$this->xobjid]['importedPages'][$importedPage['id']] = $pageId;
        }

        return $size;
    }

    /**
     * Get the size of an imported page.
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
        return $this->getImportedPageSize($tpl, $width, $height);
    }

    /**
     * @inheritdoc
     */
    protected function _getxobjectdict()
    {
        $out = parent::_getxobjectdict();

        foreach ($this->importedPages as $key => $pageData) {
            $out .= '/' . $pageData['id'] . ' ' . $pageData['objectNumber'] . ' 0 R ';
        }

        return $out;
    }

    /**
     * @inheritdoc
     * @throws CrossReferenceException
     * @throws PdfParserException
     */
    protected function _putxobjects()
    {
        foreach ($this->importedPages as $key => $pageData) {
            $this->currentObjectNumber = $this->_newobj();
            $this->importedPages[$key]['objectNumber'] = $this->currentObjectNumber;
            $this->currentReaderId = $pageData['readerId'];
            $this->writePdfType($pageData['stream']);
            $this->_put('endobj');
        }

        foreach (\array_keys($this->readers) as $readerId) {
            $parser = $this->getPdfReader($readerId)->getParser();
            $this->currentReaderId = $readerId;

            while (($objectNumber = \array_pop($this->objectsToCopy[$readerId])) !== null) {
                try {
                    $object = $parser->getIndirectObject($objectNumber);

                } catch (CrossReferenceException $e) {
                    if ($e->getCode() === CrossReferenceException::OBJECT_NOT_FOUND) {
                        $object = PdfIndirectObject::create($objectNumber, 0, new PdfNull());
                    } else {
                        throw $e;
                    }
                }

                $this->writePdfType($object);
            }
        }

        // let's prepare resources for imported pages in templates
        foreach ($this->xobjects as $xObjectId => $data) {
            if (!isset($data['importedPages'])) {
                continue;
            }

            foreach ($data['importedPages'] as $id => $pageKey) {
                $page = $this->importedPages[$pageKey];
                $this->xobjects[$xObjectId]['xobjects'][$id] = ['n' => $page['objectNumber']];
            }
        }


        parent::_putxobjects();
        $this->currentObjectNumber = null;
    }

    /**
     * Append content to the buffer of TCPDF.
     *
     * @param string $s
     * @param bool $newLine
     */
    protected function _put($s, $newLine = true)
    {
        if ($newLine) {
            $this->setBuffer($s . "\n");
        } else {
            $this->setBuffer($s);
        }
    }

    /**
     * Begin a new object and return the object number.
     *
     * @param int|string $objid Object ID (leave empty to get a new ID).
     * @return int object number
     */
    protected function _newobj($objid = '')
    {
        $this->_out($this->_getobj($objid));
        return $this->n;
    }

    /**
     * Writes a PdfType object to the resulting buffer.
     *
     * @param PdfType $value
     * @throws PdfTypeException
     */
    protected function writePdfType(PdfType $value)
    {
        if (!$this->encrypted) {
            $this->fpdiWritePdfType($value);
            return;
        }

        if ($value instanceof PdfString) {
            $string = PdfString::unescape($value->value);
            $string = $this->_encrypt_data($this->currentObjectNumber, $string);
            $value->value = \TCPDF_STATIC::_escape($string);

        } elseif ($value instanceof PdfHexString) {
            $filter = new AsciiHex();
            $string = $filter->decode($value->value);
            $string = $this->_encrypt_data($this->currentObjectNumber, $string);
            $value->value = $filter->encode($string, true);

        } elseif ($value instanceof PdfStream) {
            $stream = $value->getStream();
            $stream = $this->_encrypt_data($this->currentObjectNumber, $stream);
            $dictionary = $value->value;
            $dictionary->value['Length'] = PdfNumeric::create(\strlen($stream));
            $value = PdfStream::create($dictionary, $stream);

        } elseif ($value instanceof PdfIndirectObject) {
            /**
             * @var $value PdfIndirectObject
             */
            $this->currentObjectNumber = $this->objectMap[$this->currentReaderId][$value->objectNumber];
        }

        $this->fpdiWritePdfType($value);
    }
}