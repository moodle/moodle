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
 * Service definition for Chromewebstore (v2).
 *
 * <p>
 * The Chrome Web Store API provides access to data about apps and extensions,
 * as well as developer tools for managing them.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developer.chrome.com/docs/webstore/api" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Chromewebstore extends \Google\Service
{
  /** See, edit, update, or publish your Chrome Web Store extensions, themes, apps, and licences you have access to. */
  const CHROMEWEBSTORE =
      "https://www.googleapis.com/auth/chromewebstore";
  /** See and download your Chrome Web Store extensions and apps, and see licenses you have access to. */
  const CHROMEWEBSTORE_READONLY =
      "https://www.googleapis.com/auth/chromewebstore.readonly";

  public $media;
  public $publishers_items;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the Chromewebstore service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://chromewebstore.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://chromewebstore.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v2';
    $this->serviceName = 'chromewebstore';

    $this->media = new Chromewebstore\Resource\Media(
        $this,
        $this->serviceName,
        'media',
        [
          'methods' => [
            'upload' => [
              'path' => 'v2/{+name}:upload',
              'httpMethod' => 'POST',
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
    $this->publishers_items = new Chromewebstore\Resource\PublishersItems(
        $this,
        $this->serviceName,
        'items',
        [
          'methods' => [
            'cancelSubmission' => [
              'path' => 'v2/{+name}:cancelSubmission',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'fetchStatus' => [
              'path' => 'v2/{+name}:fetchStatus',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'publish' => [
              'path' => 'v2/{+name}:publish',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'setPublishedDeployPercentage' => [
              'path' => 'v2/{+name}:setPublishedDeployPercentage',
              'httpMethod' => 'POST',
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
class_alias(Chromewebstore::class, 'Google_Service_Chromewebstore');
