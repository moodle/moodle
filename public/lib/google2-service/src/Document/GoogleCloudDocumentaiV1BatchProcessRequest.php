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

class GoogleCloudDocumentaiV1BatchProcessRequest extends \Google\Model
{
  protected $documentOutputConfigType = GoogleCloudDocumentaiV1DocumentOutputConfig::class;
  protected $documentOutputConfigDataType = '';
  protected $inputDocumentsType = GoogleCloudDocumentaiV1BatchDocumentsInputConfig::class;
  protected $inputDocumentsDataType = '';
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
  /**
   * Whether human review should be skipped for this request. Default to
   * `false`.
   *
   * @deprecated
   * @var bool
   */
  public $skipHumanReview;

  /**
   * The output configuration for the BatchProcessDocuments method.
   *
   * @param GoogleCloudDocumentaiV1DocumentOutputConfig $documentOutputConfig
   */
  public function setDocumentOutputConfig(GoogleCloudDocumentaiV1DocumentOutputConfig $documentOutputConfig)
  {
    $this->documentOutputConfig = $documentOutputConfig;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentOutputConfig
   */
  public function getDocumentOutputConfig()
  {
    return $this->documentOutputConfig;
  }
  /**
   * The input documents for the BatchProcessDocuments method.
   *
   * @param GoogleCloudDocumentaiV1BatchDocumentsInputConfig $inputDocuments
   */
  public function setInputDocuments(GoogleCloudDocumentaiV1BatchDocumentsInputConfig $inputDocuments)
  {
    $this->inputDocuments = $inputDocuments;
  }
  /**
   * @return GoogleCloudDocumentaiV1BatchDocumentsInputConfig
   */
  public function getInputDocuments()
  {
    return $this->inputDocuments;
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
class_alias(GoogleCloudDocumentaiV1BatchProcessRequest::class, 'Google_Service_Document_GoogleCloudDocumentaiV1BatchProcessRequest');
