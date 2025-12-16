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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1beta1ExportDataRequest extends \Google\Model
{
  /**
   * Required. Annotated dataset resource name. DataItem in Dataset and their
   * annotations in specified annotated dataset will be exported. It's in format
   * of projects/{project_id}/datasets/{dataset_id}/annotatedDatasets/
   * {annotated_dataset_id}
   *
   * @var string
   */
  public $annotatedDataset;
  /**
   * Optional. Filter is not supported at this moment.
   *
   * @var string
   */
  public $filter;
  protected $outputConfigType = GoogleCloudDatalabelingV1beta1OutputConfig::class;
  protected $outputConfigDataType = '';
  /**
   * Email of the user who started the export task and should be notified by
   * email. If empty no notification will be sent.
   *
   * @var string
   */
  public $userEmailAddress;

  /**
   * Required. Annotated dataset resource name. DataItem in Dataset and their
   * annotations in specified annotated dataset will be exported. It's in format
   * of projects/{project_id}/datasets/{dataset_id}/annotatedDatasets/
   * {annotated_dataset_id}
   *
   * @param string $annotatedDataset
   */
  public function setAnnotatedDataset($annotatedDataset)
  {
    $this->annotatedDataset = $annotatedDataset;
  }
  /**
   * @return string
   */
  public function getAnnotatedDataset()
  {
    return $this->annotatedDataset;
  }
  /**
   * Optional. Filter is not supported at this moment.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Required. Specify the output destination.
   *
   * @param GoogleCloudDatalabelingV1beta1OutputConfig $outputConfig
   */
  public function setOutputConfig(GoogleCloudDatalabelingV1beta1OutputConfig $outputConfig)
  {
    $this->outputConfig = $outputConfig;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1OutputConfig
   */
  public function getOutputConfig()
  {
    return $this->outputConfig;
  }
  /**
   * Email of the user who started the export task and should be notified by
   * email. If empty no notification will be sent.
   *
   * @param string $userEmailAddress
   */
  public function setUserEmailAddress($userEmailAddress)
  {
    $this->userEmailAddress = $userEmailAddress;
  }
  /**
   * @return string
   */
  public function getUserEmailAddress()
  {
    return $this->userEmailAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1ExportDataRequest::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1ExportDataRequest');
