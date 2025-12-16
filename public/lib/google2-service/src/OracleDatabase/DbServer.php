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

class DbServer extends \Google\Model
{
  /**
   * Optional. User friendly name for this resource.
   *
   * @var string
   */
  public $displayName;
  /**
   * Identifier. The name of the database server resource with the format: proje
   * cts/{project}/locations/{location}/cloudExadataInfrastructures/{cloud_exada
   * ta_infrastructure}/dbServers/{db_server}
   *
   * @var string
   */
  public $name;
  protected $propertiesType = DbServerProperties::class;
  protected $propertiesDataType = '';

  /**
   * Optional. User friendly name for this resource.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Identifier. The name of the database server resource with the format: proje
   * cts/{project}/locations/{location}/cloudExadataInfrastructures/{cloud_exada
   * ta_infrastructure}/dbServers/{db_server}
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
   * Optional. Various properties of the database server.
   *
   * @param DbServerProperties $properties
   */
  public function setProperties(DbServerProperties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return DbServerProperties
   */
  public function getProperties()
  {
    return $this->properties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DbServer::class, 'Google_Service_OracleDatabase_DbServer');
