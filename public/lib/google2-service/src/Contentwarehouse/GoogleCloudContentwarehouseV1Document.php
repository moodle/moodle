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

class GoogleCloudContentwarehouseV1Document extends \Google\Collection
{
  /**
   * No category is specified.
   */
  public const CONTENT_CATEGORY_CONTENT_CATEGORY_UNSPECIFIED = 'CONTENT_CATEGORY_UNSPECIFIED';
  /**
   * Content is of image type.
   */
  public const CONTENT_CATEGORY_CONTENT_CATEGORY_IMAGE = 'CONTENT_CATEGORY_IMAGE';
  /**
   * Content is of audio type.
   */
  public const CONTENT_CATEGORY_CONTENT_CATEGORY_AUDIO = 'CONTENT_CATEGORY_AUDIO';
  /**
   * Content is of video type.
   */
  public const CONTENT_CATEGORY_CONTENT_CATEGORY_VIDEO = 'CONTENT_CATEGORY_VIDEO';
  /**
   * No raw document specified or it is non-parsable
   */
  public const RAW_DOCUMENT_FILE_TYPE_RAW_DOCUMENT_FILE_TYPE_UNSPECIFIED = 'RAW_DOCUMENT_FILE_TYPE_UNSPECIFIED';
  /**
   * Adobe PDF format
   */
  public const RAW_DOCUMENT_FILE_TYPE_RAW_DOCUMENT_FILE_TYPE_PDF = 'RAW_DOCUMENT_FILE_TYPE_PDF';
  /**
   * Microsoft Word format
   */
  public const RAW_DOCUMENT_FILE_TYPE_RAW_DOCUMENT_FILE_TYPE_DOCX = 'RAW_DOCUMENT_FILE_TYPE_DOCX';
  /**
   * Microsoft Excel format
   */
  public const RAW_DOCUMENT_FILE_TYPE_RAW_DOCUMENT_FILE_TYPE_XLSX = 'RAW_DOCUMENT_FILE_TYPE_XLSX';
  /**
   * Microsoft Powerpoint format
   */
  public const RAW_DOCUMENT_FILE_TYPE_RAW_DOCUMENT_FILE_TYPE_PPTX = 'RAW_DOCUMENT_FILE_TYPE_PPTX';
  /**
   * UTF-8 encoded text format
   */
  public const RAW_DOCUMENT_FILE_TYPE_RAW_DOCUMENT_FILE_TYPE_TEXT = 'RAW_DOCUMENT_FILE_TYPE_TEXT';
  /**
   * TIFF or TIF image file format
   */
  public const RAW_DOCUMENT_FILE_TYPE_RAW_DOCUMENT_FILE_TYPE_TIFF = 'RAW_DOCUMENT_FILE_TYPE_TIFF';
  protected $collection_key = 'properties';
  protected $cloudAiDocumentType = GoogleCloudDocumentaiV1Document::class;
  protected $cloudAiDocumentDataType = '';
  /**
   * Indicates the category (image, audio, video etc.) of the original content.
   *
   * @var string
   */
  public $contentCategory;
  /**
   * Output only. The time when the document is created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The user who creates the document.
   *
   * @var string
   */
  public $creator;
  /**
   * Required. Display name of the document given by the user. This name will be
   * displayed in the UI. Customer can populate this field with the name of the
   * document. This differs from the 'title' field as 'title' is optional and
   * stores the top heading in the document.
   *
   * @var string
   */
  public $displayName;
  /**
   * Uri to display the document, for example, in the UI.
   *
   * @var string
   */
  public $displayUri;
  /**
   * Output only. If linked to a Collection with RetentionPolicy, the date when
   * the document becomes mutable.
   *
   * @var string
   */
  public $dispositionTime;
  /**
   * The Document schema name. Format: projects/{project_number}/locations/{loca
   * tion}/documentSchemas/{document_schema_id}.
   *
   * @var string
   */
  public $documentSchemaName;
  /**
   * Raw document content.
   *
   * @var string
   */
  public $inlineRawDocument;
  /**
   * Output only. Indicates if the document has a legal hold on it.
   *
   * @var bool
   */
  public $legalHold;
  /**
   * The resource name of the document. Format:
   * projects/{project_number}/locations/{location}/documents/{document_id}. The
   * name is ignored when creating a document.
   *
   * @var string
   */
  public $name;
  /**
   * Other document format, such as PPTX, XLXS
   *
   * @var string
   */
  public $plainText;
  protected $propertiesType = GoogleCloudContentwarehouseV1Property::class;
  protected $propertiesDataType = 'array';
  /**
   * This is used when DocAI was not used to load the document and parsing/
   * extracting is needed for the inline_raw_document. For example, if
   * inline_raw_document is the byte representation of a PDF file, then this
   * should be set to: RAW_DOCUMENT_FILE_TYPE_PDF.
   *
   * @var string
   */
  public $rawDocumentFileType;
  /**
   * Raw document file in Cloud Storage path.
   *
   * @var string
   */
  public $rawDocumentPath;
  /**
   * The reference ID set by customers. Must be unique per project and location.
   *
   * @var string
   */
  public $referenceId;
  /**
   * If true, text extraction will not be performed.
   *
   * @deprecated
   * @var bool
   */
  public $textExtractionDisabled;
  /**
   * If true, text extraction will be performed.
   *
   * @var bool
   */
  public $textExtractionEnabled;
  /**
   * Title that describes the document. This can be the top heading or text that
   * describes the document.
   *
   * @var string
   */
  public $title;
  /**
   * Output only. The time when the document is last updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * The user who lastly updates the document.
   *
   * @var string
   */
  public $updater;

  /**
   * Document AI format to save the structured content, including OCR.
   *
   * @param GoogleCloudDocumentaiV1Document $cloudAiDocument
   */
  public function setCloudAiDocument(GoogleCloudDocumentaiV1Document $cloudAiDocument)
  {
    $this->cloudAiDocument = $cloudAiDocument;
  }
  /**
   * @return GoogleCloudDocumentaiV1Document
   */
  public function getCloudAiDocument()
  {
    return $this->cloudAiDocument;
  }
  /**
   * Indicates the category (image, audio, video etc.) of the original content.
   *
   * Accepted values: CONTENT_CATEGORY_UNSPECIFIED, CONTENT_CATEGORY_IMAGE,
   * CONTENT_CATEGORY_AUDIO, CONTENT_CATEGORY_VIDEO
   *
   * @param self::CONTENT_CATEGORY_* $contentCategory
   */
  public function setContentCategory($contentCategory)
  {
    $this->contentCategory = $contentCategory;
  }
  /**
   * @return self::CONTENT_CATEGORY_*
   */
  public function getContentCategory()
  {
    return $this->contentCategory;
  }
  /**
   * Output only. The time when the document is created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The user who creates the document.
   *
   * @param string $creator
   */
  public function setCreator($creator)
  {
    $this->creator = $creator;
  }
  /**
   * @return string
   */
  public function getCreator()
  {
    return $this->creator;
  }
  /**
   * Required. Display name of the document given by the user. This name will be
   * displayed in the UI. Customer can populate this field with the name of the
   * document. This differs from the 'title' field as 'title' is optional and
   * stores the top heading in the document.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Uri to display the document, for example, in the UI.
   *
   * @param string $displayUri
   */
  public function setDisplayUri($displayUri)
  {
    $this->displayUri = $displayUri;
  }
  /**
   * @return string
   */
  public function getDisplayUri()
  {
    return $this->displayUri;
  }
  /**
   * Output only. If linked to a Collection with RetentionPolicy, the date when
   * the document becomes mutable.
   *
   * @param string $dispositionTime
   */
  public function setDispositionTime($dispositionTime)
  {
    $this->dispositionTime = $dispositionTime;
  }
  /**
   * @return string
   */
  public function getDispositionTime()
  {
    return $this->dispositionTime;
  }
  /**
   * The Document schema name. Format: projects/{project_number}/locations/{loca
   * tion}/documentSchemas/{document_schema_id}.
   *
   * @param string $documentSchemaName
   */
  public function setDocumentSchemaName($documentSchemaName)
  {
    $this->documentSchemaName = $documentSchemaName;
  }
  /**
   * @return string
   */
  public function getDocumentSchemaName()
  {
    return $this->documentSchemaName;
  }
  /**
   * Raw document content.
   *
   * @param string $inlineRawDocument
   */
  public function setInlineRawDocument($inlineRawDocument)
  {
    $this->inlineRawDocument = $inlineRawDocument;
  }
  /**
   * @return string
   */
  public function getInlineRawDocument()
  {
    return $this->inlineRawDocument;
  }
  /**
   * Output only. Indicates if the document has a legal hold on it.
   *
   * @param bool $legalHold
   */
  public function setLegalHold($legalHold)
  {
    $this->legalHold = $legalHold;
  }
  /**
   * @return bool
   */
  public function getLegalHold()
  {
    return $this->legalHold;
  }
  /**
   * The resource name of the document. Format:
   * projects/{project_number}/locations/{location}/documents/{document_id}. The
   * name is ignored when creating a document.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Other document format, such as PPTX, XLXS
   *
   * @param string $plainText
   */
  public function setPlainText($plainText)
  {
    $this->plainText = $plainText;
  }
  /**
   * @return string
   */
  public function getPlainText()
  {
    return $this->plainText;
  }
  /**
   * List of values that are user supplied metadata.
   *
   * @param GoogleCloudContentwarehouseV1Property[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return GoogleCloudContentwarehouseV1Property[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * This is used when DocAI was not used to load the document and parsing/
   * extracting is needed for the inline_raw_document. For example, if
   * inline_raw_document is the byte representation of a PDF file, then this
   * should be set to: RAW_DOCUMENT_FILE_TYPE_PDF.
   *
   * Accepted values: RAW_DOCUMENT_FILE_TYPE_UNSPECIFIED,
   * RAW_DOCUMENT_FILE_TYPE_PDF, RAW_DOCUMENT_FILE_TYPE_DOCX,
   * RAW_DOCUMENT_FILE_TYPE_XLSX, RAW_DOCUMENT_FILE_TYPE_PPTX,
   * RAW_DOCUMENT_FILE_TYPE_TEXT, RAW_DOCUMENT_FILE_TYPE_TIFF
   *
   * @param self::RAW_DOCUMENT_FILE_TYPE_* $rawDocumentFileType
   */
  public function setRawDocumentFileType($rawDocumentFileType)
  {
    $this->rawDocumentFileType = $rawDocumentFileType;
  }
  /**
   * @return self::RAW_DOCUMENT_FILE_TYPE_*
   */
  public function getRawDocumentFileType()
  {
    return $this->rawDocumentFileType;
  }
  /**
   * Raw document file in Cloud Storage path.
   *
   * @param string $rawDocumentPath
   */
  public function setRawDocumentPath($rawDocumentPath)
  {
    $this->rawDocumentPath = $rawDocumentPath;
  }
  /**
   * @return string
   */
  public function getRawDocumentPath()
  {
    return $this->rawDocumentPath;
  }
  /**
   * The reference ID set by customers. Must be unique per project and location.
   *
   * @param string $referenceId
   */
  public function setReferenceId($referenceId)
  {
    $this->referenceId = $referenceId;
  }
  /**
   * @return string
   */
  public function getReferenceId()
  {
    return $this->referenceId;
  }
  /**
   * If true, text extraction will not be performed.
   *
   * @deprecated
   * @param bool $textExtractionDisabled
   */
  public function setTextExtractionDisabled($textExtractionDisabled)
  {
    $this->textExtractionDisabled = $textExtractionDisabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getTextExtractionDisabled()
  {
    return $this->textExtractionDisabled;
  }
  /**
   * If true, text extraction will be performed.
   *
   * @param bool $textExtractionEnabled
   */
  public function setTextExtractionEnabled($textExtractionEnabled)
  {
    $this->textExtractionEnabled = $textExtractionEnabled;
  }
  /**
   * @return bool
   */
  public function getTextExtractionEnabled()
  {
    return $this->textExtractionEnabled;
  }
  /**
   * Title that describes the document. This can be the top heading or text that
   * describes the document.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Output only. The time when the document is last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * The user who lastly updates the document.
   *
   * @param string $updater
   */
  public function setUpdater($updater)
  {
    $this->updater = $updater;
  }
  /**
   * @return string
   */
  public function getUpdater()
  {
    return $this->updater;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1Document::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1Document');
