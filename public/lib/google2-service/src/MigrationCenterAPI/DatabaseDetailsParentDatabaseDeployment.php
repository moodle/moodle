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

namespace Google\Service\MigrationCenterAPI;

class DatabaseDetailsParentDatabaseDeployment extends \Google\Model
{
  /**
   * Optional. The parent database deployment generated ID.
   *
   * @var string
   */
  public $generatedId;
  /**
   * Optional. The parent database deployment optional manual unique ID set by
   * the user.
   *
   * @var string
   */
  public $manualUniqueId;

  /**
   * Optional. The parent database deployment generated ID.
   *
   * @param string $generatedId
   */
  public function setGeneratedId($generatedId)
  {
    $this->generatedId = $generatedId;
  }
  /**
   * @return string
   */
  public function getGeneratedId()
  {
    return $this->generatedId;
  }
  /**
   * Optional. The parent database deployment optional manual unique ID set by
   * the user.
   *
   * @param string $manualUniqueId
   */
  public function setManualUniqueId($manualUniqueId)
  {
    $this->manualUniqueId = $manualUniqueId;
  }
  /**
   * @return string
   */
  public function getManualUniqueId()
  {
    return $this->manualUniqueId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseDetailsParentDatabaseDeployment::class, 'Google_Service_MigrationCenterAPI_DatabaseDetailsParentDatabaseDeployment');
