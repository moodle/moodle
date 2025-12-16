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

namespace Google\Service\Bigquery;

class RowAccessPolicyReference extends \Google\Model
{
  /**
   * Required. The ID of the dataset containing this row access policy.
   *
   * @var string
   */
  public $datasetId;
  /**
   * Required. The ID of the row access policy. The ID must contain only letters
   * (a-z, A-Z), numbers (0-9), or underscores (_). The maximum length is 256
   * characters.
   *
   * @var string
   */
  public $policyId;
  /**
   * Required. The ID of the project containing this row access policy.
   *
   * @var string
   */
  public $projectId;
  /**
   * Required. The ID of the table containing this row access policy.
   *
   * @var string
   */
  public $tableId;

  /**
   * Required. The ID of the dataset containing this row access policy.
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
   * Required. The ID of the row access policy. The ID must contain only letters
   * (a-z, A-Z), numbers (0-9), or underscores (_). The maximum length is 256
   * characters.
   *
   * @param string $policyId
   */
  public function setPolicyId($policyId)
  {
    $this->policyId = $policyId;
  }
  /**
   * @return string
   */
  public function getPolicyId()
  {
    return $this->policyId;
  }
  /**
   * Required. The ID of the project containing this row access policy.
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
   * Required. The ID of the table containing this row access policy.
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
class_alias(RowAccessPolicyReference::class, 'Google_Service_Bigquery_RowAccessPolicyReference');
