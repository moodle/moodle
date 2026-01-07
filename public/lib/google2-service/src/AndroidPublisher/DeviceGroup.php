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

namespace Google\Service\AndroidPublisher;

class DeviceGroup extends \Google\Collection
{
  protected $collection_key = 'deviceSelectors';
  protected $deviceSelectorsType = DeviceSelector::class;
  protected $deviceSelectorsDataType = 'array';
  /**
   * The name of the group.
   *
   * @var string
   */
  public $name;

  /**
   * Device selectors for this group. A device matching any of the selectors is
   * included in this group.
   *
   * @param DeviceSelector[] $deviceSelectors
   */
  public function setDeviceSelectors($deviceSelectors)
  {
    $this->deviceSelectors = $deviceSelectors;
  }
  /**
   * @return DeviceSelector[]
   */
  public function getDeviceSelectors()
  {
    return $this->deviceSelectors;
  }
  /**
   * The name of the group.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceGroup::class, 'Google_Service_AndroidPublisher_DeviceGroup');
