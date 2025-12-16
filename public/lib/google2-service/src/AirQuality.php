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
 * Service definition for AirQuality (v1).
 *
 * <p>
 * The Air Quality API.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/maps/documentation/air-quality" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class AirQuality extends \Google\Service
{
  /** See, edit, configure, and delete your Google Cloud data and see the email address for your Google Account.. */
  const CLOUD_PLATFORM =
      "https://www.googleapis.com/auth/cloud-platform";

  public $currentConditions;
  public $forecast;
  public $history;
  public $mapTypes_heatmapTiles;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the AirQuality service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://airquality.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://airquality.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v1';
    $this->serviceName = 'airquality';

    $this->currentConditions = new AirQuality\Resource\CurrentConditions(
        $this,
        $this->serviceName,
        'currentConditions',
        [
          'methods' => [
            'lookup' => [
              'path' => 'v1/currentConditions:lookup',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],
          ]
        ]
    );
    $this->forecast = new AirQuality\Resource\Forecast(
        $this,
        $this->serviceName,
        'forecast',
        [
          'methods' => [
            'lookup' => [
              'path' => 'v1/forecast:lookup',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],
          ]
        ]
    );
    $this->history = new AirQuality\Resource\History(
        $this,
        $this->serviceName,
        'history',
        [
          'methods' => [
            'lookup' => [
              'path' => 'v1/history:lookup',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],
          ]
        ]
    );
    $this->mapTypes_heatmapTiles = new AirQuality\Resource\MapTypesHeatmapTiles(
        $this,
        $this->serviceName,
        'heatmapTiles',
        [
          'methods' => [
            'lookupHeatmapTile' => [
              'path' => 'v1/mapTypes/{mapType}/heatmapTiles/{zoom}/{x}/{y}',
              'httpMethod' => 'GET',
              'parameters' => [
                'mapType' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'zoom' => [
                  'location' => 'path',
                  'type' => 'integer',
                  'required' => true,
                ],
                'x' => [
                  'location' => 'path',
                  'type' => 'integer',
                  'required' => true,
                ],
                'y' => [
                  'location' => 'path',
                  'type' => 'integer',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AirQuality::class, 'Google_Service_AirQuality');
