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

namespace Google\Service\NetworkServices;

class TrafficPortSelector extends \Google\Collection
{
  protected $collection_key = 'ports';
  /**
   * Optional. A list of ports. Can be port numbers or port range (example,
   * [80-90] specifies all ports from 80 to 90, including 80 and 90) or named
   * ports or * to specify all ports. If the list is empty, all ports are
   * selected.
   *
   * @var string[]
   */
  public $ports;

  /**
   * Optional. A list of ports. Can be port numbers or port range (example,
   * [80-90] specifies all ports from 80 to 90, including 80 and 90) or named
   * ports or * to specify all ports. If the list is empty, all ports are
   * selected.
   *
   * @param string[] $ports
   */
  public function setPorts($ports)
  {
    $this->ports = $ports;
  }
  /**
   * @return string[]
   */
  public function getPorts()
  {
    return $this->ports;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TrafficPortSelector::class, 'Google_Service_NetworkServices_TrafficPortSelector');
