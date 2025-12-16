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

class Trace extends \Google\Collection
{
  protected $collection_key = 'steps';
  protected $endpointInfoType = EndpointInfo::class;
  protected $endpointInfoDataType = '';
  /**
   * ID of trace. For forward traces, this ID is unique for each trace. For
   * return traces, it matches ID of associated forward trace. A single forward
   * trace can be associated with none, one or more than one return trace.
   *
   * @var int
   */
  public $forwardTraceId;
  protected $stepsType = Step::class;
  protected $stepsDataType = 'array';

  /**
   * Derived from the source and destination endpoints definition specified by
   * user request, and validated by the data plane model. If there are multiple
   * traces starting from different source locations, then the endpoint_info may
   * be different between traces.
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
   * ID of trace. For forward traces, this ID is unique for each trace. For
   * return traces, it matches ID of associated forward trace. A single forward
   * trace can be associated with none, one or more than one return trace.
   *
   * @param int $forwardTraceId
   */
  public function setForwardTraceId($forwardTraceId)
  {
    $this->forwardTraceId = $forwardTraceId;
  }
  /**
   * @return int
   */
  public function getForwardTraceId()
  {
    return $this->forwardTraceId;
  }
  /**
   * A trace of a test contains multiple steps from the initial state to the
   * final state (delivered, dropped, forwarded, or aborted). The steps are
   * ordered by the processing sequence within the simulated network state
   * machine. It is critical to preserve the order of the steps and avoid
   * reordering or sorting them.
   *
   * @param Step[] $steps
   */
  public function setSteps($steps)
  {
    $this->steps = $steps;
  }
  /**
   * @return Step[]
   */
  public function getSteps()
  {
    return $this->steps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Trace::class, 'Google_Service_NetworkManagement_Trace');
