<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Contentwarehouse;

class GoogleCloudDocumentaiV1DocumentPage extends \Google\Collection
{
  protected $collection_key = 'visualElements';
  protected $blocksType = GoogleCloudDocumentaiV1DocumentPageBlock::class;
  protected $blocksDataType = 'array';
  protected $detectedBarcodesType = GoogleCloudDocumentaiV1DocumentPageDetectedBarcode::class;
  protected $detectedBarcodesDataType = 'array';
  protected $detectedLanguagesType = GoogleCloudDocumentaiV1DocumentPageDetectedLanguage::class;
  protected $detectedLanguagesDataType = 'array';
  protected $dimensionType = GoogleCloudDocumentaiV1DocumentPageDimension::class;
  protected $dimensionDataType = '';
  protected $formFieldsType = GoogleCloudDocumentaiV1DocumentPageFormField::class;
  protected $formFieldsDataType = 'array';
  protected $imageType = GoogleCloudDocumentaiV1DocumentPageImage::class;
  protected $imageDataType = '';
  protected $imageQualityScoresType = GoogleCloudDocumentaiV1DocumentPageImageQualityScores::class;
  protected $imageQualityScoresDataType = '';
  protected $layoutType = GoogleCloudDocumentaiV1DocumentPageLayout::class;
  protected $layoutDataType = '';
  protected $linesType = GoogleCloudDocumentaiV1DocumentPageLine::class;
  protected $linesDataType = 'array';
  /**
   * 1-based index for current Page in a parent Document. Useful when a page is
   * taken out of a Document for individual processing.
   *
   * @var int
   */
  public $pageNumber;
  protected $paragraphsType = GoogleCloudDocumentaiV1DocumentPageParagraph::class;
  protected $paragraphsDataType = 'array';
  protected $provenanceType = GoogleCloudDocumentaiV1DocumentProvenance::class;
  protected $provenanceDataType = '';
  protected $symbolsType = GoogleCloudDocumentaiV1DocumentPageSymbol::class;
  protected $symbolsDataType = 'array';
  protected $tablesType = GoogleCloudDocumentaiV1DocumentPageTable::class;
  protected $tablesDataType = 'array';
  protected $tokensType = GoogleCloudDocumentaiV1DocumentPageToken::class;
  protected $tokensDataType = 'array';
  protected $transformsType = GoogleCloudDocumentaiV1DocumentPageMatrix::class;
  protected $transformsDataType = 'array';
  protected $visualElementsType = GoogleCloudDocumentaiV1DocumentPageVisualElement::class;
  protected $visualElementsDataType = 'array';

  /**
   * A list of visually detected text blocks on the page. A block has a set of
   * lines (collected into paragraphs) that have a common line-spacing and
   * orientation.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageBlock[] $blocks
   */
  public function setBlocks($blocks)
  {
    $this->blocks = $blocks;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageBlock[]
   */
  public function getBlocks()
  {
    return $this->blocks;
  }
  /**
   * A list of detected barcodes.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageDetectedBarcode[] $detectedBarcodes
   */
  public function setDetectedBarcodes($detectedBarcodes)
  {
    $this->detectedBarcodes = $detectedBarcodes;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageDetectedBarcode[]
   */
  public function getDetectedBarcodes()
  {
    return $this->detectedBarcodes;
  }
  /**
   * A list of detected languages together with confidence.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageDetectedLanguage[] $detectedLanguages
   */
  public function setDetectedLanguages($detectedLanguages)
  {
    $this->detectedLanguages = $detectedLanguages;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageDetectedLanguage[]
   */
  public function getDetectedLanguages()
  {
    return $this->detectedLanguages;
  }
  /**
   * Physical dimension of the page.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageDimension $dimension
   */
  public function setDimension(GoogleCloudDocumentaiV1DocumentPageDimension $dimension)
  {
    $this->dimension = $dimension;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageDimension
   */
  public function getDimension()
  {
    return $this->dimension;
  }
  /**
   * A list of visually detected form fields on the page.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageFormField[] $formFields
   */
  public function setFormFields($formFields)
  {
    $this->formFields = $formFields;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageFormField[]
   */
  public function getFormFields()
  {
    return $this->formFields;
  }
  /**
   * Rendered image for this page. This image is preprocessed to remove any
   * skew, rotation, and distortions such that the annotation bounding boxes can
   * be upright and axis-aligned.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageImage $image
   */
  public function setImage(GoogleCloudDocumentaiV1DocumentPageImage $image)
  {
    $this->image = $image;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageImage
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Image quality scores.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageImageQualityScores $imageQualityScores
   */
  public function setImageQualityScores(GoogleCloudDocumentaiV1DocumentPageImageQualityScores $imageQualityScores)
  {
    $this->imageQualityScores = $imageQualityScores;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageImageQualityScores
   */
  public function getImageQualityScores()
  {
    return $this->imageQualityScores;
  }
  /**
   * Layout for the page.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageLayout $layout
   */
  public function setLayout(GoogleCloudDocumentaiV1DocumentPageLayout $layout)
  {
    $this->layout = $layout;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageLayout
   */
  public function getLayout()
  {
    return $this->layout;
  }
  /**
   * A list of visually detected text lines on the page. A collection of tokens
   * that a human would perceive as a line.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageLine[] $lines
   */
  public function setLines($lines)
  {
    $this->lines = $lines;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageLine[]
   */
  public function getLines()
  {
    return $this->lines;
  }
  /**
   * 1-based index for current Page in a parent Document. Useful when a page is
   * taken out of a Document for individual processing.
   *
   * @param int $pageNumber
   */
  public function setPageNumber($pageNumber)
  {
    $this->pageNumber = $pageNumber;
  }
  /**
   * @return int
   */
  public function getPageNumber()
  {
    return $this->pageNumber;
  }
  /**
   * A list of visually detected text paragraphs on the page. A collection of
   * lines that a human would perceive as a paragraph.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageParagraph[] $paragraphs
   */
  public function setParagraphs($paragraphs)
  {
    $this->paragraphs = $paragraphs;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageParagraph[]
   */
  public function getParagraphs()
  {
    return $this->paragraphs;
  }
  /**
   * The history of this page.
   *
   * @deprecated
   * @param GoogleCloudDocumentaiV1DocumentProvenance $provenance
   */
  public function setProvenance(GoogleCloudDocumentaiV1DocumentProvenance $provenance)
  {
    $this->provenance = $provenance;
  }
  /**
   * @deprecated
   * @return GoogleCloudDocumentaiV1DocumentProvenance
   */
  public function getProvenance()
  {
    return $this->provenance;
  }
  /**
   * A list of visually detected symbols on the page.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageSymbol[] $symbols
   */
  public function setSymbols($symbols)
  {
    $this->symbols = $symbols;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageSymbol[]
   */
  public function getSymbols()
  {
    return $this->symbols;
  }
  /**
   * A list of visually detected tables on the page.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageTable[] $tables
   */
  public function setTables($tables)
  {
    $this->tables = $tables;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageTable[]
   */
  public function getTables()
  {
    return $this->tables;
  }
  /**
   * A list of visually detected tokens on the page.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageToken[] $tokens
   */
  public function setTokens($tokens)
  {
    $this->tokens = $tokens;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageToken[]
   */
  public function getTokens()
  {
    return $this->tokens;
  }
  /**
   * Transformation matrices that were applied to the original document image to
   * produce Page.image.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageMatrix[] $transforms
   */
  public function setTransforms($transforms)
  {
    $this->transforms = $transforms;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageMatrix[]
   */
  public function getTransforms()
  {
    return $this->transforms;
  }
  /**
   * A list of detected non-text visual elements e.g. checkbox, signature etc.
   * on the page.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageVisualElement[] $visualElements
   */
  public function setVisualElements($visualElements)
  {
    $this->visualElements = $visualElements;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageVisualElement[]
   */
  public function getVisualElements()
  {
    return $this->visualElements;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentPage::class, 'Google_Service_Contentwarehouse_GoogleCloudDocumentaiV1DocumentPage');
