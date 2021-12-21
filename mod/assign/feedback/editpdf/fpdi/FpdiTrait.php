<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2020 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi;

use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParser;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\StreamReader;
use setasign\Fpdi\PdfParser\Type\PdfArray;
use setasign\Fpdi\PdfParser\Type\PdfBoolean;
use setasign\Fpdi\PdfParser\Type\PdfDictionary;
use setasign\Fpdi\PdfParser\Type\PdfHexString;
use setasign\Fpdi\PdfParser\Type\PdfIndirectObject;
use setasign\Fpdi\PdfParser\Type\PdfIndirectObjectReference;
use setasign\Fpdi\PdfParser\Type\PdfName;
use setasign\Fpdi\PdfParser\Type\PdfNull;
use setasign\Fpdi\PdfParser\Type\PdfNumeric;
use setasign\Fpdi\PdfParser\Type\PdfStream;
use setasign\Fpdi\PdfParser\Type\PdfString;
use setasign\Fpdi\PdfParser\Type\PdfToken;
use setasign\Fpdi\PdfParser\Type\PdfType;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PageBoundaries;
use setasign\Fpdi\PdfReader\PdfReader;
use setasign\Fpdi\PdfReader\PdfReaderException;
use /* This namespace/class is used by the commercial FPDI PDF-Parser add-on. */
    /** @noinspection PhpUndefinedClassInspection */
    /** @noinspection PhpUndefinedNamespaceInspection */
    setasign\FpdiPdfParser\PdfParser\PdfParser as FpdiPdfParser;

/**
 * The FpdiTrait
 *
 * This trait offers the core functionalities of FPDI. By passing them to a trait we can reuse it with e.g. TCPDF in a
 * very easy way.
 */
trait FpdiTrait
{
    /**
     * The pdf reader instances.
     *
     * @var PdfReader[]
     */
    protected $readers = [];

    /**
     * Instances created internally.
     *
     * @var array
     */
    protected $createdReaders = [];

    /**
     * The current reader id.
     *
     * @var string|null
     */
    protected $currentReaderId;

    /**
     * Data of all imported pages.
     *
     * @var array
     */
    protected $importedPages = [];

    /**
     * A map from object numbers of imported objects to new assigned object numbers by FPDF.
     *
     * @var array
     */
    protected $objectMap = [];

    /**
     * An array with information about objects, which needs to be copied to the resulting document.
     *
     * @var array
     */
    protected $objectsToCopy = [];

    /**
     * Release resources and file handles.
     *
     * This method is called internally when the document is created successfully. By default it only cleans up
     * stream reader instances which were created internally.
     *
     * @param bool $allReaders
     */
    public function cleanUp($allReaders = false)
    {
        $readers = $allReaders ? array_keys($this->readers) : $this->createdReaders;
        foreach ($readers as $id) {
            $this->readers[$id]->getParser()->getStreamReader()->cleanUp();
            unset($this->readers[$id]);
        }

        $this->createdReaders = [];
    }

    /**
     * Set the minimal PDF version.
     *
     * @param string $pdfVersion
     */
    protected function setMinPdfVersion($pdfVersion)
    {
        if (\version_compare($pdfVersion, $this->PDFVersion, '>')) {
            $this->PDFVersion = $pdfVersion;
        }
    }

    /** @noinspection PhpUndefinedClassInspection */
    /**
     * Get a new pdf parser instance.
     *
     * @param StreamReader $streamReader
     * @return PdfParser|FpdiPdfParser
     */
    protected function getPdfParserInstance(StreamReader $streamReader)
    {
        // note: if you get an exception here - turn off errors/warnings on not found for your autoloader.
        // psr-4 (https://www.php-fig.org/psr/psr-4/) says: Autoloader implementations MUST NOT throw
        // exceptions, MUST NOT raise errors of any level, and SHOULD NOT return a value.
        /** @noinspection PhpUndefinedClassInspection */
        if (\class_exists(FpdiPdfParser::class)) {
            /** @noinspection PhpUndefinedClassInspection */
            return new FpdiPdfParser($streamReader);
        }

        return new PdfParser($streamReader);
    }

    /**
     * Get an unique reader id by the $file parameter.
     *
     * @param string|resource|PdfReader|StreamReader $file An open file descriptor, a path to a file, a PdfReader
     *                                                     instance or a StreamReader instance.
     * @return string
     */
    protected function getPdfReaderId($file)
    {
        if (\is_resource($file)) {
            $id = (string) $file;
        } elseif (\is_string($file)) {
            $id = \realpath($file);
            if ($id === false) {
                $id = $file;
            }
        } elseif (\is_object($file)) {
            $id = \spl_object_hash($file);
        } else {
            throw new \InvalidArgumentException(
                \sprintf('Invalid type in $file parameter (%s)', \gettype($file))
            );
        }

        /** @noinspection OffsetOperationsInspection */
        if (isset($this->readers[$id])) {
            return $id;
        }

        if (\is_resource($file)) {
            $streamReader = new StreamReader($file);
        } elseif (\is_string($file)) {
            $streamReader = StreamReader::createByFile($file);
            $this->createdReaders[] = $id;
        } else {
            $streamReader = $file;
        }

        $reader = new PdfReader($this->getPdfParserInstance($streamReader));
        /** @noinspection OffsetOperationsInspection */
        $this->readers[$id] = $reader;

        return $id;
    }

    /**
     * Get a pdf reader instance by its id.
     *
     * @param string $id
     * @return PdfReader
     */
    protected function getPdfReader($id)
    {
        if (isset($this->readers[$id])) {
            return $this->readers[$id];
        }

        throw new \InvalidArgumentException(
            \sprintf('No pdf reader with the given id (%s) exists.', $id)
        );
    }

    /**
     * Set the source PDF file.
     *
     * @param string|resource|StreamReader $file Path to the file or a stream resource or a StreamReader instance.
     * @return int The page count of the PDF document.
     * @throws PdfParserException
     */
    public function setSourceFile($file)
    {
        $this->currentReaderId = $this->getPdfReaderId($file);
        $this->objectsToCopy[$this->currentReaderId] = [];

        $reader = $this->getPdfReader($this->currentReaderId);
        $this->setMinPdfVersion($reader->getPdfVersion());

        return $reader->getPageCount();
    }

    /**
     * Imports a page.
     *
     * @param int $pageNumber The page number.
     * @param string $box The page boundary to import. Default set to PageBoundaries::CROP_BOX.
     * @param bool $groupXObject Define the form XObject as a group XObject to support transparency (if used).
     * @return string A unique string identifying the imported page.
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws PdfReaderException
     * @see PageBoundaries
     */
    public function importPage($pageNumber, $box = PageBoundaries::CROP_BOX, $groupXObject = true)
    {
        if (null === $this->currentReaderId) {
            throw new \BadMethodCallException('No reader initiated. Call setSourceFile() first.');
        }

        $pageId = $this->currentReaderId;

        $pageNumber = (int)$pageNumber;
        $pageId .= '|' . $pageNumber . '|' . ($groupXObject ? '1' : '0');

        // for backwards compatibility with FPDI 1
        $box = \ltrim($box, '/');
        if (!PageBoundaries::isValidName($box)) {
            throw new \InvalidArgumentException(
                \sprintf('Box name is invalid: "%s"', $box)
            );
        }

        $pageId .= '|' . $box;

        if (isset($this->importedPages[$pageId])) {
            return $pageId;
        }

        $reader = $this->getPdfReader($this->currentReaderId);
        $page = $reader->getPage($pageNumber);

        $bbox = $page->getBoundary($box);
        if ($bbox === false) {
            throw new PdfReaderException(
                \sprintf("Page doesn't have a boundary box (%s).", $box),
                PdfReaderException::MISSING_DATA
            );
        }

        $dict = new PdfDictionary();
        $dict->value['Type'] = PdfName::create('XObject');
        $dict->value['Subtype'] = PdfName::create('Form');
        $dict->value['FormType'] = PdfNumeric::create(1);
        $dict->value['BBox'] = $bbox->toPdfArray();

        if ($groupXObject) {
            $this->setMinPdfVersion('1.4');
            $dict->value['Group'] = PdfDictionary::create([
                'Type' => PdfName::create('Group'),
                'S' => PdfName::create('Transparency')
            ]);
        }

        $resources = $page->getAttribute('Resources');
        if ($resources !== null) {
            $dict->value['Resources'] = $resources;
        }

        list($width, $height) = $page->getWidthAndHeight($box);

        $a = 1;
        $b = 0;
        $c = 0;
        $d = 1;
        $e = -$bbox->getLlx();
        $f = -$bbox->getLly();

        $rotation = $page->getRotation();

        if ($rotation !== 0) {
            $rotation *= -1;
            $angle = $rotation * M_PI / 180;
            $a = \cos($angle);
            $b = \sin($angle);
            $c = -$b;
            $d = $a;

            switch ($rotation) {
                case -90:
                    $e = -$bbox->getLly();
                    $f = $bbox->getUrx();
                    break;
                case -180:
                    $e = $bbox->getUrx();
                    $f = $bbox->getUry();
                    break;
                case -270:
                    $e = $bbox->getUry();
                    $f = -$bbox->getLlx();
                    break;
            }
        }

        // we need to rotate/translate
        if ($a != 1 || $b != 0 || $c != 0 || $d != 1 || $e != 0 || $f != 0) {
            $dict->value['Matrix'] = PdfArray::create([
                PdfNumeric::create($a), PdfNumeric::create($b), PdfNumeric::create($c),
                PdfNumeric::create($d), PdfNumeric::create($e), PdfNumeric::create($f)
            ]);
        }

        // try to use the existing content stream
        $pageDict = $page->getPageDictionary();

        try {
            $contentsObject = PdfType::resolve(PdfDictionary::get($pageDict, 'Contents'), $reader->getParser(), true);
            $contents =  PdfType::resolve($contentsObject, $reader->getParser());

            // just copy the stream reference if it is only a single stream
            if (
                ($contentsIsStream = ($contents instanceof PdfStream))
                || ($contents instanceof PdfArray && \count($contents->value) === 1)
            ) {
                if ($contentsIsStream) {
                    /**
                     * @var PdfIndirectObject $contentsObject
                     */
                    $stream = $contents;
                } else {
                    $stream = PdfType::resolve($contents->value[0], $reader->getParser());
                }

                $filter = PdfDictionary::get($stream->value, 'Filter');
                if (!$filter instanceof PdfNull) {
                    $dict->value['Filter'] = $filter;
                }
                $length = PdfType::resolve(PdfDictionary::get($stream->value, 'Length'), $reader->getParser());
                $dict->value['Length'] = $length;
                $stream->value = $dict;
                // otherwise extract it from the array and re-compress the whole stream
            } else {
                $streamContent = $this->compress
                    ? \gzcompress($page->getContentStream())
                    : $page->getContentStream();

                $dict->value['Length'] = PdfNumeric::create(\strlen($streamContent));
                if ($this->compress) {
                    $dict->value['Filter'] = PdfName::create('FlateDecode');
                }

                $stream = PdfStream::create($dict, $streamContent);
            }
        // Catch faulty pages and use an empty content stream
        } catch (FpdiException $e) {
            $dict->value['Length'] = PdfNumeric::create(0);
            $stream = PdfStream::create($dict, '');
        }

        $this->importedPages[$pageId] = [
            'objectNumber' => null,
            'readerId' => $this->currentReaderId,
            'id' => 'TPL' . $this->getNextTemplateId(),
            'width' => $width / $this->k,
            'height' => $height / $this->k,
            'stream' => $stream
        ];

        return $pageId;
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
        if (\is_array($x)) {
            /** @noinspection OffsetOperationsInspection */
            unset($x['pageId']);
            \extract($x, EXTR_IF_EXISTS);
            /** @noinspection NotOptimalIfConditionsInspection */
            if (\is_array($x)) {
                $x = 0;
            }
        }

        if (!isset($this->importedPages[$pageId])) {
            throw new \InvalidArgumentException('Imported page does not exist!');
        }

        $importedPage = $this->importedPages[$pageId];

        $originalSize = $this->getTemplateSize($pageId);
        $newSize = $this->getTemplateSize($pageId, $width, $height);
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
                $importedPage['id']
            )
        );

        return $newSize;
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
    public function getImportedPageSize($tpl, $width = null, $height = null)
    {
        if (isset($this->importedPages[$tpl])) {
            $importedPage = $this->importedPages[$tpl];

            if ($width === null && $height === null) {
                $width = $importedPage['width'];
                $height = $importedPage['height'];
            } elseif ($width === null) {
                $width = $height * $importedPage['width'] / $importedPage['height'];
            }

            if ($height  === null) {
                $height = $width * $importedPage['height'] / $importedPage['width'];
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

        return false;
    }

    /**
     * Writes a PdfType object to the resulting buffer.
     *
     * @param PdfType $value
     * @throws PdfTypeException
     */
    protected function writePdfType(PdfType $value)
    {
        if ($value instanceof PdfNumeric) {
            if (\is_int($value->value)) {
                $this->_put($value->value . ' ', false);
            } else {
                $this->_put(\rtrim(\rtrim(\sprintf('%.5F', $value->value), '0'), '.') . ' ', false);
            }
        } elseif ($value instanceof PdfName) {
            $this->_put('/' . $value->value . ' ', false);
        } elseif ($value instanceof PdfString) {
            $this->_put('(' . $value->value . ')', false);
        } elseif ($value instanceof PdfHexString) {
            $this->_put('<' . $value->value . '>');
        } elseif ($value instanceof PdfBoolean) {
            $this->_put($value->value ? 'true ' : 'false ', false);
        } elseif ($value instanceof PdfArray) {
            $this->_put('[', false);
            foreach ($value->value as $entry) {
                $this->writePdfType($entry);
            }
            $this->_put(']');
        } elseif ($value instanceof PdfDictionary) {
            $this->_put('<<', false);
            foreach ($value->value as $name => $entry) {
                $this->_put('/' . $name . ' ', false);
                $this->writePdfType($entry);
            }
            $this->_put('>>');
        } elseif ($value instanceof PdfToken) {
            $this->_put($value->value);
        } elseif ($value instanceof PdfNull) {
            $this->_put('null ');
        } elseif ($value instanceof PdfStream) {
            /**
             * @var $value PdfStream
             */
            $this->writePdfType($value->value);
            $this->_put('stream');
            $this->_put($value->getStream());
            $this->_put('endstream');
        } elseif ($value instanceof PdfIndirectObjectReference) {
            if (!isset($this->objectMap[$this->currentReaderId])) {
                $this->objectMap[$this->currentReaderId] = [];
            }

            if (!isset($this->objectMap[$this->currentReaderId][$value->value])) {
                $this->objectMap[$this->currentReaderId][$value->value] = ++$this->n;
                $this->objectsToCopy[$this->currentReaderId][] = $value->value;
            }

            $this->_put($this->objectMap[$this->currentReaderId][$value->value] . ' 0 R ', false);
        } elseif ($value instanceof PdfIndirectObject) {
            /**
             * @var PdfIndirectObject $value
             */
            $n = $this->objectMap[$this->currentReaderId][$value->objectNumber];
            $this->_newobj($n);
            $this->writePdfType($value->value);
            $this->_put('endobj');
        }
    }
}
