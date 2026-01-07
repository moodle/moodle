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

class PluggableDatabase extends \Google\Model
{
  /**
   * Output only. The date and time that the PluggableDatabase was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Identifier. The name of the PluggableDatabase resource in the following
   * format: projects/{project}/locations/{region}/pluggableDatabases/{pluggable
   * _database}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. HTTPS link to OCI resources exposed to Customer via UI
   * Interface.
   *
   * @var string
   */
  public $ociUrl;
  protected $propertiesType = PluggableDatabaseProperties::class;
  protected $propertiesDataType = '';

  /**
   * Output only. The date and time that the PluggableDatabase was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Identifier. The name of the PluggableDatabase resource in the following
   * format: projects/{project}/locations/{region}/pluggableDatabases/{pluggable
   * _database}
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
   * Output only. HTTPS link to OCI resources exposed to Customer via UI
   * Interface.
   *
   * @param string $ociUrl
   */
  public function setOciUrl($ociUrl)
  {
    $this->ociUrl = $ociUrl;
  }
  /**
   * @return string
   */
  public function getOciUrl()
  {
    return $this->ociUrl;
  }
  /**
   * Optional. The properties of the PluggableDatabase.
   *
   * @param PluggableDatabaseProperties $properties
   */
  public function setProperties(PluggableDatabaseProperties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return PluggableDatabaseProperties
   */
  public function getProperties()
  {
    return $this->properties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PluggableDatabase::class, 'Google_Service_OracleDatabase_PluggableDatabase');
