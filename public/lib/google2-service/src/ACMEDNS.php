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
 * Service definition for ACMEDNS (v1).
 *
 * <p>
 * Google Domains ACME DNS API that allows users to complete ACME DNS-01
 * challenges for a domain.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/domains/acme-dns/" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class ACMEDNS extends \Google\Service
{


  public $acmeChallengeSets;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the ACMEDNS service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://acmedns.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://acmedns.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v1';
    $this->serviceName = 'acmedns';

    $this->acmeChallengeSets = new ACMEDNS\Resource\AcmeChallengeSets(
        $this,
        $this->serviceName,
        'acmeChallengeSets',
        [
          'methods' => [
            'get' => [
              'path' => 'v1/acmeChallengeSets/{rootDomain}',
              'httpMethod' => 'GET',
              'parameters' => [
                'rootDomain' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'rotateChallenges' => [
              'path' => 'v1/acmeChallengeSets/{rootDomain}:rotateChallenges',
              'httpMethod' => 'POST',
              'parameters' => [
                'rootDomain' => [
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
class_alias(ACMEDNS::class, 'Google_Service_ACMEDNS');
