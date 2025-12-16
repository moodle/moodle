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

namespace Google\Service\NetAppFiles;

class HostGroup extends \Google\Collection
{
  /**
   * Unspecified OS Type
   */
  public const OS_TYPE_OS_TYPE_UNSPECIFIED = 'OS_TYPE_UNSPECIFIED';
  /**
   * OS Type is Linux
   */
  public const OS_TYPE_LINUX = 'LINUX';
  /**
   * OS Type is Windows
   */
  public const OS_TYPE_WINDOWS = 'WINDOWS';
  /**
   * OS Type is VMware ESXi
   */
  public const OS_TYPE_ESXI = 'ESXI';
  /**
   * Unspecified state for host group.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Host group is creating.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Host group is ready.
   */
  public const STATE_READY = 'READY';
  /**
   * Host group is updating.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Host group is deleting.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Host group is disabled.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * Unspecified type for host group.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * iSCSI initiator host group.
   */
  public const TYPE_ISCSI_INITIATOR = 'ISCSI_INITIATOR';
  protected $collection_key = 'hosts';
  /**
   * Output only. Create time of the host group.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the host group.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The list of hosts associated with the host group.
   *
   * @var string[]
   */
  public $hosts;
  /**
   * Optional. Labels of the host group.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the host group. Format: `projects/{project
   * _number}/locations/{location_id}/hostGroups/{host_group_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The OS type of the host group. It indicates the type of operating
   * system used by all of the hosts in the HostGroup. All hosts in a HostGroup
   * must be of the same OS type. This can be set only when creating a
   * HostGroup.
   *
   * @var string
   */
  public $osType;
  /**
   * Output only. State of the host group.
   *
   * @var string
   */
  public $state;
  /**
   * Required. Type of the host group.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. Create time of the host group.
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
   * Optional. Description of the host group.
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
   * Required. The list of hosts associated with the host group.
   *
   * @param string[] $hosts
   */
  public function setHosts($hosts)
  {
    $this->hosts = $hosts;
  }
  /**
   * @return string[]
   */
  public function getHosts()
  {
    return $this->hosts;
  }
  /**
   * Optional. Labels of the host group.
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
   * Identifier. The resource name of the host group. Format: `projects/{project
   * _number}/locations/{location_id}/hostGroups/{host_group_id}`.
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
   * Required. The OS type of the host group. It indicates the type of operating
   * system used by all of the hosts in the HostGroup. All hosts in a HostGroup
   * must be of the same OS type. This can be set only when creating a
   * HostGroup.
   *
   * Accepted values: OS_TYPE_UNSPECIFIED, LINUX, WINDOWS, ESXI
   *
   * @param self::OS_TYPE_* $osType
   */
  public function setOsType($osType)
  {
    $this->osType = $osType;
  }
  /**
   * @return self::OS_TYPE_*
   */
  public function getOsType()
  {
    return $this->osType;
  }
  /**
   * Output only. State of the host group.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, UPDATING, DELETING,
   * DISABLED
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
   * Required. Type of the host group.
   *
   * Accepted values: TYPE_UNSPECIFIED, ISCSI_INITIATOR
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HostGroup::class, 'Google_Service_NetAppFiles_HostGroup');
