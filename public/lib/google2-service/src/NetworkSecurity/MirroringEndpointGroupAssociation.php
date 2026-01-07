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

class MirroringEndpointGroupAssociation extends \Google\Collection
{
  /**
   * Not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The association is ready and in sync with the linked endpoint group.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The association is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The association is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The association is disabled due to a breaking change in another resource.
   */
  public const STATE_CLOSED = 'CLOSED';
  /**
   * The association is out of sync with the linked endpoint group. In most
   * cases, this is a result of a transient issue within the system (e.g. an
   * inaccessible location) and the system is expected to recover automatically.
   * Check the `locations_details` field for more details.
   */
  public const STATE_OUT_OF_SYNC = 'OUT_OF_SYNC';
  /**
   * An attempt to delete the association has failed. This is a terminal state
   * and the association is not expected to be usable as some of its resources
   * have been deleted. The only permitted operation is to retry deleting the
   * association.
   */
  public const STATE_DELETE_FAILED = 'DELETE_FAILED';
  protected $collection_key = 'locationsDetails';
  /**
   * Output only. The timestamp when the resource was created. See
   * https://google.aip.dev/148#timestamps.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Labels are key/value pairs that help to organize and filter
   * resources.
   *
   * @var string[]
   */
  public $labels;
  protected $locationsType = MirroringLocation::class;
  protected $locationsDataType = 'array';
  protected $locationsDetailsType = MirroringEndpointGroupAssociationLocationDetails::class;
  protected $locationsDetailsDataType = 'array';
  /**
   * Immutable. The endpoint group that this association is connected to, for
   * example: `projects/123456789/locations/global/mirroringEndpointGroups/my-
   * eg`. See https://google.aip.dev/124.
   *
   * @var string
   */
  public $mirroringEndpointGroup;
  /**
   * Immutable. Identifier. The resource name of this endpoint group
   * association, for example:
   * `projects/123456789/locations/global/mirroringEndpointGroupAssociations/my-
   * eg-association`. See https://google.aip.dev/122 for more details.
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. The VPC network that is associated. for example:
   * `projects/123456789/global/networks/my-network`. See
   * https://google.aip.dev/124.
   *
   * @var string
   */
  public $network;
  /**
   * Output only. The current state of the resource does not match the user's
   * intended state, and the system is working to reconcile them. This part of
   * the normal operation (e.g. adding a new location to the target deployment
   * group). See https://google.aip.dev/128.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. Current state of the endpoint group association.
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
   * Output only. The list of locations where the association is configured.
   * This information is retrieved from the linked endpoint group.
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
   * Output only. The list of locations where the association is present. This
   * information is retrieved from the linked endpoint group, and not configured
   * as part of the association itself.
   *
   * @deprecated
   * @param MirroringEndpointGroupAssociationLocationDetails[] $locationsDetails
   */
  public function setLocationsDetails($locationsDetails)
  {
    $this->locationsDetails = $locationsDetails;
  }
  /**
   * @deprecated
   * @return MirroringEndpointGroupAssociationLocationDetails[]
   */
  public function getLocationsDetails()
  {
    return $this->locationsDetails;
  }
  /**
   * Immutable. The endpoint group that this association is connected to, for
   * example: `projects/123456789/locations/global/mirroringEndpointGroups/my-
   * eg`. See https://google.aip.dev/124.
   *
   * @param string $mirroringEndpointGroup
   */
  public function setMirroringEndpointGroup($mirroringEndpointGroup)
  {
    $this->mirroringEndpointGroup = $mirroringEndpointGroup;
  }
  /**
   * @return string
   */
  public function getMirroringEndpointGroup()
  {
    return $this->mirroringEndpointGroup;
  }
  /**
   * Immutable. Identifier. The resource name of this endpoint group
   * association, for example:
   * `projects/123456789/locations/global/mirroringEndpointGroupAssociations/my-
   * eg-association`. See https://google.aip.dev/122 for more details.
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
   * Immutable. The VPC network that is associated. for example:
   * `projects/123456789/global/networks/my-network`. See
   * https://google.aip.dev/124.
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
   * intended state, and the system is working to reconcile them. This part of
   * the normal operation (e.g. adding a new location to the target deployment
   * group). See https://google.aip.dev/128.
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
   * Output only. Current state of the endpoint group association.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CREATING, DELETING, CLOSED,
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
class_alias(MirroringEndpointGroupAssociation::class, 'Google_Service_NetworkSecurity_MirroringEndpointGroupAssociation');
