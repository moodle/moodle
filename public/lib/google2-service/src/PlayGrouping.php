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
 * Service definition for PlayGrouping (v1alpha1).
 *
 * <p>
 * playgrouping.googleapis.com API.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://cloud.google.com/playgrouping/" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class PlayGrouping extends \Google\Service
{


  public $apps_tokens;
  public $apps_tokens_tags;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the PlayGrouping service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://playgrouping.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://playgrouping.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v1alpha1';
    $this->serviceName = 'playgrouping';

    $this->apps_tokens = new PlayGrouping\Resource\AppsTokens(
        $this,
        $this->serviceName,
        'tokens',
        [
          'methods' => [
            'verify' => [
              'path' => 'v1alpha1/{+appPackage}/{+token}:verify',
              'httpMethod' => 'POST',
              'parameters' => [
                'appPackage' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'token' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->apps_tokens_tags = new PlayGrouping\Resource\AppsTokensTags(
        $this,
        $this->serviceName,
        'tags',
        [
          'methods' => [
            'createOrUpdate' => [
              'path' => 'v1alpha1/{+appPackage}/{+token}/tags:createOrUpdate',
              'httpMethod' => 'POST',
              'parameters' => [
                'appPackage' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'token' => [
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
class_alias(PlayGrouping::class, 'Google_Service_PlayGrouping');
