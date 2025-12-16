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

class ServiceAttachmentConnectedEndpoint extends \Google\Collection
{
  /**
   * The connection has been accepted by the producer.
   */
  public const STATUS_ACCEPTED = 'ACCEPTED';
  /**
   * The connection has been closed by the producer.
   */
  public const STATUS_CLOSED = 'CLOSED';
  /**
   * The connection has been accepted by the producer, but the producer needs to
   * take further action before the forwarding rule can serve traffic.
   */
  public const STATUS_NEEDS_ATTENTION = 'NEEDS_ATTENTION';
  /**
   * The connection is pending acceptance by the producer.
   */
  public const STATUS_PENDING = 'PENDING';
  /**
   * The consumer is still connected but not using the connection.
   */
  public const STATUS_REJECTED = 'REJECTED';
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  protected $collection_key = 'natIps';
  /**
   * The url of the consumer network.
   *
   * @var string
   */
  public $consumerNetwork;
  /**
   * The url of a connected endpoint.
   *
   * @var string
   */
  public $endpoint;
  /**
   * NAT IPs of the connected PSC endpoint and those of other endpoints
   * propagated from it.
   *
   * @var string[]
   */
  public $natIps;
  /**
   * The number of consumer Network Connectivity Center spokes that the
   * connected Private Service Connect endpoint has propagated to.
   *
   * @var string
   */
  public $propagatedConnectionCount;
  /**
   * The PSC connection id of the connected endpoint.
   *
   * @var string
   */
  public $pscConnectionId;
  /**
   * The status of a connected endpoint to this service attachment.
   *
   * @var string
   */
  public $status;

  /**
   * The url of the consumer network.
   *
   * @param string $consumerNetwork
   */
  public function setConsumerNetwork($consumerNetwork)
  {
    $this->consumerNetwork = $consumerNetwork;
  }
  /**
   * @return string
   */
  public function getConsumerNetwork()
  {
    return $this->consumerNetwork;
  }
  /**
   * The url of a connected endpoint.
   *
   * @param string $endpoint
   */
  public function setEndpoint($endpoint)
  {
    $this->endpoint = $endpoint;
  }
  /**
   * @return string
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
  /**
   * NAT IPs of the connected PSC endpoint and those of other endpoints
   * propagated from it.
   *
   * @param string[] $natIps
   */
  public function setNatIps($natIps)
  {
    $this->natIps = $natIps;
  }
  /**
   * @return string[]
   */
  public function getNatIps()
  {
    return $this->natIps;
  }
  /**
   * The number of consumer Network Connectivity Center spokes that the
   * connected Private Service Connect endpoint has propagated to.
   *
   * @param string $propagatedConnectionCount
   */
  public function setPropagatedConnectionCount($propagatedConnectionCount)
  {
    $this->propagatedConnectionCount = $propagatedConnectionCount;
  }
  /**
   * @return string
   */
  public function getPropagatedConnectionCount()
  {
    return $this->propagatedConnectionCount;
  }
  /**
   * The PSC connection id of the connected endpoint.
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
   * The status of a connected endpoint to this service attachment.
   *
   * Accepted values: ACCEPTED, CLOSED, NEEDS_ATTENTION, PENDING, REJECTED,
   * STATUS_UNSPECIFIED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceAttachmentConnectedEndpoint::class, 'Google_Service_Compute_ServiceAttachmentConnectedEndpoint');
