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

namespace Google\Service\Monitoring;

class BasicSli extends \Google\Collection
{
  protected $collection_key = 'version';
  protected $availabilityType = AvailabilityCriteria::class;
  protected $availabilityDataType = '';
  protected $latencyType = LatencyCriteria::class;
  protected $latencyDataType = '';
  /**
   * OPTIONAL: The set of locations to which this SLI is relevant. Telemetry
   * from other locations will not be used to calculate performance for this
   * SLI. If omitted, this SLI applies to all locations in which the Service has
   * activity. For service types that don't support breaking down by location,
   * setting this field will result in an error.
   *
   * @var string[]
   */
  public $location;
  /**
   * OPTIONAL: The set of RPCs to which this SLI is relevant. Telemetry from
   * other methods will not be used to calculate performance for this SLI. If
   * omitted, this SLI applies to all the Service's methods. For service types
   * that don't support breaking down by method, setting this field will result
   * in an error.
   *
   * @var string[]
   */
  public $method;
  /**
   * OPTIONAL: The set of API versions to which this SLI is relevant. Telemetry
   * from other API versions will not be used to calculate performance for this
   * SLI. If omitted, this SLI applies to all API versions. For service types
   * that don't support breaking down by version, setting this field will result
   * in an error.
   *
   * @var string[]
   */
  public $version;

  /**
   * Good service is defined to be the count of requests made to this service
   * that return successfully.
   *
   * @param AvailabilityCriteria $availability
   */
  public function setAvailability(AvailabilityCriteria $availability)
  {
    $this->availability = $availability;
  }
  /**
   * @return AvailabilityCriteria
   */
  public function getAvailability()
  {
    return $this->availability;
  }
  /**
   * Good service is defined to be the count of requests made to this service
   * that are fast enough with respect to latency.threshold.
   *
   * @param LatencyCriteria $latency
   */
  public function setLatency(LatencyCriteria $latency)
  {
    $this->latency = $latency;
  }
  /**
   * @return LatencyCriteria
   */
  public function getLatency()
  {
    return $this->latency;
  }
  /**
   * OPTIONAL: The set of locations to which this SLI is relevant. Telemetry
   * from other locations will not be used to calculate performance for this
   * SLI. If omitted, this SLI applies to all locations in which the Service has
   * activity. For service types that don't support breaking down by location,
   * setting this field will result in an error.
   *
   * @param string[] $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string[]
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * OPTIONAL: The set of RPCs to which this SLI is relevant. Telemetry from
   * other methods will not be used to calculate performance for this SLI. If
   * omitted, this SLI applies to all the Service's methods. For service types
   * that don't support breaking down by method, setting this field will result
   * in an error.
   *
   * @param string[] $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return string[]
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * OPTIONAL: The set of API versions to which this SLI is relevant. Telemetry
   * from other API versions will not be used to calculate performance for this
   * SLI. If omitted, this SLI applies to all API versions. For service types
   * that don't support breaking down by version, setting this field will result
   * in an error.
   *
   * @param string[] $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string[]
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BasicSli::class, 'Google_Service_Monitoring_BasicSli');
