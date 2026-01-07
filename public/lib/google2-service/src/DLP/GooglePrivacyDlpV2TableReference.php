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

class GooglePrivacyDlpV2TableReference extends \Google\Model
{
  /**
   * Dataset ID of the table.
   *
   * @var string
   */
  public $datasetId;
  /**
   * The Google Cloud project ID of the project containing the table. If
   * omitted, the project ID is inferred from the parent project. This field is
   * required if the parent resource is an organization.
   *
   * @var string
   */
  public $projectId;
  /**
   * Name of the table.
   *
   * @var string
   */
  public $tableId;

  /**
   * Dataset ID of the table.
   *
   * @param string $datasetId
   */
  public function setDatasetId($datasetId)
  {
    $this->datasetId = $datasetId;
  }
  /**
   * @return string
   */
  public function getDatasetId()
  {
    return $this->datasetId;
  }
  /**
   * The Google Cloud project ID of the project containing the table. If
   * omitted, the project ID is inferred from the parent project. This field is
   * required if the parent resource is an organization.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Name of the table.
   *
   * @param string $tableId
   */
  public function setTableId($tableId)
  {
    $this->tableId = $tableId;
  }
  /**
   * @return string
   */
  public function getTableId()
  {
    return $this->tableId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2TableReference::class, 'Google_Service_DLP_GooglePrivacyDlpV2TableReference');
