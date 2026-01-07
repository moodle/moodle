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

class ServerConfig extends \Google\Collection
{
  protected $collection_key = 'validNodeVersions';
  protected $channelsType = ReleaseChannelConfig::class;
  protected $channelsDataType = 'array';
  /**
   * Version of Kubernetes the service deploys by default.
   *
   * @var string
   */
  public $defaultClusterVersion;
  /**
   * Default image type.
   *
   * @var string
   */
  public $defaultImageType;
  /**
   * List of valid image types.
   *
   * @var string[]
   */
  public $validImageTypes;
  /**
   * List of valid master versions, in descending order.
   *
   * @var string[]
   */
  public $validMasterVersions;
  /**
   * List of valid node upgrade target versions, in descending order.
   *
   * @var string[]
   */
  public $validNodeVersions;

  /**
   * List of release channel configurations.
   *
   * @param ReleaseChannelConfig[] $channels
   */
  public function setChannels($channels)
  {
    $this->channels = $channels;
  }
  /**
   * @return ReleaseChannelConfig[]
   */
  public function getChannels()
  {
    return $this->channels;
  }
  /**
   * Version of Kubernetes the service deploys by default.
   *
   * @param string $defaultClusterVersion
   */
  public function setDefaultClusterVersion($defaultClusterVersion)
  {
    $this->defaultClusterVersion = $defaultClusterVersion;
  }
  /**
   * @return string
   */
  public function getDefaultClusterVersion()
  {
    return $this->defaultClusterVersion;
  }
  /**
   * Default image type.
   *
   * @param string $defaultImageType
   */
  public function setDefaultImageType($defaultImageType)
  {
    $this->defaultImageType = $defaultImageType;
  }
  /**
   * @return string
   */
  public function getDefaultImageType()
  {
    return $this->defaultImageType;
  }
  /**
   * List of valid image types.
   *
   * @param string[] $validImageTypes
   */
  public function setValidImageTypes($validImageTypes)
  {
    $this->validImageTypes = $validImageTypes;
  }
  /**
   * @return string[]
   */
  public function getValidImageTypes()
  {
    return $this->validImageTypes;
  }
  /**
   * List of valid master versions, in descending order.
   *
   * @param string[] $validMasterVersions
   */
  public function setValidMasterVersions($validMasterVersions)
  {
    $this->validMasterVersions = $validMasterVersions;
  }
  /**
   * @return string[]
   */
  public function getValidMasterVersions()
  {
    return $this->validMasterVersions;
  }
  /**
   * List of valid node upgrade target versions, in descending order.
   *
   * @param string[] $validNodeVersions
   */
  public function setValidNodeVersions($validNodeVersions)
  {
    $this->validNodeVersions = $validNodeVersions;
  }
  /**
   * @return string[]
   */
  public function getValidNodeVersions()
  {
    return $this->validNodeVersions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServerConfig::class, 'Google_Service_Container_ServerConfig');
