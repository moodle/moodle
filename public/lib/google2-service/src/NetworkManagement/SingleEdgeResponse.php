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

namespace Google\Service\NetworkManagement;

class SingleEdgeResponse extends \Google\Model
{
  /**
   * No result was specified.
   */
  public const RESULT_PROBING_RESULT_UNSPECIFIED = 'PROBING_RESULT_UNSPECIFIED';
  /**
   * At least 95% of packets reached the destination.
   */
  public const RESULT_REACHABLE = 'REACHABLE';
  /**
   * No packets reached the destination.
   */
  public const RESULT_UNREACHABLE = 'UNREACHABLE';
  /**
   * Less than 95% of packets reached the destination.
   */
  public const RESULT_REACHABILITY_INCONSISTENT = 'REACHABILITY_INCONSISTENT';
  /**
   * Reachability could not be determined. Possible reasons are: * The user
   * lacks permission to access some of the network resources required to run
   * the test. * No valid source endpoint could be derived from the request. *
   * An internal error occurred.
   */
  public const RESULT_UNDETERMINED = 'UNDETERMINED';
  protected $destinationEgressLocationType = EdgeLocation::class;
  protected $destinationEgressLocationDataType = '';
  /**
   * Router name in the format '{router}.{metroshard}'. For example: pf01.aaa01,
   * pr02.aaa01.
   *
   * @var string
   */
  public $destinationRouter;
  protected $probingLatencyType = LatencyDistribution::class;
  protected $probingLatencyDataType = '';
  /**
   * The overall result of active probing for this egress device.
   *
   * @var string
   */
  public $result;
  /**
   * Number of probes sent.
   *
   * @var int
   */
  public $sentProbeCount;
  /**
   * Number of probes that reached the destination.
   *
   * @var int
   */
  public $successfulProbeCount;

  /**
   * The EdgeLocation from which a packet, destined to the internet, will egress
   * the Google network. This will only be populated for a connectivity test
   * which has an internet destination address. The absence of this field *must
   * not* be used as an indication that the destination is part of the Google
   * network.
   *
   * @param EdgeLocation $destinationEgressLocation
   */
  public function setDestinationEgressLocation(EdgeLocation $destinationEgressLocation)
  {
    $this->destinationEgressLocation = $destinationEgressLocation;
  }
  /**
   * @return EdgeLocation
   */
  public function getDestinationEgressLocation()
  {
    return $this->destinationEgressLocation;
  }
  /**
   * Router name in the format '{router}.{metroshard}'. For example: pf01.aaa01,
   * pr02.aaa01.
   *
   * @param string $destinationRouter
   */
  public function setDestinationRouter($destinationRouter)
  {
    $this->destinationRouter = $destinationRouter;
  }
  /**
   * @return string
   */
  public function getDestinationRouter()
  {
    return $this->destinationRouter;
  }
  /**
   * Latency as measured by active probing in one direction: from the source to
   * the destination endpoint.
   *
   * @param LatencyDistribution $probingLatency
   */
  public function setProbingLatency(LatencyDistribution $probingLatency)
  {
    $this->probingLatency = $probingLatency;
  }
  /**
   * @return LatencyDistribution
   */
  public function getProbingLatency()
  {
    return $this->probingLatency;
  }
  /**
   * The overall result of active probing for this egress device.
   *
   * Accepted values: PROBING_RESULT_UNSPECIFIED, REACHABLE, UNREACHABLE,
   * REACHABILITY_INCONSISTENT, UNDETERMINED
   *
   * @param self::RESULT_* $result
   */
  public function setResult($result)
  {
    $this->result = $result;
  }
  /**
   * @return self::RESULT_*
   */
  public function getResult()
  {
    return $this->result;
  }
  /**
   * Number of probes sent.
   *
   * @param int $sentProbeCount
   */
  public function setSentProbeCount($sentProbeCount)
  {
    $this->sentProbeCount = $sentProbeCount;
  }
  /**
   * @return int
   */
  public function getSentProbeCount()
  {
    return $this->sentProbeCount;
  }
  /**
   * Number of probes that reached the destination.
   *
   * @param int $successfulProbeCount
   */
  public function setSuccessfulProbeCount($successfulProbeCount)
  {
    $this->successfulProbeCount = $successfulProbeCount;
  }
  /**
   * @return int
   */
  public function getSuccessfulProbeCount()
  {
    return $this->successfulProbeCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SingleEdgeResponse::class, 'Google_Service_NetworkManagement_SingleEdgeResponse');
