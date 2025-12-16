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

class VmwareNodePool extends \Google\Model
{
  /**
   * Not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The PROVISIONING state indicates the node pool is being created.
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * The RUNNING state indicates the node pool has been created and is fully
   * usable.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The RECONCILING state indicates that the node pool is being updated. It
   * remains available, but potentially with degraded performance.
   */
  public const STATE_RECONCILING = 'RECONCILING';
  /**
   * The STOPPING state indicates the cluster is being deleted
   */
  public const STATE_STOPPING = 'STOPPING';
  /**
   * The ERROR state indicates the node pool is in a broken unrecoverable state.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * The DEGRADED state indicates the node pool requires user action to restore
   * full functionality.
   */
  public const STATE_DEGRADED = 'DEGRADED';
  /**
   * Annotations on the node pool. This field has the same restrictions as
   * Kubernetes annotations. The total size of all keys and values combined is
   * limited to 256k. Key can have 2 segments: prefix (optional) and name
   * (required), separated by a slash (/). Prefix must be a DNS subdomain. Name
   * must be 63 characters or less, begin and end with alphanumerics, with
   * dashes (-), underscores (_), dots (.), and alphanumerics between.
   *
   * @var string[]
   */
  public $annotations;
  protected $configType = VmwareNodeConfig::class;
  protected $configDataType = '';
  /**
   * Output only. The time at which this node pool was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time at which this node pool was deleted. If the resource
   * is not deleted, this must be empty
   *
   * @var string
   */
  public $deleteTime;
  /**
   * The display name for the node pool.
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
   * Immutable. The resource name of this node pool.
   *
   * @var string
   */
  public $name;
  protected $nodePoolAutoscalingType = VmwareNodePoolAutoscalingConfig::class;
  protected $nodePoolAutoscalingDataType = '';
  /**
   * Anthos version for the node pool. Defaults to the user cluster version.
   *
   * @var string
   */
  public $onPremVersion;
  /**
   * Output only. If set, there are currently changes in flight to the node
   * pool.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. The current state of the node pool.
   *
   * @var string
   */
  public $state;
  protected $statusType = ResourceStatus::class;
  protected $statusDataType = '';
  /**
   * Output only. The unique identifier of the node pool.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time at which this node pool was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Annotations on the node pool. This field has the same restrictions as
   * Kubernetes annotations. The total size of all keys and values combined is
   * limited to 256k. Key can have 2 segments: prefix (optional) and name
   * (required), separated by a slash (/). Prefix must be a DNS subdomain. Name
   * must be 63 characters or less, begin and end with alphanumerics, with
   * dashes (-), underscores (_), dots (.), and alphanumerics between.
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
   * Required. The node configuration of the node pool.
   *
   * @param VmwareNodeConfig $config
   */
  public function setConfig(VmwareNodeConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return VmwareNodeConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Output only. The time at which this node pool was created.
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
   * Output only. The time at which this node pool was deleted. If the resource
   * is not deleted, this must be empty
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
   * The display name for the node pool.
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
   * Immutable. The resource name of this node pool.
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
   * Node pool autoscaling config for the node pool.
   *
   * @param VmwareNodePoolAutoscalingConfig $nodePoolAutoscaling
   */
  public function setNodePoolAutoscaling(VmwareNodePoolAutoscalingConfig $nodePoolAutoscaling)
  {
    $this->nodePoolAutoscaling = $nodePoolAutoscaling;
  }
  /**
   * @return VmwareNodePoolAutoscalingConfig
   */
  public function getNodePoolAutoscaling()
  {
    return $this->nodePoolAutoscaling;
  }
  /**
   * Anthos version for the node pool. Defaults to the user cluster version.
   *
   * @param string $onPremVersion
   */
  public function setOnPremVersion($onPremVersion)
  {
    $this->onPremVersion = $onPremVersion;
  }
  /**
   * @return string
   */
  public function getOnPremVersion()
  {
    return $this->onPremVersion;
  }
  /**
   * Output only. If set, there are currently changes in flight to the node
   * pool.
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
   * Output only. The current state of the node pool.
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
   * Output only. ResourceStatus representing the detailed VMware node pool
   * state.
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
   * Output only. The unique identifier of the node pool.
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
   * Output only. The time at which this node pool was last updated.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareNodePool::class, 'Google_Service_GKEOnPrem_VmwareNodePool');
