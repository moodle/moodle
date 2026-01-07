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

class InterceptEndpointGroup extends \Google\Collection
{
  /**
   * State not set (this is not a valid state).
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The endpoint group is ready and in sync with the target deployment group.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The deployment group backing this endpoint group has been force-deleted.
   * This endpoint group cannot be used and interception is effectively
   * disabled.
   */
  public const STATE_CLOSED = 'CLOSED';
  /**
   * The endpoint group is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The endpoint group is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The endpoint group is out of sync with the backing deployment group. In
   * most cases, this is a result of a transient issue within the system (e.g.
   * an inaccessible location) and the system is expected to recover
   * automatically. See the associations field for details per network and
   * location.
   */
  public const STATE_OUT_OF_SYNC = 'OUT_OF_SYNC';
  /**
   * An attempt to delete the endpoint group has failed. This is a terminal
   * state and the endpoint group is not expected to recover. The only permitted
   * operation is to retry deleting the endpoint group.
   */
  public const STATE_DELETE_FAILED = 'DELETE_FAILED';
  protected $collection_key = 'associations';
  protected $associationsType = InterceptEndpointGroupAssociationDetails::class;
  protected $associationsDataType = 'array';
  protected $connectedDeploymentGroupType = InterceptEndpointGroupConnectedDeploymentGroup::class;
  protected $connectedDeploymentGroupDataType = '';
  /**
   * Output only. The timestamp when the resource was created. See
   * https://google.aip.dev/148#timestamps.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. User-provided description of the endpoint group. Used as
   * additional context for the endpoint group.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Immutable. The deployment group that this endpoint group is
   * connected to, for example:
   * `projects/123456789/locations/global/interceptDeploymentGroups/my-dg`. See
   * https://google.aip.dev/124.
   *
   * @var string
   */
  public $interceptDeploymentGroup;
  /**
   * Optional. Labels are key/value pairs that help to organize and filter
   * resources.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Immutable. Identifier. The resource name of this endpoint group, for
   * example: `projects/123456789/locations/global/interceptEndpointGroups/my-
   * eg`. See https://google.aip.dev/122 for more details.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The current state of the resource does not match the user's
   * intended state, and the system is working to reconcile them. This is part
   * of the normal operation (e.g. adding a new association to the group). See
   * https://google.aip.dev/128.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. The current state of the endpoint group. See
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
   * Output only. List of associations to this endpoint group.
   *
   * @param InterceptEndpointGroupAssociationDetails[] $associations
   */
  public function setAssociations($associations)
  {
    $this->associations = $associations;
  }
  /**
   * @return InterceptEndpointGroupAssociationDetails[]
   */
  public function getAssociations()
  {
    return $this->associations;
  }
  /**
   * Output only. Details about the connected deployment group to this endpoint
   * group.
   *
   * @param InterceptEndpointGroupConnectedDeploymentGroup $connectedDeploymentGroup
   */
  public function setConnectedDeploymentGroup(InterceptEndpointGroupConnectedDeploymentGroup $connectedDeploymentGroup)
  {
    $this->connectedDeploymentGroup = $connectedDeploymentGroup;
  }
  /**
   * @return InterceptEndpointGroupConnectedDeploymentGroup
   */
  public function getConnectedDeploymentGroup()
  {
    return $this->connectedDeploymentGroup;
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
   * Optional. User-provided description of the endpoint group. Used as
   * additional context for the endpoint group.
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
   * Required. Immutable. The deployment group that this endpoint group is
   * connected to, for example:
   * `projects/123456789/locations/global/interceptDeploymentGroups/my-dg`. See
   * https://google.aip.dev/124.
   *
   * @param string $interceptDeploymentGroup
   */
  public function setInterceptDeploymentGroup($interceptDeploymentGroup)
  {
    $this->interceptDeploymentGroup = $interceptDeploymentGroup;
  }
  /**
   * @return string
   */
  public function getInterceptDeploymentGroup()
  {
    return $this->interceptDeploymentGroup;
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
   * Immutable. Identifier. The resource name of this endpoint group, for
   * example: `projects/123456789/locations/global/interceptEndpointGroups/my-
   * eg`. See https://google.aip.dev/122 for more details.
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
   * Output only. The current state of the resource does not match the user's
   * intended state, and the system is working to reconcile them. This is part
   * of the normal operation (e.g. adding a new association to the group). See
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
   * Output only. The current state of the endpoint group. See
   * https://google.aip.dev/216.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CLOSED, CREATING, DELETING,
   * OUT_OF_SYNC, DELETE_FAILED
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
class_alias(InterceptEndpointGroup::class, 'Google_Service_NetworkSecurity_InterceptEndpointGroup');
