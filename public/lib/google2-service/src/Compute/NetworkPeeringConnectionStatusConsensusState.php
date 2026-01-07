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

namespace Google\Service\Compute;

class NetworkPeeringConnectionStatusConsensusState extends \Google\Model
{
  /**
   * Both network admins have agreed this consensus peering connection can be
   * deleted.
   */
  public const DELETE_STATUS_DELETE_ACKNOWLEDGED = 'DELETE_ACKNOWLEDGED';
  public const DELETE_STATUS_DELETE_STATUS_UNSPECIFIED = 'DELETE_STATUS_UNSPECIFIED';
  /**
   * Network admin has requested deletion of this peering connection.
   */
  public const DELETE_STATUS_LOCAL_DELETE_REQUESTED = 'LOCAL_DELETE_REQUESTED';
  /**
   * The peer network admin has requested deletion of this peering connection.
   */
  public const DELETE_STATUS_PEER_DELETE_REQUESTED = 'PEER_DELETE_REQUESTED';
  /**
   * No pending configuration update proposals to the  peering connection.
   */
  public const UPDATE_STATUS_IN_SYNC = 'IN_SYNC';
  /**
   * The peer network admin has made an updatePeering call. The change is
   * awaiting acknowledgment from this peering's network admin.
   */
  public const UPDATE_STATUS_PENDING_LOCAL_ACKNOWLEDMENT = 'PENDING_LOCAL_ACKNOWLEDMENT';
  /**
   * The local network admin has made an updatePeering call. The change is
   * awaiting acknowledgment from the peer network admin.
   */
  public const UPDATE_STATUS_PENDING_PEER_ACKNOWLEDGEMENT = 'PENDING_PEER_ACKNOWLEDGEMENT';
  public const UPDATE_STATUS_UPDATE_STATUS_UNSPECIFIED = 'UPDATE_STATUS_UNSPECIFIED';
  /**
   * The status of the delete request.
   *
   * @var string
   */
  public $deleteStatus;
  /**
   * The status of the update request.
   *
   * @var string
   */
  public $updateStatus;

  /**
   * The status of the delete request.
   *
   * Accepted values: DELETE_ACKNOWLEDGED, DELETE_STATUS_UNSPECIFIED,
   * LOCAL_DELETE_REQUESTED, PEER_DELETE_REQUESTED
   *
   * @param self::DELETE_STATUS_* $deleteStatus
   */
  public function setDeleteStatus($deleteStatus)
  {
    $this->deleteStatus = $deleteStatus;
  }
  /**
   * @return self::DELETE_STATUS_*
   */
  public function getDeleteStatus()
  {
    return $this->deleteStatus;
  }
  /**
   * The status of the update request.
   *
   * Accepted values: IN_SYNC, PENDING_LOCAL_ACKNOWLEDMENT,
   * PENDING_PEER_ACKNOWLEDGEMENT, UPDATE_STATUS_UNSPECIFIED
   *
   * @param self::UPDATE_STATUS_* $updateStatus
   */
  public function setUpdateStatus($updateStatus)
  {
    $this->updateStatus = $updateStatus;
  }
  /**
   * @return self::UPDATE_STATUS_*
   */
  public function getUpdateStatus()
  {
    return $this->updateStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkPeeringConnectionStatusConsensusState::class, 'Google_Service_Compute_NetworkPeeringConnectionStatusConsensusState');
