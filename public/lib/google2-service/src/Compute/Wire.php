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

class Wire extends \Google\Collection
{
  protected $collection_key = 'endpoints';
  /**
   * Output only. [Output Only] Indicates whether the wire is enabled. When
   * false, the wire is disabled. When true and when the wire group of the wire
   * is also enabled, the wire is enabled. Defaults to true.
   *
   * @var bool
   */
  public $adminEnabled;
  protected $endpointsType = WireEndpoint::class;
  protected $endpointsDataType = 'array';
  /**
   * Output only. [Output Only] A label that identifies the wire. The format of
   * this label combines the existing labels of the wire group endpoints and
   * Interconnect connections used by this wire in alphabetical order as
   * follows: `ENDPOINT_A+CONNECTION_A1,ENDPOINT_B+CONNECTION_B1`, where:
   * - ENDPOINT_A and ENDPOINT_B: are the labels    that you entered as map keys
   * when you specified the wire group endpoint    objects.    - CONNECTION_A1
   * and CONNECTION_B1: are the    labels that you entered as map keys when you
   * specified the wire group    Interconnect objects.
   *
   * @var string
   */
  public $label;
  protected $wirePropertiesType = WireProperties::class;
  protected $wirePropertiesDataType = '';

  /**
   * Output only. [Output Only] Indicates whether the wire is enabled. When
   * false, the wire is disabled. When true and when the wire group of the wire
   * is also enabled, the wire is enabled. Defaults to true.
   *
   * @param bool $adminEnabled
   */
  public function setAdminEnabled($adminEnabled)
  {
    $this->adminEnabled = $adminEnabled;
  }
  /**
   * @return bool
   */
  public function getAdminEnabled()
  {
    return $this->adminEnabled;
  }
  /**
   * Output only. Wire endpoints are specific Interconnect connections.
   *
   * @param WireEndpoint[] $endpoints
   */
  public function setEndpoints($endpoints)
  {
    $this->endpoints = $endpoints;
  }
  /**
   * @return WireEndpoint[]
   */
  public function getEndpoints()
  {
    return $this->endpoints;
  }
  /**
   * Output only. [Output Only] A label that identifies the wire. The format of
   * this label combines the existing labels of the wire group endpoints and
   * Interconnect connections used by this wire in alphabetical order as
   * follows: `ENDPOINT_A+CONNECTION_A1,ENDPOINT_B+CONNECTION_B1`, where:
   * - ENDPOINT_A and ENDPOINT_B: are the labels    that you entered as map keys
   * when you specified the wire group endpoint    objects.    - CONNECTION_A1
   * and CONNECTION_B1: are the    labels that you entered as map keys when you
   * specified the wire group    Interconnect objects.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * Output only. [Output Only] Properties of the wire.
   *
   * @param WireProperties $wireProperties
   */
  public function setWireProperties(WireProperties $wireProperties)
  {
    $this->wireProperties = $wireProperties;
  }
  /**
   * @return WireProperties
   */
  public function getWireProperties()
  {
    return $this->wireProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Wire::class, 'Google_Service_Compute_Wire');
