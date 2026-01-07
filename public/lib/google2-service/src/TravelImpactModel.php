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

namespace Google\Service;

use Google\Client;

/**
 * Service definition for TravelImpactModel (v1).
 *
 * <p>
 * Travel Impact Model API lets you query travel carbon emission estimates.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/travel/impact-model" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class TravelImpactModel extends \Google\Service
{


  public $flights;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the TravelImpactModel service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://travelimpactmodel.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://travelimpactmodel.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v1';
    $this->serviceName = 'travelimpactmodel';

    $this->flights = new TravelImpactModel\Resource\Flights(
        $this,
        $this->serviceName,
        'flights',
        [
          'methods' => [
            'computeFlightEmissions' => [
              'path' => 'v1/flights:computeFlightEmissions',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'computeScope3FlightEmissions' => [
              'path' => 'v1/flights:computeScope3FlightEmissions',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'computeTypicalFlightEmissions' => [
              'path' => 'v1/flights:computeTypicalFlightEmissions',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],
          ]
        ]
    );
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TravelImpactModel::class, 'Google_Service_TravelImpactModel');
