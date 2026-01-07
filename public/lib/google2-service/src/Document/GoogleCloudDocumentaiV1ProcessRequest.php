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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1ProcessRequest extends \Google\Model
{
  /**
   * Specifies which fields to include in the ProcessResponse.document output.
   * Only supports top-level document and pages field, so it must be in the form
   * of `{document_field_name}` or `pages.{page_field_name}`.
   *
   * @var string
   */
  public $fieldMask;
  protected $gcsDocumentType = GoogleCloudDocumentaiV1GcsDocument::class;
  protected $gcsDocumentDataType = '';
  /**
   * Optional. Option to remove images from the document.
   *
   * @var bool
   */
  public $imagelessMode;
  protected $inlineDocumentType = GoogleCloudDocumentaiV1Document::class;
  protected $inlineDocumentDataType = '';
  /**
   * Optional. The labels with user-defined metadata for the request. Label keys
   * and values can be no longer than 63 characters (Unicode codepoints) and can
   * only contain lowercase letters, numeric characters, underscores, and
   * dashes. International characters are allowed. Label values are optional.
   * Label keys must start with a letter.
   *
   * @var string[]
   */
  public $labels;
  protected $processOptionsType = GoogleCloudDocumentaiV1ProcessOptions::class;
  protected $processOptionsDataType = '';
  protected $rawDocumentType = GoogleCloudDocumentaiV1RawDocument::class;
  protected $rawDocumentDataType = '';
  /**
   * Whether human review should be skipped for this request. Default to
   * `false`.
   *
   * @deprecated
   * @var bool
   */
  public $skipHumanReview;

  /**
   * Specifies which fields to include in the ProcessResponse.document output.
   * Only supports top-level document and pages field, so it must be in the form
   * of `{document_field_name}` or `pages.{page_field_name}`.
   *
   * @param string $fieldMask
   */
  public function setFieldMask($fieldMask)
  {
    $this->fieldMask = $fieldMask;
  }
  /**
   * @return string
   */
  public function getFieldMask()
  {
    return $this->fieldMask;
  }
  /**
   * A raw document on Google Cloud Storage.
   *
   * @param GoogleCloudDocumentaiV1GcsDocument $gcsDocument
   */
  public function setGcsDocument(GoogleCloudDocumentaiV1GcsDocument $gcsDocument)
  {
    $this->gcsDocument = $gcsDocument;
  }
  /**
   * @return GoogleCloudDocumentaiV1GcsDocument
   */
  public function getGcsDocument()
  {
    return $this->gcsDocument;
  }
  /**
   * Optional. Option to remove images from the document.
   *
   * @param bool $imagelessMode
   */
  public function setImagelessMode($imagelessMode)
  {
    $this->imagelessMode = $imagelessMode;
  }
  /**
   * @return bool
   */
  public function getImagelessMode()
  {
    return $this->imagelessMode;
  }
  /**
   * An inline document proto.
   *
   * @param GoogleCloudDocumentaiV1Document $inlineDocument
   */
  public function setInlineDocument(GoogleCloudDocumentaiV1Document $inlineDocument)
  {
    $this->inlineDocument = $inlineDocument;
  }
  /**
   * @return GoogleCloudDocumentaiV1Document
   */
  public function getInlineDocument()
  {
    return $this->inlineDocument;
  }
  /**
   * Optional. The labels with user-defined metadata for the request. Label keys
   * and values can be no longer than 63 characters (Unicode codepoints) and can
   * only contain lowercase letters, numeric characters, underscores, and
   * dashes. International characters are allowed. Label values are optional.
   * Label keys must start with a letter.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Inference-time options for the process API
   *
   * @param GoogleCloudDocumentaiV1ProcessOptions $processOptions
   */
  public function setProcessOptions(GoogleCloudDocumentaiV1ProcessOptions $processOptions)
  {
    $this->processOptions = $processOptions;
  }
  /**
   * @return GoogleCloudDocumentaiV1ProcessOptions
   */
  public function getProcessOptions()
  {
    return $this->processOptions;
  }
  /**
   * A raw document content (bytes).
   *
   * @param GoogleCloudDocumentaiV1RawDocument $rawDocument
   */
  public function setRawDocument(GoogleCloudDocumentaiV1RawDocument $rawDocument)
  {
    $this->rawDocument = $rawDocument;
  }
  /**
   * @return GoogleCloudDocumentaiV1RawDocument
   */
  public function getRawDocument()
  {
    return $this->rawDocument;
  }
  /**
   * Whether human review should be skipped for this request. Default to
   * `false`.
   *
   * @deprecated
   * @param bool $skipHumanReview
   */
  public function setSkipHumanReview($skipHumanReview)
  {
    $this->skipHumanReview = $skipHumanReview;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getSkipHumanReview()
  {
    return $this->skipHumanReview;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1ProcessRequest::class, 'Google_Service_Document_GoogleCloudDocumentaiV1ProcessRequest');
