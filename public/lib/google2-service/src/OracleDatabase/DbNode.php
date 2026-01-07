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

class DbNode extends \Google\Model
{
  /**
   * Identifier. The name of the database node resource in the following format:
   * projects/{project}/locations/{location}/cloudVmClusters/{cloud_vm_cluster}/
   * dbNodes/{db_node}
   *
   * @var string
   */
  public $name;
  protected $propertiesType = DbNodeProperties::class;
  protected $propertiesDataType = '';

  /**
   * Identifier. The name of the database node resource in the following format:
   * projects/{project}/locations/{location}/cloudVmClusters/{cloud_vm_cluster}/
   * dbNodes/{db_node}
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
   * Optional. Various properties of the database node.
   *
   * @param DbNodeProperties $properties
   */
  public function setProperties(DbNodeProperties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return DbNodeProperties
   */
  public function getProperties()
  {
    return $this->properties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DbNode::class, 'Google_Service_OracleDatabase_DbNode');
