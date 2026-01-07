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

class GooglePrivacyDlpV2DatabaseResourceRegex extends \Google\Model
{
  /**
   * Regex to test the database name against. If empty, all databases match.
   *
   * @var string
   */
  public $databaseRegex;
  /**
   * Regex to test the database resource's name against. An example of a
   * database resource name is a table's name. Other database resource names
   * like view names could be included in the future. If empty, all database
   * resources match.
   *
   * @var string
   */
  public $databaseResourceNameRegex;
  /**
   * Regex to test the instance name against. If empty, all instances match.
   *
   * @var string
   */
  public $instanceRegex;
  /**
   * For organizations, if unset, will match all projects. Has no effect for
   * configurations created within a project.
   *
   * @var string
   */
  public $projectIdRegex;

  /**
   * Regex to test the database name against. If empty, all databases match.
   *
   * @param string $databaseRegex
   */
  public function setDatabaseRegex($databaseRegex)
  {
    $this->databaseRegex = $databaseRegex;
  }
  /**
   * @return string
   */
  public function getDatabaseRegex()
  {
    return $this->databaseRegex;
  }
  /**
   * Regex to test the database resource's name against. An example of a
   * database resource name is a table's name. Other database resource names
   * like view names could be included in the future. If empty, all database
   * resources match.
   *
   * @param string $databaseResourceNameRegex
   */
  public function setDatabaseResourceNameRegex($databaseResourceNameRegex)
  {
    $this->databaseResourceNameRegex = $databaseResourceNameRegex;
  }
  /**
   * @return string
   */
  public function getDatabaseResourceNameRegex()
  {
    return $this->databaseResourceNameRegex;
  }
  /**
   * Regex to test the instance name against. If empty, all instances match.
   *
   * @param string $instanceRegex
   */
  public function setInstanceRegex($instanceRegex)
  {
    $this->instanceRegex = $instanceRegex;
  }
  /**
   * @return string
   */
  public function getInstanceRegex()
  {
    return $this->instanceRegex;
  }
  /**
   * For organizations, if unset, will match all projects. Has no effect for
   * configurations created within a project.
   *
   * @param string $projectIdRegex
   */
  public function setProjectIdRegex($projectIdRegex)
  {
    $this->projectIdRegex = $projectIdRegex;
  }
  /**
   * @return string
   */
  public function getProjectIdRegex()
  {
    return $this->projectIdRegex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DatabaseResourceRegex::class, 'Google_Service_DLP_GooglePrivacyDlpV2DatabaseResourceRegex');
