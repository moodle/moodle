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

namespace Google\Service\TravelImpactModel;

class Market extends \Google\Model
{
  /**
   * Required. IATA airport code for flight destination, e.g. "JFK".
   *
   * @var string
   */
  public $destination;
  /**
   * Required. IATA airport code for flight origin, e.g. "LHR".
   *
   * @var string
   */
  public $origin;

  /**
   * Required. IATA airport code for flight destination, e.g. "JFK".
   *
   * @param string $destination
   */
  public function setDestination($destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return string
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * Required. IATA airport code for flight origin, e.g. "LHR".
   *
   * @param string $origin
   */
  public function setOrigin($origin)
  {
    $this->origin = $origin;
  }
  /**
   * @return string
   */
  public function getOrigin()
  {
    return $this->origin;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Market::class, 'Google_Service_TravelImpactModel_Market');
