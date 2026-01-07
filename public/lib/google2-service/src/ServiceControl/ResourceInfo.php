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

namespace Google\Service\ServiceControl;

class ResourceInfo extends \Google\Model
{
  /**
   * Optional. The identifier of the container of this resource. For Google
   * Cloud APIs, the resource container must be one of the following formats: -
   * `projects/` - `folders/` - `organizations/` Required for the policy
   * enforcement on the container level (e.g. VPCSC, Location Policy check, Org
   * Policy check).
   *
   * @var string
   */
  public $container;
  /**
   * Optional. The location of the resource, it must be a valid zone, region or
   * multiregion, for example: "europe-west4", "northamerica-northeast1-a".
   * Required for location policy check.
   *
   * @var string
   */
  public $location;
  /**
   * The name of the resource referenced in the request.
   *
   * @var string
   */
  public $name;
  /**
   * The resource permission needed for this request. The format must be
   * "{service}/{plural}.{verb}".
   *
   * @var string
   */
  public $permission;
  /**
   * The resource type in the format of "{service}/{kind}".
   *
   * @var string
   */
  public $type;

  /**
   * Optional. The identifier of the container of this resource. For Google
   * Cloud APIs, the resource container must be one of the following formats: -
   * `projects/` - `folders/` - `organizations/` Required for the policy
   * enforcement on the container level (e.g. VPCSC, Location Policy check, Org
   * Policy check).
   *
   * @param string $container
   */
  public function setContainer($container)
  {
    $this->container = $container;
  }
  /**
   * @return string
   */
  public function getContainer()
  {
    return $this->container;
  }
  /**
   * Optional. The location of the resource, it must be a valid zone, region or
   * multiregion, for example: "europe-west4", "northamerica-northeast1-a".
   * Required for location policy check.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The name of the resource referenced in the request.
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
   * The resource permission needed for this request. The format must be
   * "{service}/{plural}.{verb}".
   *
   * @param string $permission
   */
  public function setPermission($permission)
  {
    $this->permission = $permission;
  }
  /**
   * @return string
   */
  public function getPermission()
  {
    return $this->permission;
  }
  /**
   * The resource type in the format of "{service}/{kind}".
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
class_alias(ResourceInfo::class, 'Google_Service_ServiceControl_ResourceInfo');
