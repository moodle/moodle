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

class ReachabilityDetails extends \Google\Collection
{
  /**
   * No result was specified.
   */
  public const RESULT_RESULT_UNSPECIFIED = 'RESULT_UNSPECIFIED';
  /**
   * Possible scenarios are: * The configuration analysis determined that a
   * packet originating from the source is expected to reach the destination. *
   * The analysis didn't complete because the user lacks permission for some of
   * the resources in the trace. However, at the time the user's permission
   * became insufficient, the trace had been successful so far.
   */
  public const RESULT_REACHABLE = 'REACHABLE';
  /**
   * A packet originating from the source is expected to be dropped before
   * reaching the destination.
   */
  public const RESULT_UNREACHABLE = 'UNREACHABLE';
  /**
   * The source and destination endpoints do not uniquely identify the test
   * location in the network, and the reachability result contains multiple
   * traces. For some traces, a packet could be delivered, and for others, it
   * would not be. This result is also assigned to configuration analysis of
   * return path if on its own it should be REACHABLE, but configuration
   * analysis of forward path is AMBIGUOUS.
   */
  public const RESULT_AMBIGUOUS = 'AMBIGUOUS';
  /**
   * The configuration analysis did not complete. Possible reasons are: * A
   * permissions error occurred--for example, the user might not have read
   * permission for all of the resources named in the test. * An internal error
   * occurred. * The analyzer received an invalid or unsupported argument or was
   * unable to identify a known endpoint.
   */
  public const RESULT_UNDETERMINED = 'UNDETERMINED';
  protected $collection_key = 'traces';
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * The overall result of the test's configuration analysis.
   *
   * @var string
   */
  public $result;
  protected $tracesType = Trace::class;
  protected $tracesDataType = 'array';
  /**
   * The time of the configuration analysis.
   *
   * @var string
   */
  public $verifyTime;

  /**
   * The details of a failure or a cancellation of reachability analysis.
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
   * The overall result of the test's configuration analysis.
   *
   * Accepted values: RESULT_UNSPECIFIED, REACHABLE, UNREACHABLE, AMBIGUOUS,
   * UNDETERMINED
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
   * Result may contain a list of traces if a test has multiple possible paths
   * in the network, such as when destination endpoint is a load balancer with
   * multiple backends.
   *
   * @param Trace[] $traces
   */
  public function setTraces($traces)
  {
    $this->traces = $traces;
  }
  /**
   * @return Trace[]
   */
  public function getTraces()
  {
    return $this->traces;
  }
  /**
   * The time of the configuration analysis.
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
class_alias(ReachabilityDetails::class, 'Google_Service_NetworkManagement_ReachabilityDetails');
