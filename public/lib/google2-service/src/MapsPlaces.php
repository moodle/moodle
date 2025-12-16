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
 * Service definition for MapsPlaces (v1).
 *
 * <p>
</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://mapsplatform.google.com/maps-products/#places-section" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class MapsPlaces extends \Google\Service
{
  /** See, edit, configure, and delete your Google Cloud data and see the email address for your Google Account.. */
  const CLOUD_PLATFORM =
      "https://www.googleapis.com/auth/cloud-platform";
  /** Private Service: https://www.googleapis.com/auth/maps-platform.places. */
  const MAPS_PLATFORM_PLACES =
      "https://www.googleapis.com/auth/maps-platform.places";
  /** Private Service: https://www.googleapis.com/auth/maps-platform.places.autocomplete. */
  const MAPS_PLATFORM_PLACES_AUTOCOMPLETE =
      "https://www.googleapis.com/auth/maps-platform.places.autocomplete";
  /** Private Service: https://www.googleapis.com/auth/maps-platform.places.details. */
  const MAPS_PLATFORM_PLACES_DETAILS =
      "https://www.googleapis.com/auth/maps-platform.places.details";
  /** Private Service: https://www.googleapis.com/auth/maps-platform.places.getphotomedia. */
  const MAPS_PLATFORM_PLACES_GETPHOTOMEDIA =
      "https://www.googleapis.com/auth/maps-platform.places.getphotomedia";
  /** Private Service: https://www.googleapis.com/auth/maps-platform.places.nearbysearch. */
  const MAPS_PLATFORM_PLACES_NEARBYSEARCH =
      "https://www.googleapis.com/auth/maps-platform.places.nearbysearch";
  /** Private Service: https://www.googleapis.com/auth/maps-platform.places.textsearch. */
  const MAPS_PLATFORM_PLACES_TEXTSEARCH =
      "https://www.googleapis.com/auth/maps-platform.places.textsearch";

  public $places;
  public $places_photos;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the MapsPlaces service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://places.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://places.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v1';
    $this->serviceName = 'places';

    $this->places = new MapsPlaces\Resource\Places(
        $this,
        $this->serviceName,
        'places',
        [
          'methods' => [
            'autocomplete' => [
              'path' => 'v1/places:autocomplete',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'get' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'languageCode' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'regionCode' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'sessionToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'searchNearby' => [
              'path' => 'v1/places:searchNearby',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'searchText' => [
              'path' => 'v1/places:searchText',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],
          ]
        ]
    );
    $this->places_photos = new MapsPlaces\Resource\PlacesPhotos(
        $this,
        $this->serviceName,
        'photos',
        [
          'methods' => [
            'getMedia' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'maxHeightPx' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'maxWidthPx' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'skipHttpRedirect' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
              ],
            ],
          ]
        ]
    );
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MapsPlaces::class, 'Google_Service_MapsPlaces');
