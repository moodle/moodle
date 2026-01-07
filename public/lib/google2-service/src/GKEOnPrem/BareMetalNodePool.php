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

namespace Google\Service\GKEOnPrem;

class BareMetalNodePool extends \Google\Model
{
  /**
   * Not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The PROVISIONING state indicates the bare metal node pool is being created.
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * The RUNNING state indicates the bare metal node pool has been created and
   * is fully usable.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The RECONCILING state indicates that the bare metal node pool is being
   * updated. It remains available, but potentially with degraded performance.
   */
  public const STATE_RECONCILING = 'RECONCILING';
  /**
   * The STOPPING state indicates the bare metal node pool is being deleted.
   */
  public const STATE_STOPPING = 'STOPPING';
  /**
   * The ERROR state indicates the bare metal node pool is in a broken
   * unrecoverable state.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * The DEGRADED state indicates the bare metal node pool requires user action
   * to restore full functionality.
   */
  public const STATE_DEGRADED = 'DEGRADED';
  /**
   * Annotations on the bare metal node pool. This field has the same
   * restrictions as Kubernetes annotations. The total size of all keys and
   * values combined is limited to 256k. Key can have 2 segments: prefix
   * (optional) and name (required), separated by a slash (/). Prefix must be a
   * DNS subdomain. Name must be 63 characters or less, begin and end with
   * alphanumerics, with dashes (-), underscores (_), dots (.), and
   * alphanumerics between.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. The time at which this bare metal node pool was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time at which this bare metal node pool was deleted. If
   * the resource is not deleted, this must be empty
   *
   * @var string
   */
  public $deleteTime;
  /**
   * The display name for the bare metal node pool.
   *
   * @var string
   */
  public $displayName;
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding. Allows clients to perform consistent
   * read-modify-writes through optimistic concurrency control.
   *
   * @var string
   */
  public $etag;
  /**
   * Immutable. The bare metal node pool resource name.
   *
   * @var string
   */
  public $name;
  protected $nodePoolConfigType = BareMetalNodePoolConfig::class;
  protected $nodePoolConfigDataType = '';
  /**
   * Output only. If set, there are currently changes in flight to the bare
   * metal node pool.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. The current state of the bare metal node pool.
   *
   * @var string
   */
  public $state;
  protected $statusType = ResourceStatus::class;
  protected $statusDataType = '';
  /**
   * Output only. The unique identifier of the bare metal node pool.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time at which this bare metal node pool was last updated.
   *
   * @var string
   */
  public $updateTime;
  protected $upgradePolicyType = BareMetalNodePoolUpgradePolicy::class;
  protected $upgradePolicyDataType = '';

  /**
   * Annotations on the bare metal node pool. This field has the same
   * restrictions as Kubernetes annotations. The total size of all keys and
   * values combined is limited to 256k. Key can have 2 segments: prefix
   * (optional) and name (required), separated by a slash (/). Prefix must be a
   * DNS subdomain. Name must be 63 characters or less, begin and end with
   * alphanumerics, with dashes (-), underscores (_), dots (.), and
   * alphanumerics between.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. The time at which this bare metal node pool was created.
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
   * Output only. The time at which this bare metal node pool was deleted. If
   * the resource is not deleted, this must be empty
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
   * The display name for the bare metal node pool.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding. Allows clients to perform consistent
   * read-modify-writes through optimistic concurrency control.
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
   * Immutable. The bare metal node pool resource name.
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
   * Required. Node pool configuration.
   *
   * @param BareMetalNodePoolConfig $nodePoolConfig
   */
  public function setNodePoolConfig(BareMetalNodePoolConfig $nodePoolConfig)
  {
    $this->nodePoolConfig = $nodePoolConfig;
  }
  /**
   * @return BareMetalNodePoolConfig
   */
  public function getNodePoolConfig()
  {
    return $this->nodePoolConfig;
  }
  /**
   * Output only. If set, there are currently changes in flight to the bare
   * metal node pool.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Output only. The current state of the bare metal node pool.
   *
   * Accepted values: STATE_UNSPECIFIED, PROVISIONING, RUNNING, RECONCILING,
   * STOPPING, ERROR, DEGRADED
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
   * Output only. ResourceStatus representing the detailed node pool status.
   *
   * @param ResourceStatus $status
   */
  public function setStatus(ResourceStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return ResourceStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. The unique identifier of the bare metal node pool.
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
   * Output only. The time at which this bare metal node pool was last updated.
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
   * The worker node pool upgrade policy.
   *
   * @param BareMetalNodePoolUpgradePolicy $upgradePolicy
   */
  public function setUpgradePolicy(BareMetalNodePoolUpgradePolicy $upgradePolicy)
  {
    $this->upgradePolicy = $upgradePolicy;
  }
  /**
   * @return BareMetalNodePoolUpgradePolicy
   */
  public function getUpgradePolicy()
  {
    return $this->upgradePolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalNodePool::class, 'Google_Service_GKEOnPrem_BareMetalNodePool');
