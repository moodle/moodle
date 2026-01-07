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

namespace Google\Service\Container;

class UpgradeAvailableEvent extends \Google\Model
{
  /**
   * Default value. This shouldn't be used.
   */
  public const RESOURCE_TYPE_UPGRADE_RESOURCE_TYPE_UNSPECIFIED = 'UPGRADE_RESOURCE_TYPE_UNSPECIFIED';
  /**
   * Master / control plane
   */
  public const RESOURCE_TYPE_MASTER = 'MASTER';
  /**
   * Node pool
   */
  public const RESOURCE_TYPE_NODE_POOL = 'NODE_POOL';
  protected $releaseChannelType = ReleaseChannel::class;
  protected $releaseChannelDataType = '';
  /**
   * Optional relative path to the resource. For example, the relative path of
   * the node pool.
   *
   * @var string
   */
  public $resource;
  /**
   * The resource type of the release version.
   *
   * @var string
   */
  public $resourceType;
  /**
   * The release version available for upgrade.
   *
   * @var string
   */
  public $version;

  /**
   * The release channel of the version. If empty, it means a non-channel
   * release.
   *
   * @param ReleaseChannel $releaseChannel
   */
  public function setReleaseChannel(ReleaseChannel $releaseChannel)
  {
    $this->releaseChannel = $releaseChannel;
  }
  /**
   * @return ReleaseChannel
   */
  public function getReleaseChannel()
  {
    return $this->releaseChannel;
  }
  /**
   * Optional relative path to the resource. For example, the relative path of
   * the node pool.
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * The resource type of the release version.
   *
   * Accepted values: UPGRADE_RESOURCE_TYPE_UNSPECIFIED, MASTER, NODE_POOL
   *
   * @param self::RESOURCE_TYPE_* $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return self::RESOURCE_TYPE_*
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
  /**
   * The release version available for upgrade.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpgradeAvailableEvent::class, 'Google_Service_Container_UpgradeAvailableEvent');
