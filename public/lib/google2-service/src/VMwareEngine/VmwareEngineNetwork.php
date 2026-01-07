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

namespace Google\Service\VMwareEngine;

class VmwareEngineNetwork extends \Google\Collection
{
  /**
   * The default value. This value is used if the state is omitted.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The VMware Engine network is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The VMware Engine network is ready.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The VMware Engine network is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The VMware Engine network is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The default value. This value should never be used.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Network type used by private clouds created in projects without a network
   * of type `STANDARD`. This network type is no longer used for new VMware
   * Engine private cloud deployments.
   */
  public const TYPE_LEGACY = 'LEGACY';
  /**
   * Standard network type used for private cloud connectivity.
   */
  public const TYPE_STANDARD = 'STANDARD';
  protected $collection_key = 'vpcNetworks';
  /**
   * Output only. Creation time of this resource.
   *
   * @var string
   */
  public $createTime;
  /**
   * User-provided description for this VMware Engine network.
   *
   * @var string
   */
  public $description;
  /**
   * Checksum that may be sent on update and delete requests to ensure that the
   * user-provided value is up to date before the server processes a request.
   * The server computes checksums based on the value of other fields in the
   * request.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. Identifier. The resource name of the VMware Engine network.
   * Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/global/vmwareEngineNetworks/my-network`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. State of the VMware Engine network.
   *
   * @var string
   */
  public $state;
  /**
   * Required. VMware Engine network type.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. System-generated unique identifier for the resource.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Last update time of this resource.
   *
   * @var string
   */
  public $updateTime;
  protected $vpcNetworksType = VpcNetwork::class;
  protected $vpcNetworksDataType = 'array';

  /**
   * Output only. Creation time of this resource.
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
   * User-provided description for this VMware Engine network.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Checksum that may be sent on update and delete requests to ensure that the
   * user-provided value is up to date before the server processes a request.
   * The server computes checksums based on the value of other fields in the
   * request.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. Identifier. The resource name of the VMware Engine network.
   * Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/global/vmwareEngineNetworks/my-network`
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
   * Output only. State of the VMware Engine network.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, UPDATING, DELETING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Required. VMware Engine network type.
   *
   * Accepted values: TYPE_UNSPECIFIED, LEGACY, STANDARD
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. System-generated unique identifier for the resource.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. Last update time of this resource.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Output only. VMware Engine service VPC networks that provide connectivity
   * from a private cloud to customer projects, the internet, and other Google
   * Cloud services.
   *
   * @param VpcNetwork[] $vpcNetworks
   */
  public function setVpcNetworks($vpcNetworks)
  {
    $this->vpcNetworks = $vpcNetworks;
  }
  /**
   * @return VpcNetwork[]
   */
  public function getVpcNetworks()
  {
    return $this->vpcNetworks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareEngineNetwork::class, 'Google_Service_VMwareEngine_VmwareEngineNetwork');
