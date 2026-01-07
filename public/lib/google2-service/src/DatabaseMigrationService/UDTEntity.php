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

namespace Google\Service\DatabaseMigrationService;

class UDTEntity extends \Google\Model
{
  /**
   * Custom engine specific features.
   *
   * @var array[]
   */
  public $customFeatures;
  /**
   * The SQL code which creates the udt body.
   *
   * @var string
   */
  public $udtBody;
  /**
   * The SQL code which creates the udt.
   *
   * @var string
   */
  public $udtSqlCode;

  /**
   * Custom engine specific features.
   *
   * @param array[] $customFeatures
   */
  public function setCustomFeatures($customFeatures)
  {
    $this->customFeatures = $customFeatures;
  }
  /**
   * @return array[]
   */
  public function getCustomFeatures()
  {
    return $this->customFeatures;
  }
  /**
   * The SQL code which creates the udt body.
   *
   * @param string $udtBody
   */
  public function setUdtBody($udtBody)
  {
    $this->udtBody = $udtBody;
  }
  /**
   * @return string
   */
  public function getUdtBody()
  {
    return $this->udtBody;
  }
  /**
   * The SQL code which creates the udt.
   *
   * @param string $udtSqlCode
   */
  public function setUdtSqlCode($udtSqlCode)
  {
    $this->udtSqlCode = $udtSqlCode;
  }
  /**
   * @return string
   */
  public function getUdtSqlCode()
  {
    return $this->udtSqlCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UDTEntity::class, 'Google_Service_DatabaseMigrationService_UDTEntity');
