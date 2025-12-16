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

class PostgreSqlDatabaseDeployment extends \Google\Collection
{
  protected $collection_key = 'settings';
  protected $propertiesType = PostgreSqlProperty::class;
  protected $propertiesDataType = 'array';
  protected $settingsType = PostgreSqlSetting::class;
  protected $settingsDataType = 'array';

  /**
   * Optional. List of PostgreSql properties.
   *
   * @param PostgreSqlProperty[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return PostgreSqlProperty[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Optional. List of PostgreSql settings.
   *
   * @param PostgreSqlSetting[] $settings
   */
  public function setSettings($settings)
  {
    $this->settings = $settings;
  }
  /**
   * @return PostgreSqlSetting[]
   */
  public function getSettings()
  {
    return $this->settings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PostgreSqlDatabaseDeployment::class, 'Google_Service_MigrationCenterAPI_PostgreSqlDatabaseDeployment');
