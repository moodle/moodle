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
 * Service definition for Kmsinventory (v1).
 *
 * <p>
</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://cloud.google.com/kms/" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Kmsinventory extends \Google\Service
{
  /** See, edit, configure, and delete your Google Cloud data and see the email address for your Google Account.. */
  const CLOUD_PLATFORM =
      "https://www.googleapis.com/auth/cloud-platform";

  public $organizations_protectedResources;
  public $projects_cryptoKeys;
  public $projects_locations_keyRings_cryptoKeys;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the Kmsinventory service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://kmsinventory.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://kmsinventory.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v1';
    $this->serviceName = 'kmsinventory';

    $this->organizations_protectedResources = new Kmsinventory\Resource\OrganizationsProtectedResources(
        $this,
        $this->serviceName,
        'protectedResources',
        [
          'methods' => [
            'search' => [
              'path' => 'v1/{+scope}/protectedResources:search',
              'httpMethod' => 'GET',
              'parameters' => [
                'scope' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'cryptoKey' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'resourceTypes' => [
                  'location' => 'query',
                  'type' => 'string',
                  'repeated' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->projects_cryptoKeys = new Kmsinventory\Resource\ProjectsCryptoKeys(
        $this,
        $this->serviceName,
        'cryptoKeys',
        [
          'methods' => [
            'list' => [
              'path' => 'v1/{+parent}/cryptoKeys',
              'httpMethod' => 'GET',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->projects_locations_keyRings_cryptoKeys = new Kmsinventory\Resource\ProjectsLocationsKeyRingsCryptoKeys(
        $this,
        $this->serviceName,
        'cryptoKeys',
        [
          'methods' => [
            'getProtectedResourcesSummary' => [
              'path' => 'v1/{+name}/protectedResourcesSummary',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
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
class_alias(Kmsinventory::class, 'Google_Service_Kmsinventory');
