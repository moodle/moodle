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
 * Service definition for PaymentsResellerSubscription (v1).
 *
 * <p>
</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/payments/reseller/subscription/" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class PaymentsResellerSubscription extends \Google\Service
{
  /** See and/or control the devices that you selected. */
  const SDM_SERVICE =
      "https://www.googleapis.com/auth/sdm.service";

  public $partners_products;
  public $partners_promotions;
  public $partners_subscriptions;
  public $partners_subscriptions_lineItems;
  public $partners_userSessions;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the PaymentsResellerSubscription
   * service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://paymentsresellersubscription.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://paymentsresellersubscription.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v1';
    $this->serviceName = 'paymentsresellersubscription';

    $this->partners_products = new PaymentsResellerSubscription\Resource\PartnersProducts(
        $this,
        $this->serviceName,
        'products',
        [
          'methods' => [
            'list' => [
              'path' => 'v1/{+parent}/products',
              'httpMethod' => 'GET',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'filter' => [
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
              ],
            ],
          ]
        ]
    );
    $this->partners_promotions = new PaymentsResellerSubscription\Resource\PartnersPromotions(
        $this,
        $this->serviceName,
        'promotions',
        [
          'methods' => [
            'findEligible' => [
              'path' => 'v1/{+parent}/promotions:findEligible',
              'httpMethod' => 'POST',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'v1/{+parent}/promotions',
              'httpMethod' => 'GET',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'filter' => [
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
              ],
            ],
          ]
        ]
    );
    $this->partners_subscriptions = new PaymentsResellerSubscription\Resource\PartnersSubscriptions(
        $this,
        $this->serviceName,
        'subscriptions',
        [
          'methods' => [
            'cancel' => [
              'path' => 'v1/{+name}:cancel',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'create' => [
              'path' => 'v1/{+parent}/subscriptions',
              'httpMethod' => 'POST',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'subscriptionId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'entitle' => [
              'path' => 'v1/{+name}:entitle',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'extend' => [
              'path' => 'v1/{+name}:extend',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'provision' => [
              'path' => 'v1/{+parent}/subscriptions:provision',
              'httpMethod' => 'POST',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'cycleOptions.initialCycleDuration.count' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'cycleOptions.initialCycleDuration.unit' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'subscriptionId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'resume' => [
              'path' => 'v1/{+name}:resume',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'suspend' => [
              'path' => 'v1/{+name}:suspend',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'undoCancel' => [
              'path' => 'v1/{+name}:undoCancel',
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
    $this->partners_subscriptions_lineItems = new PaymentsResellerSubscription\Resource\PartnersSubscriptionsLineItems(
        $this,
        $this->serviceName,
        'lineItems',
        [
          'methods' => [
            'patch' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'updateMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->partners_userSessions = new PaymentsResellerSubscription\Resource\PartnersUserSessions(
        $this,
        $this->serviceName,
        'userSessions',
        [
          'methods' => [
            'generate' => [
              'path' => 'v1/{+parent}/userSessions:generate',
              'httpMethod' => 'POST',
              'parameters' => [
                'parent' => [
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
class_alias(PaymentsResellerSubscription::class, 'Google_Service_PaymentsResellerSubscription');
