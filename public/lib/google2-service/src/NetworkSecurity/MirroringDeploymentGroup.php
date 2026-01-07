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

namespace Google\Service\NetworkSecurity;

class MirroringDeploymentGroup extends \Google\Collection
{
  /**
   * State not set (this is not a valid state).
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The deployment group is ready.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The deployment group is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The deployment group is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The deployment group is being wiped out (project deleted).
   */
  public const STATE_CLOSED = 'CLOSED';
  protected $collection_key = 'nestedDeployments';
  protected $connectedEndpointGroupsType = MirroringDeploymentGroupConnectedEndpointGroup::class;
  protected $connectedEndpointGroupsDataType = 'array';
  /**
   * Output only. The timestamp when the resource was created. See
   * https://google.aip.dev/148#timestamps.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. User-provided description of the deployment group. Used as
   * additional context for the deployment group.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Labels are key/value pairs that help to organize and filter
   * resources.
   *
   * @var string[]
   */
  public $labels;
  protected $locationsType = MirroringLocation::class;
  protected $locationsDataType = 'array';
  /**
   * Immutable. Identifier. The resource name of this deployment group, for
   * example: `projects/123456789/locations/global/mirroringDeploymentGroups/my-
   * dg`. See https://google.aip.dev/122 for more details.
   *
   * @var string
   */
  public $name;
  protected $nestedDeploymentsType = MirroringDeploymentGroupDeployment::class;
  protected $nestedDeploymentsDataType = 'array';
  /**
   * Required. Immutable. The network that will be used for all child
   * deployments, for example: `projects/{project}/global/networks/{network}`.
   * See https://google.aip.dev/124.
   *
   * @var string
   */
  public $network;
  /**
   * Output only. The current state of the resource does not match the user's
   * intended state, and the system is working to reconcile them. This is part
   * of the normal operation (e.g. adding a new deployment to the group) See
   * https://google.aip.dev/128.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. The current state of the deployment group. See
   * https://google.aip.dev/216.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The timestamp when the resource was most recently updated. See
   * https://google.aip.dev/148#timestamps.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The list of endpoint groups that are connected to this
   * resource.
   *
   * @param MirroringDeploymentGroupConnectedEndpointGroup[] $connectedEndpointGroups
   */
  public function setConnectedEndpointGroups($connectedEndpointGroups)
  {
    $this->connectedEndpointGroups = $connectedEndpointGroups;
  }
  /**
   * @return MirroringDeploymentGroupConnectedEndpointGroup[]
   */
  public function getConnectedEndpointGroups()
  {
    return $this->connectedEndpointGroups;
  }
  /**
   * Output only. The timestamp when the resource was created. See
   * https://google.aip.dev/148#timestamps.
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
   * Optional. User-provided description of the deployment group. Used as
   * additional context for the deployment group.
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
   * Optional. Labels are key/value pairs that help to organize and filter
   * resources.
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
   * Output only. The list of locations where the deployment group is present.
   *
   * @param MirroringLocation[] $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @return MirroringLocation[]
   */
  public function getLocations()
  {
    return $this->locations;
  }
  /**
   * Immutable. Identifier. The resource name of this deployment group, for
   * example: `projects/123456789/locations/global/mirroringDeploymentGroups/my-
   * dg`. See https://google.aip.dev/122 for more details.
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
   * Output only. The list of Mirroring Deployments that belong to this group.
   *
   * @deprecated
   * @param MirroringDeploymentGroupDeployment[] $nestedDeployments
   */
  public function setNestedDeployments($nestedDeployments)
  {
    $this->nestedDeployments = $nestedDeployments;
  }
  /**
   * @deprecated
   * @return MirroringDeploymentGroupDeployment[]
   */
  public function getNestedDeployments()
  {
    return $this->nestedDeployments;
  }
  /**
   * Required. Immutable. The network that will be used for all child
   * deployments, for example: `projects/{project}/global/networks/{network}`.
   * See https://google.aip.dev/124.
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
   * Output only. The current state of the resource does not match the user's
   * intended state, and the system is working to reconcile them. This is part
   * of the normal operation (e.g. adding a new deployment to the group) See
   * https://google.aip.dev/128.
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
   * Output only. The current state of the deployment group. See
   * https://google.aip.dev/216.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CREATING, DELETING, CLOSED
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
   * Output only. The timestamp when the resource was most recently updated. See
   * https://google.aip.dev/148#timestamps.
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
class_alias(MirroringDeploymentGroup::class, 'Google_Service_NetworkSecurity_MirroringDeploymentGroup');
