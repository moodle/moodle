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

class SqlServerServerFlag extends \Google\Model
{
  /**
   * Required. The server flag name.
   *
   * @var string
   */
  public $serverFlagName;
  /**
   * Required. The server flag value set by the user.
   *
   * @var string
   */
  public $value;
  /**
   * Required. The server flag actual value. If `value_in_use` is different from
   * `value` it means that either the configuration change was not applied or it
   * is an expected behavior. See SQL Server documentation for more details.
   *
   * @var string
   */
  public $valueInUse;

  /**
   * Required. The server flag name.
   *
   * @param string $serverFlagName
   */
  public function setServerFlagName($serverFlagName)
  {
    $this->serverFlagName = $serverFlagName;
  }
  /**
   * @return string
   */
  public function getServerFlagName()
  {
    return $this->serverFlagName;
  }
  /**
   * Required. The server flag value set by the user.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
  /**
   * Required. The server flag actual value. If `value_in_use` is different from
   * `value` it means that either the configuration change was not applied or it
   * is an expected behavior. See SQL Server documentation for more details.
   *
   * @param string $valueInUse
   */
  public function setValueInUse($valueInUse)
  {
    $this->valueInUse = $valueInUse;
  }
  /**
   * @return string
   */
  public function getValueInUse()
  {
    return $this->valueInUse;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlServerServerFlag::class, 'Google_Service_MigrationCenterAPI_SqlServerServerFlag');
