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

class HybridPeeringDetails extends \Google\Model
{
  /**
   * Output only. Copy-paste-able commands to be used on user's ONTAP to accept
   * peering requests.
   *
   * @var string
   */
  public $command;
  /**
   * Output only. Expiration time for the peering command to be executed on
   * user's ONTAP.
   *
   * @var string
   */
  public $commandExpiryTime;
  /**
   * Output only. Temporary passphrase generated to accept cluster peering
   * command.
   *
   * @var string
   */
  public $passphrase;
  /**
   * Output only. Name of the user's local source cluster to be peered with the
   * destination cluster.
   *
   * @var string
   */
  public $peerClusterName;
  /**
   * Output only. Name of the user's local source vserver svm to be peered with
   * the destination vserver svm.
   *
   * @var string
   */
  public $peerSvmName;
  /**
   * Output only. Name of the user's local source volume to be peered with the
   * destination volume.
   *
   * @var string
   */
  public $peerVolumeName;
  /**
   * Output only. IP address of the subnet.
   *
   * @var string
   */
  public $subnetIp;

  /**
   * Output only. Copy-paste-able commands to be used on user's ONTAP to accept
   * peering requests.
   *
   * @param string $command
   */
  public function setCommand($command)
  {
    $this->command = $command;
  }
  /**
   * @return string
   */
  public function getCommand()
  {
    return $this->command;
  }
  /**
   * Output only. Expiration time for the peering command to be executed on
   * user's ONTAP.
   *
   * @param string $commandExpiryTime
   */
  public function setCommandExpiryTime($commandExpiryTime)
  {
    $this->commandExpiryTime = $commandExpiryTime;
  }
  /**
   * @return string
   */
  public function getCommandExpiryTime()
  {
    return $this->commandExpiryTime;
  }
  /**
   * Output only. Temporary passphrase generated to accept cluster peering
   * command.
   *
   * @param string $passphrase
   */
  public function setPassphrase($passphrase)
  {
    $this->passphrase = $passphrase;
  }
  /**
   * @return string
   */
  public function getPassphrase()
  {
    return $this->passphrase;
  }
  /**
   * Output only. Name of the user's local source cluster to be peered with the
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
   * Output only. Name of the user's local source vserver svm to be peered with
   * the destination vserver svm.
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
   * Output only. Name of the user's local source volume to be peered with the
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
  /**
   * Output only. IP address of the subnet.
   *
   * @param string $subnetIp
   */
  public function setSubnetIp($subnetIp)
  {
    $this->subnetIp = $subnetIp;
  }
  /**
   * @return string
   */
  public function getSubnetIp()
  {
    return $this->subnetIp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HybridPeeringDetails::class, 'Google_Service_NetAppFiles_HybridPeeringDetails');
