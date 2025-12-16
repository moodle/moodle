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

class EstablishPeeringRequest extends \Google\Collection
{
  protected $collection_key = 'peerIpAddresses';
  /**
   * Required. Name of the user's local source cluster to be peered with the
   * destination cluster.
   *
   * @var string
   */
  public $peerClusterName;
  /**
   * Optional. List of IPv4 ip addresses to be used for peering.
   *
   * @var string[]
   */
  public $peerIpAddresses;
  /**
   * Required. Name of the user's local source vserver svm to be peered with the
   * destination vserver svm.
   *
   * @var string
   */
  public $peerSvmName;
  /**
   * Required. Name of the user's local source volume to be peered with the
   * destination volume.
   *
   * @var string
   */
  public $peerVolumeName;

  /**
   * Required. Name of the user's local source cluster to be peered with the
   * destination cluster.
   *
   * @param string $peerClusterName
   */
  public function setPeerClusterName($peerClusterName)
  {
    $this->peerClusterName = $peerClusterName;
  }
  /**
   * @return string
   */
  public function getPeerClusterName()
  {
    return $this->peerClusterName;
  }
  /**
   * Optional. List of IPv4 ip addresses to be used for peering.
   *
   * @param string[] $peerIpAddresses
   */
  public function setPeerIpAddresses($peerIpAddresses)
  {
    $this->peerIpAddresses = $peerIpAddresses;
  }
  /**
   * @return string[]
   */
  public function getPeerIpAddresses()
  {
    return $this->peerIpAddresses;
  }
  /**
   * Required. Name of the user's local source vserver svm to be peered with the
   * destination vserver svm.
   *
   * @param string $peerSvmName
   */
  public function setPeerSvmName($peerSvmName)
  {
    $this->peerSvmName = $peerSvmName;
  }
  /**
   * @return string
   */
  public function getPeerSvmName()
  {
    return $this->peerSvmName;
  }
  /**
   * Required. Name of the user's local source volume to be peered with the
   * destination volume.
   *
   * @param string $peerVolumeName
   */
  public function setPeerVolumeName($peerVolumeName)
  {
    $this->peerVolumeName = $peerVolumeName;
  }
  /**
   * @return string
   */
  public function getPeerVolumeName()
  {
    return $this->peerVolumeName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EstablishPeeringRequest::class, 'Google_Service_NetAppFiles_EstablishPeeringRequest');
