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
 * Service definition for SA360 (v0).
 *
 * <p>
 * The Search Ads 360 API allows developers to automate downloading reports from
 * Search Ads 360.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/search-ads/reporting" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class SA360 extends \Google\Service
{
  /** View and manage your advertising data in DoubleClick Search. */
  const DOUBLECLICKSEARCH =
      "https://www.googleapis.com/auth/doubleclicksearch";

  public $customers;
  public $customers_customColumns;
  public $customers_searchAds360;
  public $searchAds360Fields;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the SA360 service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://searchads360.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://searchads360.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v0';
    $this->serviceName = 'searchads360';

    $this->customers = new SA360\Resource\Customers(
        $this,
        $this->serviceName,
        'customers',
        [
          'methods' => [
            'listAccessibleCustomers' => [
              'path' => 'v0/customers:listAccessibleCustomers',
              'httpMethod' => 'GET',
              'parameters' => [],
            ],
          ]
        ]
    );
    $this->customers_customColumns = new SA360\Resource\CustomersCustomColumns(
        $this,
        $this->serviceName,
        'customColumns',
        [
          'methods' => [
            'get' => [
              'path' => 'v0/{+resourceName}',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceName' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'v0/customers/{+customerId}/customColumns',
              'httpMethod' => 'GET',
              'parameters' => [
                'customerId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->customers_searchAds360 = new SA360\Resource\CustomersSearchAds360(
        $this,
        $this->serviceName,
        'searchAds360',
        [
          'methods' => [
            'search' => [
              'path' => 'v0/customers/{+customerId}/searchAds360:search',
              'httpMethod' => 'POST',
              'parameters' => [
                'customerId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->searchAds360Fields = new SA360\Resource\SearchAds360Fields(
        $this,
        $this->serviceName,
        'searchAds360Fields',
        [
          'methods' => [
            'get' => [
              'path' => 'v0/{+resourceName}',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceName' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'search' => [
              'path' => 'v0/searchAds360Fields:search',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],
          ]
        ]
    );
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SA360::class, 'Google_Service_SA360');
