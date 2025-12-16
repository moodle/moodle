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

class MysqlDatabaseDeployment extends \Google\Collection
{
  protected $collection_key = 'variables';
  protected $pluginsType = MySqlPlugin::class;
  protected $pluginsDataType = 'array';
  protected $propertiesType = MySqlProperty::class;
  protected $propertiesDataType = 'array';
  /**
   * Optional. Number of resource groups.
   *
   * @var int
   */
  public $resourceGroupsCount;
  protected $variablesType = MySqlVariable::class;
  protected $variablesDataType = 'array';

  /**
   * Optional. List of MySql plugins.
   *
   * @param MySqlPlugin[] $plugins
   */
  public function setPlugins($plugins)
  {
    $this->plugins = $plugins;
  }
  /**
   * @return MySqlPlugin[]
   */
  public function getPlugins()
  {
    return $this->plugins;
  }
  /**
   * Optional. List of MySql properties.
   *
   * @param MySqlProperty[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return MySqlProperty[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Optional. Number of resource groups.
   *
   * @param int $resourceGroupsCount
   */
  public function setResourceGroupsCount($resourceGroupsCount)
  {
    $this->resourceGroupsCount = $resourceGroupsCount;
  }
  /**
   * @return int
   */
  public function getResourceGroupsCount()
  {
    return $this->resourceGroupsCount;
  }
  /**
   * Optional. List of MySql variables.
   *
   * @param MySqlVariable[] $variables
   */
  public function setVariables($variables)
  {
    $this->variables = $variables;
  }
  /**
   * @return MySqlVariable[]
   */
  public function getVariables()
  {
    return $this->variables;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MysqlDatabaseDeployment::class, 'Google_Service_MigrationCenterAPI_MysqlDatabaseDeployment');
