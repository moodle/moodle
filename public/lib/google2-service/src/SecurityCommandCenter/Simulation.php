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

namespace Google\Service\SecurityCommandCenter;

class Simulation extends \Google\Collection
{
  /**
   * The cloud provider is unspecified.
   */
  public const CLOUD_PROVIDER_CLOUD_PROVIDER_UNSPECIFIED = 'CLOUD_PROVIDER_UNSPECIFIED';
  /**
   * The cloud provider is Google Cloud.
   */
  public const CLOUD_PROVIDER_GOOGLE_CLOUD_PLATFORM = 'GOOGLE_CLOUD_PLATFORM';
  /**
   * The cloud provider is Amazon Web Services.
   */
  public const CLOUD_PROVIDER_AMAZON_WEB_SERVICES = 'AMAZON_WEB_SERVICES';
  /**
   * The cloud provider is Microsoft Azure.
   */
  public const CLOUD_PROVIDER_MICROSOFT_AZURE = 'MICROSOFT_AZURE';
  protected $collection_key = 'resourceValueConfigsMetadata';
  /**
   * Indicates which cloud provider was used in this simulation.
   *
   * @var string
   */
  public $cloudProvider;
  /**
   * Output only. Time simulation was created
   *
   * @var string
   */
  public $createTime;
  /**
   * Full resource name of the Simulation: `organizations/123/simulations/456`
   *
   * @var string
   */
  public $name;
  protected $resourceValueConfigsMetadataType = ResourceValueConfigMetadata::class;
  protected $resourceValueConfigsMetadataDataType = 'array';

  /**
   * Indicates which cloud provider was used in this simulation.
   *
   * Accepted values: CLOUD_PROVIDER_UNSPECIFIED, GOOGLE_CLOUD_PLATFORM,
   * AMAZON_WEB_SERVICES, MICROSOFT_AZURE
   *
   * @param self::CLOUD_PROVIDER_* $cloudProvider
   */
  public function setCloudProvider($cloudProvider)
  {
    $this->cloudProvider = $cloudProvider;
  }
  /**
   * @return self::CLOUD_PROVIDER_*
   */
  public function getCloudProvider()
  {
    return $this->cloudProvider;
  }
  /**
   * Output only. Time simulation was created
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Full resource name of the Simulation: `organizations/123/simulations/456`
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
   * Resource value configurations' metadata used in this simulation. Maximum of
   * 100.
   *
   * @param ResourceValueConfigMetadata[] $resourceValueConfigsMetadata
   */
  public function setResourceValueConfigsMetadata($resourceValueConfigsMetadata)
  {
    $this->resourceValueConfigsMetadata = $resourceValueConfigsMetadata;
  }
  /**
   * @return ResourceValueConfigMetadata[]
   */
  public function getResourceValueConfigsMetadata()
  {
    return $this->resourceValueConfigsMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Simulation::class, 'Google_Service_SecurityCommandCenter_Simulation');
