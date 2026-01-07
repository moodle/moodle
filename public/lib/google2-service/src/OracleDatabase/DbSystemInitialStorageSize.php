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

namespace Google\Service\OracleDatabase;

class DbSystemInitialStorageSize extends \Google\Model
{
  /**
   * Output only. The name of the resource.
   *
   * @var string
   */
  public $name;
  protected $propertiesType = DbSystemInitialStorageSizeProperties::class;
  protected $propertiesDataType = '';

  /**
   * Output only. The name of the resource.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The properties of the DbSystem initial storage size summary.
   *
   * @param DbSystemInitialStorageSizeProperties $properties
   */
  public function setProperties(DbSystemInitialStorageSizeProperties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return DbSystemInitialStorageSizeProperties
   */
  public function getProperties()
  {
    return $this->properties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DbSystemInitialStorageSize::class, 'Google_Service_OracleDatabase_DbSystemInitialStorageSize');
