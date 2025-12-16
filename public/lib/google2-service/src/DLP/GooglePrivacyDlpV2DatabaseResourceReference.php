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

class GooglePrivacyDlpV2DatabaseResourceReference extends \Google\Model
{
  /**
   * Required. Name of a database within the instance.
   *
   * @var string
   */
  public $database;
  /**
   * Required. Name of a database resource, for example, a table within the
   * database.
   *
   * @var string
   */
  public $databaseResource;
  /**
   * Required. The instance where this resource is located. For example: Cloud
   * SQL instance ID.
   *
   * @var string
   */
  public $instance;
  /**
   * Required. If within a project-level config, then this must match the
   * config's project ID.
   *
   * @var string
   */
  public $projectId;

  /**
   * Required. Name of a database within the instance.
   *
   * @param string $database
   */
  public function setDatabase($database)
  {
    $this->database = $database;
  }
  /**
   * @return string
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * Required. Name of a database resource, for example, a table within the
   * database.
   *
   * @param string $databaseResource
   */
  public function setDatabaseResource($databaseResource)
  {
    $this->databaseResource = $databaseResource;
  }
  /**
   * @return string
   */
  public function getDatabaseResource()
  {
    return $this->databaseResource;
  }
  /**
   * Required. The instance where this resource is located. For example: Cloud
   * SQL instance ID.
   *
   * @param string $instance
   */
  public function setInstance($instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return string
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * Required. If within a project-level config, then this must match the
   * config's project ID.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DatabaseResourceReference::class, 'Google_Service_DLP_GooglePrivacyDlpV2DatabaseResourceReference');
