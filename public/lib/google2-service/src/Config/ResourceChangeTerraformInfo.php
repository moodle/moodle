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

namespace Google\Service\Config;

class ResourceChangeTerraformInfo extends \Google\Collection
{
  protected $collection_key = 'actions';
  /**
   * Output only. TF resource actions.
   *
   * @var string[]
   */
  public $actions;
  /**
   * Output only. TF resource address that uniquely identifies the resource.
   *
   * @var string
   */
  public $address;
  /**
   * Output only. TF resource provider.
   *
   * @var string
   */
  public $provider;
  /**
   * Output only. TF resource name.
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. TF resource type.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. TF resource actions.
   *
   * @param string[] $actions
   */
  public function setActions($actions)
  {
    $this->actions = $actions;
  }
  /**
   * @return string[]
   */
  public function getActions()
  {
    return $this->actions;
  }
  /**
   * Output only. TF resource address that uniquely identifies the resource.
   *
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }
  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * Output only. TF resource provider.
   *
   * @param string $provider
   */
  public function setProvider($provider)
  {
    $this->provider = $provider;
  }
  /**
   * @return string
   */
  public function getProvider()
  {
    return $this->provider;
  }
  /**
   * Output only. TF resource name.
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Output only. TF resource type.
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
class_alias(ResourceChangeTerraformInfo::class, 'Google_Service_Config_ResourceChangeTerraformInfo');
