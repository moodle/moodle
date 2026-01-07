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

namespace Google\Service\SQLAdmin;

class SqlServerDatabaseDetails extends \Google\Model
{
  /**
   * The version of SQL Server with which the database is to be made compatible
   *
   * @var int
   */
  public $compatibilityLevel;
  /**
   * The recovery model of a SQL Server database
   *
   * @var string
   */
  public $recoveryModel;

  /**
   * The version of SQL Server with which the database is to be made compatible
   *
   * @param int $compatibilityLevel
   */
  public function setCompatibilityLevel($compatibilityLevel)
  {
    $this->compatibilityLevel = $compatibilityLevel;
  }
  /**
   * @return int
   */
  public function getCompatibilityLevel()
  {
    return $this->compatibilityLevel;
  }
  /**
   * The recovery model of a SQL Server database
   *
   * @param string $recoveryModel
   */
  public function setRecoveryModel($recoveryModel)
  {
    $this->recoveryModel = $recoveryModel;
  }
  /**
   * @return string
   */
  public function getRecoveryModel()
  {
    return $this->recoveryModel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlServerDatabaseDetails::class, 'Google_Service_SQLAdmin_SqlServerDatabaseDetails');
