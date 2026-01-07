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

class GoogleCloudDocumentaiUiv1beta3ResyncDatasetMetadataDatasetResyncStatus extends \Google\Model
{
  /**
   * Default value.
   */
  public const DATASET_INCONSISTENCY_TYPE_DATASET_INCONSISTENCY_TYPE_UNSPECIFIED = 'DATASET_INCONSISTENCY_TYPE_UNSPECIFIED';
  /**
   * The marker file under the dataset folder is not found.
   */
  public const DATASET_INCONSISTENCY_TYPE_DATASET_INCONSISTENCY_TYPE_NO_STORAGE_MARKER = 'DATASET_INCONSISTENCY_TYPE_NO_STORAGE_MARKER';
  /**
   * The type of the inconsistency of the dataset.
   *
   * @var string
   */
  public $datasetInconsistencyType;
  protected $statusType = GoogleRpcStatus::class;
  protected $statusDataType = '';

  /**
   * The type of the inconsistency of the dataset.
   *
   * Accepted values: DATASET_INCONSISTENCY_TYPE_UNSPECIFIED,
   * DATASET_INCONSISTENCY_TYPE_NO_STORAGE_MARKER
   *
   * @param self::DATASET_INCONSISTENCY_TYPE_* $datasetInconsistencyType
   */
  public function setDatasetInconsistencyType($datasetInconsistencyType)
  {
    $this->datasetInconsistencyType = $datasetInconsistencyType;
  }
  /**
   * @return self::DATASET_INCONSISTENCY_TYPE_*
   */
  public function getDatasetInconsistencyType()
  {
    return $this->datasetInconsistencyType;
  }
  /**
   * The status of resyncing the dataset with regards to the detected
   * inconsistency. Empty if ResyncDatasetRequest.validate_only is `true`.
   *
   * @param GoogleRpcStatus $status
   */
  public function setStatus(GoogleRpcStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiUiv1beta3ResyncDatasetMetadataDatasetResyncStatus::class, 'Google_Service_Document_GoogleCloudDocumentaiUiv1beta3ResyncDatasetMetadataDatasetResyncStatus');
