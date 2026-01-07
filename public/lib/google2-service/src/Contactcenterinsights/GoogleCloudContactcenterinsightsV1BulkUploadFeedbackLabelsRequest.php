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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1BulkUploadFeedbackLabelsRequest extends \Google\Model
{
  protected $gcsSourceType = GoogleCloudContactcenterinsightsV1BulkUploadFeedbackLabelsRequestGcsSource::class;
  protected $gcsSourceDataType = '';
  protected $sheetsSourceType = GoogleCloudContactcenterinsightsV1BulkUploadFeedbackLabelsRequestSheetsSource::class;
  protected $sheetsSourceDataType = '';
  /**
   * Optional. If set, upload will not happen and the labels will be validated.
   * If not set, then default behavior will be to upload the labels after
   * validation is complete.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * A cloud storage bucket source.
   *
   * @param GoogleCloudContactcenterinsightsV1BulkUploadFeedbackLabelsRequestGcsSource $gcsSource
   */
  public function setGcsSource(GoogleCloudContactcenterinsightsV1BulkUploadFeedbackLabelsRequestGcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1BulkUploadFeedbackLabelsRequestGcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
  /**
   * A sheets document source.
   *
   * @param GoogleCloudContactcenterinsightsV1BulkUploadFeedbackLabelsRequestSheetsSource $sheetsSource
   */
  public function setSheetsSource(GoogleCloudContactcenterinsightsV1BulkUploadFeedbackLabelsRequestSheetsSource $sheetsSource)
  {
    $this->sheetsSource = $sheetsSource;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1BulkUploadFeedbackLabelsRequestSheetsSource
   */
  public function getSheetsSource()
  {
    return $this->sheetsSource;
  }
  /**
   * Optional. If set, upload will not happen and the labels will be validated.
   * If not set, then default behavior will be to upload the labels after
   * validation is complete.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1BulkUploadFeedbackLabelsRequest::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1BulkUploadFeedbackLabelsRequest');
