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

class ComputeTypicalFlightEmissionsResponse extends \Google\Collection
{
  protected $collection_key = 'typicalFlightEmissions';
  protected $modelVersionType = ModelVersion::class;
  protected $modelVersionDataType = '';
  protected $typicalFlightEmissionsType = TypicalFlightEmissions::class;
  protected $typicalFlightEmissionsDataType = 'array';

  /**
   * The model version under which typical flight emission estimates for all
   * flights in this response were computed.
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
  /**
   * Market's Typical Flight Emissions requested.
   *
   * @param TypicalFlightEmissions[] $typicalFlightEmissions
   */
  public function setTypicalFlightEmissions($typicalFlightEmissions)
  {
    $this->typicalFlightEmissions = $typicalFlightEmissions;
  }
  /**
   * @return TypicalFlightEmissions[]
   */
  public function getTypicalFlightEmissions()
  {
    return $this->typicalFlightEmissions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComputeTypicalFlightEmissionsResponse::class, 'Google_Service_TravelImpactModel_ComputeTypicalFlightEmissionsResponse');
