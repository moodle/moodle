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

namespace Google\Service\SmartDeviceManagement;

class GoogleHomeEnterpriseSdmV1Device extends \Google\Collection
{
  protected $collection_key = 'parentRelations';
  /**
   * Required. The resource name of the device. For example:
   * "enterprises/XYZ/devices/123".
   *
   * @var string
   */
  public $name;
  protected $parentRelationsType = GoogleHomeEnterpriseSdmV1ParentRelation::class;
  protected $parentRelationsDataType = 'array';
  /**
   * Output only. Device traits.
   *
   * @var array[]
   */
  public $traits;
  /**
   * Output only. Type of the device for general display purposes. For example:
   * "THERMOSTAT". The device type should not be used to deduce or infer
   * functionality of the actual device it is assigned to. Instead, use the
   * returned traits for the device.
   *
   * @var string
   */
  public $type;

  /**
   * Required. The resource name of the device. For example:
   * "enterprises/XYZ/devices/123".
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
   * Assignee details of the device.
   *
   * @param GoogleHomeEnterpriseSdmV1ParentRelation[] $parentRelations
   */
  public function setParentRelations($parentRelations)
  {
    $this->parentRelations = $parentRelations;
  }
  /**
   * @return GoogleHomeEnterpriseSdmV1ParentRelation[]
   */
  public function getParentRelations()
  {
    return $this->parentRelations;
  }
  /**
   * Output only. Device traits.
   *
   * @param array[] $traits
   */
  public function setTraits($traits)
  {
    $this->traits = $traits;
  }
  /**
   * @return array[]
   */
  public function getTraits()
  {
    return $this->traits;
  }
  /**
   * Output only. Type of the device for general display purposes. For example:
   * "THERMOSTAT". The device type should not be used to deduce or infer
   * functionality of the actual device it is assigned to. Instead, use the
   * returned traits for the device.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleHomeEnterpriseSdmV1Device::class, 'Google_Service_SmartDeviceManagement_GoogleHomeEnterpriseSdmV1Device');
