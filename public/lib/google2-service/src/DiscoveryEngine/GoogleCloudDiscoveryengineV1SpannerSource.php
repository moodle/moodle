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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1SpannerSource extends \Google\Model
{
  /**
   * Required. The database ID of the source Spanner table.
   *
   * @var string
   */
  public $databaseId;
  /**
   * Whether to apply data boost on Spanner export. Enabling this option will
   * incur additional cost. More info can be found
   * [here](https://cloud.google.com/spanner/docs/databoost/databoost-
   * overview#billing_and_quotas).
   *
   * @var bool
   */
  public $enableDataBoost;
  /**
   * Required. The instance ID of the source Spanner table.
   *
   * @var string
   */
  public $instanceId;
  /**
   * The project ID that contains the Spanner source. Has a length limit of 128
   * characters. If not specified, inherits the project ID from the parent
   * request.
   *
   * @var string
   */
  public $projectId;
  /**
   * Required. The table name of the Spanner database that needs to be imported.
   *
   * @var string
   */
  public $tableId;

  /**
   * Required. The database ID of the source Spanner table.
   *
   * @param string $databaseId
   */
  public function setDatabaseId($databaseId)
  {
    $this->databaseId = $databaseId;
  }
  /**
   * @return string
   */
  public function getDatabaseId()
  {
    return $this->databaseId;
  }
  /**
   * Whether to apply data boost on Spanner export. Enabling this option will
   * incur additional cost. More info can be found
   * [here](https://cloud.google.com/spanner/docs/databoost/databoost-
   * overview#billing_and_quotas).
   *
   * @param bool $enableDataBoost
   */
  public function setEnableDataBoost($enableDataBoost)
  {
    $this->enableDataBoost = $enableDataBoost;
  }
  /**
   * @return bool
   */
  public function getEnableDataBoost()
  {
    return $this->enableDataBoost;
  }
  /**
   * Required. The instance ID of the source Spanner table.
   *
   * @param string $instanceId
   */
  public function setInstanceId($instanceId)
  {
    $this->instanceId = $instanceId;
  }
  /**
   * @return string
   */
  public function getInstanceId()
  {
    return $this->instanceId;
  }
  /**
   * The project ID that contains the Spanner source. Has a length limit of 128
   * characters. If not specified, inherits the project ID from the parent
   * request.
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
   * Required. The table name of the Spanner database that needs to be imported.
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
class_alias(GoogleCloudDiscoveryengineV1SpannerSource::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SpannerSource');
