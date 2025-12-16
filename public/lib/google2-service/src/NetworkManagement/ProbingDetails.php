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

class ProbingDetails extends \Google\Collection
{
  /**
   * No reason was specified.
   */
  public const ABORT_CAUSE_PROBING_ABORT_CAUSE_UNSPECIFIED = 'PROBING_ABORT_CAUSE_UNSPECIFIED';
  /**
   * The user lacks permission to access some of the network resources required
   * to run the test.
   */
  public const ABORT_CAUSE_PERMISSION_DENIED = 'PERMISSION_DENIED';
  /**
   * No valid source endpoint could be derived from the request.
   */
  public const ABORT_CAUSE_NO_SOURCE_LOCATION = 'NO_SOURCE_LOCATION';
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
  protected $collection_key = 'edgeResponses';
  /**
   * The reason probing was aborted.
   *
   * @var string
   */
  public $abortCause;
  protected $destinationEgressLocationType = EdgeLocation::class;
  protected $destinationEgressLocationDataType = '';
  protected $edgeResponsesType = SingleEdgeResponse::class;
  protected $edgeResponsesDataType = 'array';
  protected $endpointInfoType = EndpointInfo::class;
  protected $endpointInfoDataType = '';
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * Whether all relevant edge devices were probed.
   *
   * @var bool
   */
  public $probedAllDevices;
  protected $probingLatencyType = LatencyDistribution::class;
  protected $probingLatencyDataType = '';
  /**
   * The overall result of active probing.
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
   * The time that reachability was assessed through active probing.
   *
   * @var string
   */
  public $verifyTime;

  /**
   * The reason probing was aborted.
   *
   * Accepted values: PROBING_ABORT_CAUSE_UNSPECIFIED, PERMISSION_DENIED,
   * NO_SOURCE_LOCATION
   *
   * @param self::ABORT_CAUSE_* $abortCause
   */
  public function setAbortCause($abortCause)
  {
    $this->abortCause = $abortCause;
  }
  /**
   * @return self::ABORT_CAUSE_*
   */
  public function getAbortCause()
  {
    return $this->abortCause;
  }
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
   * Probing results for all edge devices.
   *
   * @param SingleEdgeResponse[] $edgeResponses
   */
  public function setEdgeResponses($edgeResponses)
  {
    $this->edgeResponses = $edgeResponses;
  }
  /**
   * @return SingleEdgeResponse[]
   */
  public function getEdgeResponses()
  {
    return $this->edgeResponses;
  }
  /**
   * The source and destination endpoints derived from the test input and used
   * for active probing.
   *
   * @param EndpointInfo $endpointInfo
   */
  public function setEndpointInfo(EndpointInfo $endpointInfo)
  {
    $this->endpointInfo = $endpointInfo;
  }
  /**
   * @return EndpointInfo
   */
  public function getEndpointInfo()
  {
    return $this->endpointInfo;
  }
  /**
   * Details about an internal failure or the cancellation of active probing.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Whether all relevant edge devices were probed.
   *
   * @param bool $probedAllDevices
   */
  public function setProbedAllDevices($probedAllDevices)
  {
    $this->probedAllDevices = $probedAllDevices;
  }
  /**
   * @return bool
   */
  public function getProbedAllDevices()
  {
    return $this->probedAllDevices;
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
   * The overall result of active probing.
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
  /**
   * The time that reachability was assessed through active probing.
   *
   * @param string $verifyTime
   */
  public function setVerifyTime($verifyTime)
  {
    $this->verifyTime = $verifyTime;
  }
  /**
   * @return string
   */
  public function getVerifyTime()
  {
    return $this->verifyTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProbingDetails::class, 'Google_Service_NetworkManagement_ProbingDetails');
