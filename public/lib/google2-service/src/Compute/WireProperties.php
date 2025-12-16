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

class WireProperties extends \Google\Model
{
  /**
   * Configures a separate unmetered bandwidth allocation (and associated
   * charges) for each wire in the group.
   */
  public const BANDWIDTH_ALLOCATION_ALLOCATE_PER_WIRE = 'ALLOCATE_PER_WIRE';
  /**
   * This is the default behavior. Configures one unmetered bandwidth allocation
   * for the wire group. The unmetered bandwidth is divided equally across each
   * wire in the group, but dynamic throttling reallocates unused unmetered
   * bandwidth from unused or underused wires to other wires in the group.
   */
  public const BANDWIDTH_ALLOCATION_SHARED_WITH_WIRE_GROUP = 'SHARED_WITH_WIRE_GROUP';
  /**
   * Set the port line protocol down when inline probes detect a fault. This
   * setting is only permitted on port mode pseudowires.
   */
  public const FAULT_RESPONSE_DISABLE_PORT = 'DISABLE_PORT';
  /**
   * Default.
   */
  public const FAULT_RESPONSE_NONE = 'NONE';
  /**
   * The configuration of the bandwidth allocation, one of the following:
   * - ALLOCATE_PER_WIRE: configures a separate unmetered bandwidth allocation
   * (and associated charges) for each wire in the group.    -
   * SHARED_WITH_WIRE_GROUP: this is the default behavior, which configures
   * one unmetered bandwidth allocation for the wire group. The unmetered
   * bandwidth is divided equally across each wire in the group, but dynamic
   * throttling reallocates unused unmetered bandwidth from unused or underused
   * wires to other wires in the group.
   *
   * @var string
   */
  public $bandwidthAllocation;
  /**
   * The unmetered bandwidth in Gigabits per second, using decimal units. `10`
   * is 10 Gbps, `100` is 100 Gbps. The bandwidth must be greater than 0.
   *
   * @var string
   */
  public $bandwidthUnmetered;
  /**
   * Response when a fault is detected in a pseudowire:        - NONE: default.
   * - DISABLE_PORT: set the port line protocol down when inline probes
   * detect a fault. This setting is only permitted on port mode    pseudowires.
   *
   * @var string
   */
  public $faultResponse;

  /**
   * The configuration of the bandwidth allocation, one of the following:
   * - ALLOCATE_PER_WIRE: configures a separate unmetered bandwidth allocation
   * (and associated charges) for each wire in the group.    -
   * SHARED_WITH_WIRE_GROUP: this is the default behavior, which configures
   * one unmetered bandwidth allocation for the wire group. The unmetered
   * bandwidth is divided equally across each wire in the group, but dynamic
   * throttling reallocates unused unmetered bandwidth from unused or underused
   * wires to other wires in the group.
   *
   * Accepted values: ALLOCATE_PER_WIRE, SHARED_WITH_WIRE_GROUP
   *
   * @param self::BANDWIDTH_ALLOCATION_* $bandwidthAllocation
   */
  public function setBandwidthAllocation($bandwidthAllocation)
  {
    $this->bandwidthAllocation = $bandwidthAllocation;
  }
  /**
   * @return self::BANDWIDTH_ALLOCATION_*
   */
  public function getBandwidthAllocation()
  {
    return $this->bandwidthAllocation;
  }
  /**
   * The unmetered bandwidth in Gigabits per second, using decimal units. `10`
   * is 10 Gbps, `100` is 100 Gbps. The bandwidth must be greater than 0.
   *
   * @param string $bandwidthUnmetered
   */
  public function setBandwidthUnmetered($bandwidthUnmetered)
  {
    $this->bandwidthUnmetered = $bandwidthUnmetered;
  }
  /**
   * @return string
   */
  public function getBandwidthUnmetered()
  {
    return $this->bandwidthUnmetered;
  }
  /**
   * Response when a fault is detected in a pseudowire:        - NONE: default.
   * - DISABLE_PORT: set the port line protocol down when inline probes
   * detect a fault. This setting is only permitted on port mode    pseudowires.
   *
   * Accepted values: DISABLE_PORT, NONE
   *
   * @param self::FAULT_RESPONSE_* $faultResponse
   */
  public function setFaultResponse($faultResponse)
  {
    $this->faultResponse = $faultResponse;
  }
  /**
   * @return self::FAULT_RESPONSE_*
   */
  public function getFaultResponse()
  {
    return $this->faultResponse;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WireProperties::class, 'Google_Service_Compute_WireProperties');
