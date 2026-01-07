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
 * Service definition for Solar (v1).
 *
 * <p>
 * Solar API.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/maps/documentation/solar" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Solar extends \Google\Service
{
  /** See, edit, configure, and delete your Google Cloud data and see the email address for your Google Account.. */
  const CLOUD_PLATFORM =
      "https://www.googleapis.com/auth/cloud-platform";

  public $buildingInsights;
  public $dataLayers;
  public $geoTiff;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the Solar service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://solar.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://solar.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v1';
    $this->serviceName = 'solar';

    $this->buildingInsights = new Solar\Resource\BuildingInsights(
        $this,
        $this->serviceName,
        'buildingInsights',
        [
          'methods' => [
            'findClosest' => [
              'path' => 'v1/buildingInsights:findClosest',
              'httpMethod' => 'GET',
              'parameters' => [
                'exactQualityRequired' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
                'experiments' => [
                  'location' => 'query',
                  'type' => 'string',
                  'repeated' => true,
                ],
                'location.latitude' => [
                  'location' => 'query',
                  'type' => 'number',
                ],
                'location.longitude' => [
                  'location' => 'query',
                  'type' => 'number',
                ],
                'requiredQuality' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->dataLayers = new Solar\Resource\DataLayers(
        $this,
        $this->serviceName,
        'dataLayers',
        [
          'methods' => [
            'get' => [
              'path' => 'v1/dataLayers:get',
              'httpMethod' => 'GET',
              'parameters' => [
                'exactQualityRequired' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
                'experiments' => [
                  'location' => 'query',
                  'type' => 'string',
                  'repeated' => true,
                ],
                'location.latitude' => [
                  'location' => 'query',
                  'type' => 'number',
                ],
                'location.longitude' => [
                  'location' => 'query',
                  'type' => 'number',
                ],
                'pixelSizeMeters' => [
                  'location' => 'query',
                  'type' => 'number',
                ],
                'radiusMeters' => [
                  'location' => 'query',
                  'type' => 'number',
                ],
                'requiredQuality' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'view' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->geoTiff = new Solar\Resource\GeoTiff(
        $this,
        $this->serviceName,
        'geoTiff',
        [
          'methods' => [
            'get' => [
              'path' => 'v1/geoTiff:get',
              'httpMethod' => 'GET',
              'parameters' => [
                'id' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Solar::class, 'Google_Service_Solar');
