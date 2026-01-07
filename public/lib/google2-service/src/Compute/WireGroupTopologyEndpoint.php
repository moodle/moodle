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

class WireGroupTopologyEndpoint extends \Google\Model
{
  /**
   * Output only. The InterconnectLocation.city (metropolitan area designator)
   * that all interconnects are located in.
   *
   * @var string
   */
  public $city;
  /**
   * Output only. Endpoint label from the wire group.
   *
   * @var string
   */
  public $label;

  /**
   * Output only. The InterconnectLocation.city (metropolitan area designator)
   * that all interconnects are located in.
   *
   * @param string $city
   */
  public function setCity($city)
  {
    $this->city = $city;
  }
  /**
   * @return string
   */
  public function getCity()
  {
    return $this->city;
  }
  /**
   * Output only. Endpoint label from the wire group.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WireGroupTopologyEndpoint::class, 'Google_Service_Compute_WireGroupTopologyEndpoint');
