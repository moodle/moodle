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

class PrivateCloud extends \Google\Model
{
  /**
   * The default value. This value should never be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The private cloud is ready.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The private cloud is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The private cloud is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The private cloud is in failed state.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The private cloud is scheduled for deletion. The deletion process can be
   * cancelled by using the corresponding undelete method.
   */
  public const STATE_DELETED = 'DELETED';
  /**
   * The private cloud is irreversibly deleted and is being removed from the
   * system.
   */
  public const STATE_PURGING = 'PURGING';
  /**
   * Standard private is a zonal resource, with 3+ nodes. Default type.
   */
  public const TYPE_STANDARD = 'STANDARD';
  /**
   * Time limited private cloud is a zonal resource, can have only 1 node and
   * has limited life span. Will be deleted after defined period of time, can be
   * converted into standard private cloud by expanding it up to 3 or more
   * nodes.
   */
  public const TYPE_TIME_LIMITED = 'TIME_LIMITED';
  /**
   * Stretched private cloud is a regional resource with redundancy, with a
   * minimum of 6 nodes, nodes count has to be even.
   */
  public const TYPE_STRETCHED = 'STRETCHED';
  /**
   * Output only. Creation time of this resource.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Time when the resource was scheduled for deletion.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * User-provided description for this private cloud.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Time when the resource will be irreversibly deleted.
   *
   * @var string
   */
  public $expireTime;
  protected $hcxType = Hcx::class;
  protected $hcxDataType = '';
  protected $managementClusterType = ManagementCluster::class;
  protected $managementClusterDataType = '';
  /**
   * Output only. Identifier. The resource name of this private cloud. Resource
   * names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-cloud`
   *
   * @var string
   */
  public $name;
  protected $networkConfigType = NetworkConfig::class;
  protected $networkConfigDataType = '';
  protected $nsxType = Nsx::class;
  protected $nsxDataType = '';
  /**
   * Output only. State of the resource. New values may be added to this enum
   * when appropriate.
   *
   * @var string
   */
  public $state;
  /**
   * Optional. Type of the private cloud. Defaults to STANDARD.
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
  protected $vcenterType = Vcenter::class;
  protected $vcenterDataType = '';

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
   * Output only. Time when the resource was scheduled for deletion.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * User-provided description for this private cloud.
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
   * Output only. Time when the resource will be irreversibly deleted.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Output only. HCX appliance.
   *
   * @param Hcx $hcx
   */
  public function setHcx(Hcx $hcx)
  {
    $this->hcx = $hcx;
  }
  /**
   * @return Hcx
   */
  public function getHcx()
  {
    return $this->hcx;
  }
  /**
   * Required. Input only. The management cluster for this private cloud. This
   * field is required during creation of the private cloud to provide details
   * for the default cluster. The following fields can't be changed after
   * private cloud creation: `ManagementCluster.clusterId`,
   * `ManagementCluster.nodeTypeId`.
   *
   * @param ManagementCluster $managementCluster
   */
  public function setManagementCluster(ManagementCluster $managementCluster)
  {
    $this->managementCluster = $managementCluster;
  }
  /**
   * @return ManagementCluster
   */
  public function getManagementCluster()
  {
    return $this->managementCluster;
  }
  /**
   * Output only. Identifier. The resource name of this private cloud. Resource
   * names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-cloud`
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
   * Required. Network configuration of the private cloud.
   *
   * @param NetworkConfig $networkConfig
   */
  public function setNetworkConfig(NetworkConfig $networkConfig)
  {
    $this->networkConfig = $networkConfig;
  }
  /**
   * @return NetworkConfig
   */
  public function getNetworkConfig()
  {
    return $this->networkConfig;
  }
  /**
   * Output only. NSX appliance.
   *
   * @param Nsx $nsx
   */
  public function setNsx(Nsx $nsx)
  {
    $this->nsx = $nsx;
  }
  /**
   * @return Nsx
   */
  public function getNsx()
  {
    return $this->nsx;
  }
  /**
   * Output only. State of the resource. New values may be added to this enum
   * when appropriate.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CREATING, UPDATING, FAILED,
   * DELETED, PURGING
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
   * Optional. Type of the private cloud. Defaults to STANDARD.
   *
   * Accepted values: STANDARD, TIME_LIMITED, STRETCHED
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
   * Output only. Vcenter appliance.
   *
   * @param Vcenter $vcenter
   */
  public function setVcenter(Vcenter $vcenter)
  {
    $this->vcenter = $vcenter;
  }
  /**
   * @return Vcenter
   */
  public function getVcenter()
  {
    return $this->vcenter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrivateCloud::class, 'Google_Service_VMwareEngine_PrivateCloud');
