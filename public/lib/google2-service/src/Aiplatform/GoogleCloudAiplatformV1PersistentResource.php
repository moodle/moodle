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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1PersistentResource extends \Google\Collection
{
  /**
   * Not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The PROVISIONING state indicates the persistent resources is being created.
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * The RUNNING state indicates the persistent resource is healthy and fully
   * usable.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The STOPPING state indicates the persistent resource is being deleted.
   */
  public const STATE_STOPPING = 'STOPPING';
  /**
   * The ERROR state indicates the persistent resource may be unusable. Details
   * can be found in the `error` field.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * The REBOOTING state indicates the persistent resource is being rebooted (PR
   * is not available right now but is expected to be ready again later).
   */
  public const STATE_REBOOTING = 'REBOOTING';
  /**
   * The UPDATING state indicates the persistent resource is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  protected $collection_key = 'resourcePools';
  /**
   * Output only. Time when the PersistentResource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The display name of the PersistentResource. The name can be up to
   * 128 characters long and can consist of any UTF-8 characters.
   *
   * @var string
   */
  public $displayName;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  /**
   * Optional. The labels with user-defined metadata to organize
   * PersistentResource. Label keys and values can be no longer than 64
   * characters (Unicode codepoints), can only contain lowercase letters,
   * numeric characters, underscores and dashes. International characters are
   * allowed. See https://goo.gl/xmQnxf for more information and examples of
   * labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Immutable. Resource name of a PersistentResource.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The full name of the Compute Engine
   * [network](/compute/docs/networks-and-firewalls#networks) to peered with
   * Vertex AI to host the persistent resources. For example,
   * `projects/12345/global/networks/myVPC`.
   * [Format](/compute/docs/reference/rest/v1/networks/insert) is of the form
   * `projects/{project}/global/networks/{network}`. Where {project} is a
   * project number, as in `12345`, and {network} is a network name. To specify
   * this field, you must have already [configured VPC Network Peering for
   * Vertex AI](https://cloud.google.com/vertex-ai/docs/general/vpc-peering). If
   * this field is left unspecified, the resources aren't peered with any
   * network.
   *
   * @var string
   */
  public $network;
  protected $pscInterfaceConfigType = GoogleCloudAiplatformV1PscInterfaceConfig::class;
  protected $pscInterfaceConfigDataType = '';
  /**
   * Optional. A list of names for the reserved IP ranges under the VPC network
   * that can be used for this persistent resource. If set, we will deploy the
   * persistent resource within the provided IP ranges. Otherwise, the
   * persistent resource is deployed to any IP ranges under the provided VPC
   * network. Example: ['vertex-ai-ip-range'].
   *
   * @var string[]
   */
  public $reservedIpRanges;
  protected $resourcePoolsType = GoogleCloudAiplatformV1ResourcePool::class;
  protected $resourcePoolsDataType = 'array';
  protected $resourceRuntimeType = GoogleCloudAiplatformV1ResourceRuntime::class;
  protected $resourceRuntimeDataType = '';
  protected $resourceRuntimeSpecType = GoogleCloudAiplatformV1ResourceRuntimeSpec::class;
  protected $resourceRuntimeSpecDataType = '';
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. Time when the PersistentResource for the first time entered
   * the `RUNNING` state.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The detailed state of a Study.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Time when the PersistentResource was most recently updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Time when the PersistentResource was created.
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
   * Optional. The display name of the PersistentResource. The name can be up to
   * 128 characters long and can consist of any UTF-8 characters.
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
   * Optional. Customer-managed encryption key spec for a PersistentResource. If
   * set, this PersistentResource and all sub-resources of this
   * PersistentResource will be secured by this key.
   *
   * @param GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec
   */
  public function setEncryptionSpec(GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec)
  {
    $this->encryptionSpec = $encryptionSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1EncryptionSpec
   */
  public function getEncryptionSpec()
  {
    return $this->encryptionSpec;
  }
  /**
   * Output only. Only populated when persistent resource's state is `STOPPING`
   * or `ERROR`.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Optional. The labels with user-defined metadata to organize
   * PersistentResource. Label keys and values can be no longer than 64
   * characters (Unicode codepoints), can only contain lowercase letters,
   * numeric characters, underscores and dashes. International characters are
   * allowed. See https://goo.gl/xmQnxf for more information and examples of
   * labels.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Immutable. Resource name of a PersistentResource.
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
   * Optional. The full name of the Compute Engine
   * [network](/compute/docs/networks-and-firewalls#networks) to peered with
   * Vertex AI to host the persistent resources. For example,
   * `projects/12345/global/networks/myVPC`.
   * [Format](/compute/docs/reference/rest/v1/networks/insert) is of the form
   * `projects/{project}/global/networks/{network}`. Where {project} is a
   * project number, as in `12345`, and {network} is a network name. To specify
   * this field, you must have already [configured VPC Network Peering for
   * Vertex AI](https://cloud.google.com/vertex-ai/docs/general/vpc-peering). If
   * this field is left unspecified, the resources aren't peered with any
   * network.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Optional. Configuration for PSC-I for PersistentResource.
   *
   * @param GoogleCloudAiplatformV1PscInterfaceConfig $pscInterfaceConfig
   */
  public function setPscInterfaceConfig(GoogleCloudAiplatformV1PscInterfaceConfig $pscInterfaceConfig)
  {
    $this->pscInterfaceConfig = $pscInterfaceConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1PscInterfaceConfig
   */
  public function getPscInterfaceConfig()
  {
    return $this->pscInterfaceConfig;
  }
  /**
   * Optional. A list of names for the reserved IP ranges under the VPC network
   * that can be used for this persistent resource. If set, we will deploy the
   * persistent resource within the provided IP ranges. Otherwise, the
   * persistent resource is deployed to any IP ranges under the provided VPC
   * network. Example: ['vertex-ai-ip-range'].
   *
   * @param string[] $reservedIpRanges
   */
  public function setReservedIpRanges($reservedIpRanges)
  {
    $this->reservedIpRanges = $reservedIpRanges;
  }
  /**
   * @return string[]
   */
  public function getReservedIpRanges()
  {
    return $this->reservedIpRanges;
  }
  /**
   * Required. The spec of the pools of different resources.
   *
   * @param GoogleCloudAiplatformV1ResourcePool[] $resourcePools
   */
  public function setResourcePools($resourcePools)
  {
    $this->resourcePools = $resourcePools;
  }
  /**
   * @return GoogleCloudAiplatformV1ResourcePool[]
   */
  public function getResourcePools()
  {
    return $this->resourcePools;
  }
  /**
   * Output only. Runtime information of the Persistent Resource.
   *
   * @param GoogleCloudAiplatformV1ResourceRuntime $resourceRuntime
   */
  public function setResourceRuntime(GoogleCloudAiplatformV1ResourceRuntime $resourceRuntime)
  {
    $this->resourceRuntime = $resourceRuntime;
  }
  /**
   * @return GoogleCloudAiplatformV1ResourceRuntime
   */
  public function getResourceRuntime()
  {
    return $this->resourceRuntime;
  }
  /**
   * Optional. Persistent Resource runtime spec. For example, used for Ray
   * cluster configuration.
   *
   * @param GoogleCloudAiplatformV1ResourceRuntimeSpec $resourceRuntimeSpec
   */
  public function setResourceRuntimeSpec(GoogleCloudAiplatformV1ResourceRuntimeSpec $resourceRuntimeSpec)
  {
    $this->resourceRuntimeSpec = $resourceRuntimeSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1ResourceRuntimeSpec
   */
  public function getResourceRuntimeSpec()
  {
    return $this->resourceRuntimeSpec;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. Time when the PersistentResource for the first time entered
   * the `RUNNING` state.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. The detailed state of a Study.
   *
   * Accepted values: STATE_UNSPECIFIED, PROVISIONING, RUNNING, STOPPING, ERROR,
   * REBOOTING, UPDATING
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
   * Output only. Time when the PersistentResource was most recently updated.
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
class_alias(GoogleCloudAiplatformV1PersistentResource::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PersistentResource');
