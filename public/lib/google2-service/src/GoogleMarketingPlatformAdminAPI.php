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
 * Service definition for GoogleMarketingPlatformAdminAPI (v1alpha).
 *
 * <p>
 * The Google Marketing Platform Admin API allows for programmatic access to the
 * Google Marketing Platform configuration data. You can use the Google
 * Marketing Platform Admin API to manage links between your Google Marketing
 * Platform organization and Google Analytics accounts, and to set the service
 * level of your GA4 properties.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/analytics/devguides/config/gmp/v1" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class GoogleMarketingPlatformAdminAPI extends \Google\Service
{
  /** View your Google Analytics product account data in GMP home. */
  const MARKETINGPLATFORMADMIN_ANALYTICS_READ =
      "https://www.googleapis.com/auth/marketingplatformadmin.analytics.read";
  /** Manage your Google Analytics product account data in GMP home. */
  const MARKETINGPLATFORMADMIN_ANALYTICS_UPDATE =
      "https://www.googleapis.com/auth/marketingplatformadmin.analytics.update";

  public $organizations;
  public $organizations_analyticsAccountLinks;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the
   * GoogleMarketingPlatformAdminAPI service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://marketingplatformadmin.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://marketingplatformadmin.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v1alpha';
    $this->serviceName = 'marketingplatformadmin';

    $this->organizations = new GoogleMarketingPlatformAdminAPI\Resource\Organizations(
        $this,
        $this->serviceName,
        'organizations',
        [
          'methods' => [
            'findSalesPartnerManagedClients' => [
              'path' => 'v1alpha/{+organization}:findSalesPartnerManagedClients',
              'httpMethod' => 'POST',
              'parameters' => [
                'organization' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'v1alpha/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'v1alpha/organizations',
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
            ],'reportPropertyUsage' => [
              'path' => 'v1alpha/{+organization}:reportPropertyUsage',
              'httpMethod' => 'POST',
              'parameters' => [
                'organization' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->organizations_analyticsAccountLinks = new GoogleMarketingPlatformAdminAPI\Resource\OrganizationsAnalyticsAccountLinks(
        $this,
        $this->serviceName,
        'analyticsAccountLinks',
        [
          'methods' => [
            'create' => [
              'path' => 'v1alpha/{+parent}/analyticsAccountLinks',
              'httpMethod' => 'POST',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'delete' => [
              'path' => 'v1alpha/{+name}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'v1alpha/{+parent}/analyticsAccountLinks',
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
            ],'setPropertyServiceLevel' => [
              'path' => 'v1alpha/{+analyticsAccountLink}:setPropertyServiceLevel',
              'httpMethod' => 'POST',
              'parameters' => [
                'analyticsAccountLink' => [
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
class_alias(GoogleMarketingPlatformAdminAPI::class, 'Google_Service_GoogleMarketingPlatformAdminAPI');
