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

class MirroringEndpointGroupAssociationDetails extends \Google\Model
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
  /**
   * Output only. The connected association's resource name, for example:
   * `projects/123456789/locations/global/mirroringEndpointGroupAssociations/my-
   * ega`. See https://google.aip.dev/124.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The associated network, for example:
   * projects/123456789/global/networks/my-network. See
   * https://google.aip.dev/124.
   *
   * @var string
   */
  public $network;
  /**
   * Output only. Most recent known state of the association.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The connected association's resource name, for example:
   * `projects/123456789/locations/global/mirroringEndpointGroupAssociations/my-
   * ega`. See https://google.aip.dev/124.
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
   * Output only. The associated network, for example:
   * projects/123456789/global/networks/my-network. See
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
   * Output only. Most recent known state of the association.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MirroringEndpointGroupAssociationDetails::class, 'Google_Service_NetworkSecurity_MirroringEndpointGroupAssociationDetails');
