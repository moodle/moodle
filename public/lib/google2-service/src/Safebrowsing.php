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
 * Service definition for Safebrowsing (v5).
 *
 * <p>
 * Enables client applications to check web resources (most commonly URLs)
 * against Google-generated lists of unsafe web resources. The Safe Browsing
 * APIs are for non-commercial use only. If you need to use APIs to detect
 * malicious URLs for commercial purposes – meaning “for sale or revenue-
 * generating purposes” – please refer to the Web Risk API.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/safe-browsing/" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Safebrowsing extends \Google\Service
{


  public $hashList;
  public $hashLists;
  public $hashes;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the Safebrowsing service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://safebrowsing.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://safebrowsing.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v5';
    $this->serviceName = 'safebrowsing';

    $this->hashList = new Safebrowsing\Resource\HashList(
        $this,
        $this->serviceName,
        'hashList',
        [
          'methods' => [
            'get' => [
              'path' => 'v5/hashList/{name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'sizeConstraints.maxDatabaseEntries' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'sizeConstraints.maxUpdateEntries' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'version' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->hashLists = new Safebrowsing\Resource\HashLists(
        $this,
        $this->serviceName,
        'hashLists',
        [
          'methods' => [
            'batchGet' => [
              'path' => 'v5/hashLists:batchGet',
              'httpMethod' => 'GET',
              'parameters' => [
                'names' => [
                  'location' => 'query',
                  'type' => 'string',
                  'repeated' => true,
                ],
                'sizeConstraints.maxDatabaseEntries' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'sizeConstraints.maxUpdateEntries' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'version' => [
                  'location' => 'query',
                  'type' => 'string',
                  'repeated' => true,
                ],
              ],
            ],'list' => [
              'path' => 'v5/hashLists',
              'httpMethod' => 'GET',
              'parameters' => [
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
    $this->hashes = new Safebrowsing\Resource\Hashes(
        $this,
        $this->serviceName,
        'hashes',
        [
          'methods' => [
            'search' => [
              'path' => 'v5/hashes:search',
              'httpMethod' => 'GET',
              'parameters' => [
                'hashPrefixes' => [
                  'location' => 'query',
                  'type' => 'string',
                  'repeated' => true,
                ],
              ],
            ],
          ]
        ]
    );
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Safebrowsing::class, 'Google_Service_Safebrowsing');
