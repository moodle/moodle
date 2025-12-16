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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2DataProfileFindingLocation extends \Google\Model
{
  /**
   * Name of the container where the finding is located. The top-level name is
   * the source file name or table name. Names of some common storage containers
   * are formatted as follows: * BigQuery tables:
   * `{project_id}:{dataset_id}.{table_id}` * Cloud Storage files:
   * `gs://{bucket}/{path}`
   *
   * @var string
   */
  public $containerName;
  protected $dataProfileFindingRecordLocationType = GooglePrivacyDlpV2DataProfileFindingRecordLocation::class;
  protected $dataProfileFindingRecordLocationDataType = '';

  /**
   * Name of the container where the finding is located. The top-level name is
   * the source file name or table name. Names of some common storage containers
   * are formatted as follows: * BigQuery tables:
   * `{project_id}:{dataset_id}.{table_id}` * Cloud Storage files:
   * `gs://{bucket}/{path}`
   *
   * @param string $containerName
   */
  public function setContainerName($containerName)
  {
    $this->containerName = $containerName;
  }
  /**
   * @return string
   */
  public function getContainerName()
  {
    return $this->containerName;
  }
  /**
   * Location of a finding within a resource that produces a table data profile.
   *
   * @param GooglePrivacyDlpV2DataProfileFindingRecordLocation $dataProfileFindingRecordLocation
   */
  public function setDataProfileFindingRecordLocation(GooglePrivacyDlpV2DataProfileFindingRecordLocation $dataProfileFindingRecordLocation)
  {
    $this->dataProfileFindingRecordLocation = $dataProfileFindingRecordLocation;
  }
  /**
   * @return GooglePrivacyDlpV2DataProfileFindingRecordLocation
   */
  public function getDataProfileFindingRecordLocation()
  {
    return $this->dataProfileFindingRecordLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DataProfileFindingLocation::class, 'Google_Service_DLP_GooglePrivacyDlpV2DataProfileFindingLocation');
