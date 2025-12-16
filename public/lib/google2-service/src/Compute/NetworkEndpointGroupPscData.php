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

class NetworkEndpointGroupPscData extends \Google\Model
{
  /**
   * The connection has been accepted by the producer.
   */
  public const PSC_CONNECTION_STATUS_ACCEPTED = 'ACCEPTED';
  /**
   * The connection has been closed by the producer and will not serve traffic
   * going forward.
   */
  public const PSC_CONNECTION_STATUS_CLOSED = 'CLOSED';
  /**
   * The connection has been accepted by the producer, but the producer needs to
   * take further action before the forwarding rule can serve traffic.
   */
  public const PSC_CONNECTION_STATUS_NEEDS_ATTENTION = 'NEEDS_ATTENTION';
  /**
   * The connection is pending acceptance by the producer.
   */
  public const PSC_CONNECTION_STATUS_PENDING = 'PENDING';
  /**
   * The connection has been rejected by the producer.
   */
  public const PSC_CONNECTION_STATUS_REJECTED = 'REJECTED';
  public const PSC_CONNECTION_STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * Output only. [Output Only] Address allocated from given subnetwork for PSC.
   * This IP address acts as a VIP for a PSC NEG, allowing it to act as an
   * endpoint in L7 PSC-XLB.
   *
   * @var string
   */
  public $consumerPscAddress;
  /**
   * The psc producer port is used to connect PSC NEG with specific port on the
   * PSC Producer side; should only be used for the PRIVATE_SERVICE_CONNECT NEG
   * type
   *
   * @var int
   */
  public $producerPort;
  /**
   * Output only. [Output Only] The PSC connection id of the PSC Network
   * Endpoint Group Consumer.
   *
   * @var string
   */
  public $pscConnectionId;
  /**
   * Output only. [Output Only] The connection status of the PSC Forwarding
   * Rule.
   *
   * @var string
   */
  public $pscConnectionStatus;

  /**
   * Output only. [Output Only] Address allocated from given subnetwork for PSC.
   * This IP address acts as a VIP for a PSC NEG, allowing it to act as an
   * endpoint in L7 PSC-XLB.
   *
   * @param string $consumerPscAddress
   */
  public function setConsumerPscAddress($consumerPscAddress)
  {
    $this->consumerPscAddress = $consumerPscAddress;
  }
  /**
   * @return string
   */
  public function getConsumerPscAddress()
  {
    return $this->consumerPscAddress;
  }
  /**
   * The psc producer port is used to connect PSC NEG with specific port on the
   * PSC Producer side; should only be used for the PRIVATE_SERVICE_CONNECT NEG
   * type
   *
   * @param int $producerPort
   */
  public function setProducerPort($producerPort)
  {
    $this->producerPort = $producerPort;
  }
  /**
   * @return int
   */
  public function getProducerPort()
  {
    return $this->producerPort;
  }
  /**
   * Output only. [Output Only] The PSC connection id of the PSC Network
   * Endpoint Group Consumer.
   *
   * @param string $pscConnectionId
   */
  public function setPscConnectionId($pscConnectionId)
  {
    $this->pscConnectionId = $pscConnectionId;
  }
  /**
   * @return string
   */
  public function getPscConnectionId()
  {
    return $this->pscConnectionId;
  }
  /**
   * Output only. [Output Only] The connection status of the PSC Forwarding
   * Rule.
   *
   * Accepted values: ACCEPTED, CLOSED, NEEDS_ATTENTION, PENDING, REJECTED,
   * STATUS_UNSPECIFIED
   *
   * @param self::PSC_CONNECTION_STATUS_* $pscConnectionStatus
   */
  public function setPscConnectionStatus($pscConnectionStatus)
  {
    $this->pscConnectionStatus = $pscConnectionStatus;
  }
  /**
   * @return self::PSC_CONNECTION_STATUS_*
   */
  public function getPscConnectionStatus()
  {
    return $this->pscConnectionStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkEndpointGroupPscData::class, 'Google_Service_Compute_NetworkEndpointGroupPscData');
