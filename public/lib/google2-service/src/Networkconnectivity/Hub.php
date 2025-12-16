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

namespace Google\Service\Networkconnectivity;

class Hub extends \Google\Collection
{
  /**
   * Policy mode is unspecified. It defaults to PRESET with preset_topology =
   * MESH.
   */
  public const POLICY_MODE_POLICY_MODE_UNSPECIFIED = 'POLICY_MODE_UNSPECIFIED';
  /**
   * Hub uses one of the preset topologies.
   */
  public const POLICY_MODE_PRESET = 'PRESET';
  /**
   * Preset topology is unspecified. When policy_mode = PRESET, it defaults to
   * MESH.
   */
  public const PRESET_TOPOLOGY_PRESET_TOPOLOGY_UNSPECIFIED = 'PRESET_TOPOLOGY_UNSPECIFIED';
  /**
   * Mesh topology is implemented. Group `default` is automatically created. All
   * spokes in the hub are added to group `default`.
   */
  public const PRESET_TOPOLOGY_MESH = 'MESH';
  /**
   * Star topology is implemented. Two groups, `center` and `edge`, are
   * automatically created along with hub creation. Spokes have to join one of
   * the groups during creation.
   */
  public const PRESET_TOPOLOGY_STAR = 'STAR';
  /**
   * No state information available
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The resource's create operation is in progress.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The resource is active
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The resource's delete operation is in progress.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The resource's accept operation is in progress.
   */
  public const STATE_ACCEPTING = 'ACCEPTING';
  /**
   * The resource's reject operation is in progress.
   */
  public const STATE_REJECTING = 'REJECTING';
  /**
   * The resource's update operation is in progress.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The resource is inactive.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * The hub associated with this spoke resource has been deleted. This state
   * applies to spoke resources only.
   */
  public const STATE_OBSOLETE = 'OBSOLETE';
  /**
   * The resource is in an undefined state due to resource creation or deletion
   * failure. You can try to delete the resource later or contact support for
   * help.
   */
  public const STATE_FAILED = 'FAILED';
  protected $collection_key = 'routingVpcs';
  /**
   * Output only. The time the hub was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. An optional description of the hub.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Whether Private Service Connect connection propagation is enabled
   * for the hub. If true, Private Service Connect endpoints in VPC spokes
   * attached to the hub are made accessible to other VPC spokes attached to the
   * hub. The default value is false.
   *
   * @var bool
   */
  public $exportPsc;
  /**
   * Optional labels in key-value pair format. For more information about
   * labels, see [Requirements for labels](https://cloud.google.com/resource-
   * manager/docs/creating-managing-labels#requirements).
   *
   * @var string[]
   */
  public $labels;
  /**
   * Immutable. The name of the hub. Hub names must be unique. They use the
   * following form: `projects/{project_number}/locations/global/hubs/{hub_id}`
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The policy mode of this hub. This field can be either PRESET or
   * CUSTOM. If unspecified, the policy_mode defaults to PRESET.
   *
   * @var string
   */
  public $policyMode;
  /**
   * Optional. The topology implemented in this hub. Currently, this field is
   * only used when policy_mode = PRESET. The available preset topologies are
   * MESH and STAR. If preset_topology is unspecified and policy_mode = PRESET,
   * the preset_topology defaults to MESH. When policy_mode = CUSTOM, the
   * preset_topology is set to PRESET_TOPOLOGY_UNSPECIFIED.
   *
   * @var string
   */
  public $presetTopology;
  /**
   * Output only. The route tables that belong to this hub. They use the
   * following form: `projects/{project_number}/locations/global/hubs/{hub_id}/r
   * outeTables/{route_table_id}` This field is read-only. Network Connectivity
   * Center automatically populates it based on the route tables nested under
   * the hub.
   *
   * @var string[]
   */
  public $routeTables;
  protected $routingVpcsType = RoutingVPC::class;
  protected $routingVpcsDataType = 'array';
  protected $spokeSummaryType = SpokeSummary::class;
  protected $spokeSummaryDataType = '';
  /**
   * Output only. The current lifecycle state of this hub.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The Google-generated UUID for the hub. This value is unique
   * across all hub resources. If a hub is deleted and another with the same
   * name is created, the new hub is assigned a different unique_id.
   *
   * @var string
   */
  public $uniqueId;
  /**
   * Output only. The time the hub was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time the hub was created.
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
   * Optional. An optional description of the hub.
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
   * Optional. Whether Private Service Connect connection propagation is enabled
   * for the hub. If true, Private Service Connect endpoints in VPC spokes
   * attached to the hub are made accessible to other VPC spokes attached to the
   * hub. The default value is false.
   *
   * @param bool $exportPsc
   */
  public function setExportPsc($exportPsc)
  {
    $this->exportPsc = $exportPsc;
  }
  /**
   * @return bool
   */
  public function getExportPsc()
  {
    return $this->exportPsc;
  }
  /**
   * Optional labels in key-value pair format. For more information about
   * labels, see [Requirements for labels](https://cloud.google.com/resource-
   * manager/docs/creating-managing-labels#requirements).
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
   * Immutable. The name of the hub. Hub names must be unique. They use the
   * following form: `projects/{project_number}/locations/global/hubs/{hub_id}`
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
   * Optional. The policy mode of this hub. This field can be either PRESET or
   * CUSTOM. If unspecified, the policy_mode defaults to PRESET.
   *
   * Accepted values: POLICY_MODE_UNSPECIFIED, PRESET
   *
   * @param self::POLICY_MODE_* $policyMode
   */
  public function setPolicyMode($policyMode)
  {
    $this->policyMode = $policyMode;
  }
  /**
   * @return self::POLICY_MODE_*
   */
  public function getPolicyMode()
  {
    return $this->policyMode;
  }
  /**
   * Optional. The topology implemented in this hub. Currently, this field is
   * only used when policy_mode = PRESET. The available preset topologies are
   * MESH and STAR. If preset_topology is unspecified and policy_mode = PRESET,
   * the preset_topology defaults to MESH. When policy_mode = CUSTOM, the
   * preset_topology is set to PRESET_TOPOLOGY_UNSPECIFIED.
   *
   * Accepted values: PRESET_TOPOLOGY_UNSPECIFIED, MESH, STAR
   *
   * @param self::PRESET_TOPOLOGY_* $presetTopology
   */
  public function setPresetTopology($presetTopology)
  {
    $this->presetTopology = $presetTopology;
  }
  /**
   * @return self::PRESET_TOPOLOGY_*
   */
  public function getPresetTopology()
  {
    return $this->presetTopology;
  }
  /**
   * Output only. The route tables that belong to this hub. They use the
   * following form: `projects/{project_number}/locations/global/hubs/{hub_id}/r
   * outeTables/{route_table_id}` This field is read-only. Network Connectivity
   * Center automatically populates it based on the route tables nested under
   * the hub.
   *
   * @param string[] $routeTables
   */
  public function setRouteTables($routeTables)
  {
    $this->routeTables = $routeTables;
  }
  /**
   * @return string[]
   */
  public function getRouteTables()
  {
    return $this->routeTables;
  }
  /**
   * Output only. The VPC networks associated with this hub's spokes. This field
   * is read-only. Network Connectivity Center automatically populates it based
   * on the set of spokes attached to the hub.
   *
   * @param RoutingVPC[] $routingVpcs
   */
  public function setRoutingVpcs($routingVpcs)
  {
    $this->routingVpcs = $routingVpcs;
  }
  /**
   * @return RoutingVPC[]
   */
  public function getRoutingVpcs()
  {
    return $this->routingVpcs;
  }
  /**
   * Output only. A summary of the spokes associated with a hub. The summary
   * includes a count of spokes according to type and according to state. If any
   * spokes are inactive, the summary also lists the reasons they are inactive,
   * including a count for each reason.
   *
   * @param SpokeSummary $spokeSummary
   */
  public function setSpokeSummary(SpokeSummary $spokeSummary)
  {
    $this->spokeSummary = $spokeSummary;
  }
  /**
   * @return SpokeSummary
   */
  public function getSpokeSummary()
  {
    return $this->spokeSummary;
  }
  /**
   * Output only. The current lifecycle state of this hub.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, ACCEPTING,
   * REJECTING, UPDATING, INACTIVE, OBSOLETE, FAILED
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
   * Output only. The Google-generated UUID for the hub. This value is unique
   * across all hub resources. If a hub is deleted and another with the same
   * name is created, the new hub is assigned a different unique_id.
   *
   * @param string $uniqueId
   */
  public function setUniqueId($uniqueId)
  {
    $this->uniqueId = $uniqueId;
  }
  /**
   * @return string
   */
  public function getUniqueId()
  {
    return $this->uniqueId;
  }
  /**
   * Output only. The time the hub was last updated.
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
class_alias(Hub::class, 'Google_Service_Networkconnectivity_Hub');
