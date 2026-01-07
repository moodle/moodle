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

namespace Google\Service\Storage;

class BucketIpFilter extends \Google\Collection
{
  protected $collection_key = 'vpcNetworkSources';
  /**
   * Whether to allow all service agents to access the bucket regardless of the
   * IP filter configuration.
   *
   * @var bool
   */
  public $allowAllServiceAgentAccess;
  /**
   * Whether to allow cross-org VPCs in the bucket's IP filter configuration.
   *
   * @var bool
   */
  public $allowCrossOrgVpcs;
  /**
   * The mode of the IP filter. Valid values are 'Enabled' and 'Disabled'.
   *
   * @var string
   */
  public $mode;
  protected $publicNetworkSourceType = BucketIpFilterPublicNetworkSource::class;
  protected $publicNetworkSourceDataType = '';
  protected $vpcNetworkSourcesType = BucketIpFilterVpcNetworkSources::class;
  protected $vpcNetworkSourcesDataType = 'array';

  /**
   * Whether to allow all service agents to access the bucket regardless of the
   * IP filter configuration.
   *
   * @param bool $allowAllServiceAgentAccess
   */
  public function setAllowAllServiceAgentAccess($allowAllServiceAgentAccess)
  {
    $this->allowAllServiceAgentAccess = $allowAllServiceAgentAccess;
  }
  /**
   * @return bool
   */
  public function getAllowAllServiceAgentAccess()
  {
    return $this->allowAllServiceAgentAccess;
  }
  /**
   * Whether to allow cross-org VPCs in the bucket's IP filter configuration.
   *
   * @param bool $allowCrossOrgVpcs
   */
  public function setAllowCrossOrgVpcs($allowCrossOrgVpcs)
  {
    $this->allowCrossOrgVpcs = $allowCrossOrgVpcs;
  }
  /**
   * @return bool
   */
  public function getAllowCrossOrgVpcs()
  {
    return $this->allowCrossOrgVpcs;
  }
  /**
   * The mode of the IP filter. Valid values are 'Enabled' and 'Disabled'.
   *
   * @param string $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return string
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * The public network source of the bucket's IP filter.
   *
   * @param BucketIpFilterPublicNetworkSource $publicNetworkSource
   */
  public function setPublicNetworkSource(BucketIpFilterPublicNetworkSource $publicNetworkSource)
  {
    $this->publicNetworkSource = $publicNetworkSource;
  }
  /**
   * @return BucketIpFilterPublicNetworkSource
   */
  public function getPublicNetworkSource()
  {
    return $this->publicNetworkSource;
  }
  /**
   * The list of [VPC network](https://cloud.google.com/vpc/docs/vpc) sources of
   * the bucket's IP filter.
   *
   * @param BucketIpFilterVpcNetworkSources[] $vpcNetworkSources
   */
  public function setVpcNetworkSources($vpcNetworkSources)
  {
    $this->vpcNetworkSources = $vpcNetworkSources;
  }
  /**
   * @return BucketIpFilterVpcNetworkSources[]
   */
  public function getVpcNetworkSources()
  {
    return $this->vpcNetworkSources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BucketIpFilter::class, 'Google_Service_Storage_BucketIpFilter');
