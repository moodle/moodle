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

class ComputeScope3FlightEmissionsResponse extends \Google\Collection
{
  protected $collection_key = 'flightEmissions';
  protected $flightEmissionsType = Scope3FlightEmissions::class;
  protected $flightEmissionsDataType = 'array';
  protected $modelVersionType = ModelVersion::class;
  protected $modelVersionDataType = '';

  /**
   * List of flight segments with emission estimates.
   *
   * @param Scope3FlightEmissions[] $flightEmissions
   */
  public function setFlightEmissions($flightEmissions)
  {
    $this->flightEmissions = $flightEmissions;
  }
  /**
   * @return Scope3FlightEmissions[]
   */
  public function getFlightEmissions()
  {
    return $this->flightEmissions;
  }
  /**
   * The model version under which emission estimates for all flights in this
   * response were computed.
   *
   * @param ModelVersion $modelVersion
   */
  public function setModelVersion(ModelVersion $modelVersion)
  {
    $this->modelVersion = $modelVersion;
  }
  /**
   * @return ModelVersion
   */
  public function getModelVersion()
  {
    return $this->modelVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComputeScope3FlightEmissionsResponse::class, 'Google_Service_TravelImpactModel_ComputeScope3FlightEmissionsResponse');
