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
 * Service definition for ShoppingContent (v2.1).
 *
 * <p>
 * This API is deprecated. Please use Merchant API instead:
 * https://developers.google.com/merchant/api.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/shopping-content/v2/" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class ShoppingContent extends \Google\Service
{
  /** Manage your product listings and accounts for Google Shopping. */
  const CONTENT =
      "https://www.googleapis.com/auth/content";

  public $accounts;
  public $accounts_credentials;
  public $accounts_labels;
  public $accounts_returncarrier;
  public $accountstatuses;
  public $accounttax;
  public $collections;
  public $collectionstatuses;
  public $conversionsources;
  public $csses;
  public $datafeeds;
  public $datafeedstatuses;
  public $freelistingsprogram;
  public $freelistingsprogram_checkoutsettings;
  public $liasettings;
  public $localinventory;
  public $merchantsupport;
  public $ordertrackingsignals;
  public $pos;
  public $productdeliverytime;
  public $products;
  public $productstatuses;
  public $promotions;
  public $pubsubnotificationsettings;
  public $quotas;
  public $recommendations;
  public $regionalinventory;
  public $regions;
  public $reports;
  public $returnpolicyonline;
  public $shippingsettings;
  public $shoppingadsprogram;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the ShoppingContent service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://shoppingcontent.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://shoppingcontent.UNIVERSE_DOMAIN/';
    $this->servicePath = 'content/v2.1/';
    $this->batchPath = 'batch';
    $this->version = 'v2.1';
    $this->serviceName = 'content';

    $this->accounts = new ShoppingContent\Resource\Accounts(
        $this,
        $this->serviceName,
        'accounts',
        [
          'methods' => [
            'authinfo' => [
              'path' => 'accounts/authinfo',
              'httpMethod' => 'GET',
              'parameters' => [],
            ],'claimwebsite' => [
              'path' => '{merchantId}/accounts/{accountId}/claimwebsite',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'overwrite' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
              ],
            ],'custombatch' => [
              'path' => 'accounts/batch',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'delete' => [
              'path' => '{merchantId}/accounts/{accountId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'force' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
              ],
            ],'get' => [
              'path' => '{merchantId}/accounts/{accountId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'view' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'insert' => [
              'path' => '{merchantId}/accounts',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'link' => [
              'path' => '{merchantId}/accounts/{accountId}/link',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => '{merchantId}/accounts',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'label' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'name' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'view' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'listlinks' => [
              'path' => '{merchantId}/accounts/{accountId}/listlinks',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'requestphoneverification' => [
              'path' => '{merchantId}/accounts/{accountId}/requestphoneverification',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'update' => [
              'path' => '{merchantId}/accounts/{accountId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'updatelabels' => [
              'path' => '{merchantId}/accounts/{accountId}/updatelabels',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'verifyphonenumber' => [
              'path' => '{merchantId}/accounts/{accountId}/verifyphonenumber',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->accounts_credentials = new ShoppingContent\Resource\AccountsCredentials(
        $this,
        $this->serviceName,
        'credentials',
        [
          'methods' => [
            'create' => [
              'path' => 'accounts/{accountId}/credentials',
              'httpMethod' => 'POST',
              'parameters' => [
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->accounts_labels = new ShoppingContent\Resource\AccountsLabels(
        $this,
        $this->serviceName,
        'labels',
        [
          'methods' => [
            'create' => [
              'path' => 'accounts/{accountId}/labels',
              'httpMethod' => 'POST',
              'parameters' => [
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'delete' => [
              'path' => 'accounts/{accountId}/labels/{labelId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'labelId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'accounts/{accountId}/labels',
              'httpMethod' => 'GET',
              'parameters' => [
                'accountId' => [
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
            ],'patch' => [
              'path' => 'accounts/{accountId}/labels/{labelId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'labelId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->accounts_returncarrier = new ShoppingContent\Resource\AccountsReturncarrier(
        $this,
        $this->serviceName,
        'returncarrier',
        [
          'methods' => [
            'create' => [
              'path' => 'accounts/{accountId}/returncarrier',
              'httpMethod' => 'POST',
              'parameters' => [
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'delete' => [
              'path' => 'accounts/{accountId}/returncarrier/{carrierAccountId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'carrierAccountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'accounts/{accountId}/returncarrier',
              'httpMethod' => 'GET',
              'parameters' => [
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'patch' => [
              'path' => 'accounts/{accountId}/returncarrier/{carrierAccountId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'carrierAccountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->accountstatuses = new ShoppingContent\Resource\Accountstatuses(
        $this,
        $this->serviceName,
        'accountstatuses',
        [
          'methods' => [
            'custombatch' => [
              'path' => 'accountstatuses/batch',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'get' => [
              'path' => '{merchantId}/accountstatuses/{accountId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'destinations' => [
                  'location' => 'query',
                  'type' => 'string',
                  'repeated' => true,
                ],
              ],
            ],'list' => [
              'path' => '{merchantId}/accountstatuses',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'destinations' => [
                  'location' => 'query',
                  'type' => 'string',
                  'repeated' => true,
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'name' => [
                  'location' => 'query',
                  'type' => 'string',
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
    $this->accounttax = new ShoppingContent\Resource\Accounttax(
        $this,
        $this->serviceName,
        'accounttax',
        [
          'methods' => [
            'custombatch' => [
              'path' => 'accounttax/batch',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'get' => [
              'path' => '{merchantId}/accounttax/{accountId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => '{merchantId}/accounttax',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'update' => [
              'path' => '{merchantId}/accounttax/{accountId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->collections = new ShoppingContent\Resource\Collections(
        $this,
        $this->serviceName,
        'collections',
        [
          'methods' => [
            'create' => [
              'path' => '{merchantId}/collections',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'delete' => [
              'path' => '{merchantId}/collections/{collectionId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'collectionId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => '{merchantId}/collections/{collectionId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'collectionId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => '{merchantId}/collections',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
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
    $this->collectionstatuses = new ShoppingContent\Resource\Collectionstatuses(
        $this,
        $this->serviceName,
        'collectionstatuses',
        [
          'methods' => [
            'get' => [
              'path' => '{merchantId}/collectionstatuses/{collectionId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'collectionId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => '{merchantId}/collectionstatuses',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
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
    $this->conversionsources = new ShoppingContent\Resource\Conversionsources(
        $this,
        $this->serviceName,
        'conversionsources',
        [
          'methods' => [
            'create' => [
              'path' => '{merchantId}/conversionsources',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'delete' => [
              'path' => '{merchantId}/conversionsources/{conversionSourceId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'conversionSourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => '{merchantId}/conversionsources/{conversionSourceId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'conversionSourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => '{merchantId}/conversionsources',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
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
                'showDeleted' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
              ],
            ],'patch' => [
              'path' => '{merchantId}/conversionsources/{conversionSourceId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'conversionSourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'updateMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'undelete' => [
              'path' => '{merchantId}/conversionsources/{conversionSourceId}:undelete',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'conversionSourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->csses = new ShoppingContent\Resource\Csses(
        $this,
        $this->serviceName,
        'csses',
        [
          'methods' => [
            'get' => [
              'path' => '{cssGroupId}/csses/{cssDomainId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'cssGroupId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'cssDomainId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => '{cssGroupId}/csses',
              'httpMethod' => 'GET',
              'parameters' => [
                'cssGroupId' => [
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
            ],'updatelabels' => [
              'path' => '{cssGroupId}/csses/{cssDomainId}/updatelabels',
              'httpMethod' => 'POST',
              'parameters' => [
                'cssGroupId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'cssDomainId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->datafeeds = new ShoppingContent\Resource\Datafeeds(
        $this,
        $this->serviceName,
        'datafeeds',
        [
          'methods' => [
            'custombatch' => [
              'path' => 'datafeeds/batch',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'delete' => [
              'path' => '{merchantId}/datafeeds/{datafeedId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'datafeedId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'fetchnow' => [
              'path' => '{merchantId}/datafeeds/{datafeedId}/fetchNow',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'datafeedId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => '{merchantId}/datafeeds/{datafeedId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'datafeedId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => '{merchantId}/datafeeds',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => '{merchantId}/datafeeds',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'update' => [
              'path' => '{merchantId}/datafeeds/{datafeedId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'datafeedId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->datafeedstatuses = new ShoppingContent\Resource\Datafeedstatuses(
        $this,
        $this->serviceName,
        'datafeedstatuses',
        [
          'methods' => [
            'custombatch' => [
              'path' => 'datafeedstatuses/batch',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'get' => [
              'path' => '{merchantId}/datafeedstatuses/{datafeedId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'datafeedId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'country' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'feedLabel' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'language' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'list' => [
              'path' => '{merchantId}/datafeedstatuses',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'maxResults' => [
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
    $this->freelistingsprogram = new ShoppingContent\Resource\Freelistingsprogram(
        $this,
        $this->serviceName,
        'freelistingsprogram',
        [
          'methods' => [
            'get' => [
              'path' => '{merchantId}/freelistingsprogram',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'requestreview' => [
              'path' => '{merchantId}/freelistingsprogram/requestreview',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->freelistingsprogram_checkoutsettings = new ShoppingContent\Resource\FreelistingsprogramCheckoutsettings(
        $this,
        $this->serviceName,
        'checkoutsettings',
        [
          'methods' => [
            'delete' => [
              'path' => '{merchantId}/freelistingsprogram/checkoutsettings',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => '{merchantId}/freelistingsprogram/checkoutsettings',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => '{merchantId}/freelistingsprogram/checkoutsettings',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->liasettings = new ShoppingContent\Resource\Liasettings(
        $this,
        $this->serviceName,
        'liasettings',
        [
          'methods' => [
            'custombatch' => [
              'path' => 'liasettings/batch',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'get' => [
              'path' => '{merchantId}/liasettings/{accountId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'getaccessiblegmbaccounts' => [
              'path' => '{merchantId}/liasettings/{accountId}/accessiblegmbaccounts',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => '{merchantId}/liasettings',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'listposdataproviders' => [
              'path' => 'liasettings/posdataproviders',
              'httpMethod' => 'GET',
              'parameters' => [],
            ],'requestgmbaccess' => [
              'path' => '{merchantId}/liasettings/{accountId}/requestgmbaccess',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'gmbEmail' => [
                  'location' => 'query',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'requestinventoryverification' => [
              'path' => '{merchantId}/liasettings/{accountId}/requestinventoryverification/{country}',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'country' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'setinventoryverificationcontact' => [
              'path' => '{merchantId}/liasettings/{accountId}/setinventoryverificationcontact',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'country' => [
                  'location' => 'query',
                  'type' => 'string',
                  'required' => true,
                ],
                'language' => [
                  'location' => 'query',
                  'type' => 'string',
                  'required' => true,
                ],
                'contactName' => [
                  'location' => 'query',
                  'type' => 'string',
                  'required' => true,
                ],
                'contactEmail' => [
                  'location' => 'query',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'setomnichannelexperience' => [
              'path' => '{merchantId}/liasettings/{accountId}/setomnichannelexperience',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'country' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'lsfType' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pickupTypes' => [
                  'location' => 'query',
                  'type' => 'string',
                  'repeated' => true,
                ],
              ],
            ],'setposdataprovider' => [
              'path' => '{merchantId}/liasettings/{accountId}/setposdataprovider',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'country' => [
                  'location' => 'query',
                  'type' => 'string',
                  'required' => true,
                ],
                'posDataProviderId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'posExternalAccountId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'update' => [
              'path' => '{merchantId}/liasettings/{accountId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->localinventory = new ShoppingContent\Resource\Localinventory(
        $this,
        $this->serviceName,
        'localinventory',
        [
          'methods' => [
            'custombatch' => [
              'path' => 'localinventory/batch',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'insert' => [
              'path' => '{merchantId}/products/{productId}/localinventory',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'productId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->merchantsupport = new ShoppingContent\Resource\Merchantsupport(
        $this,
        $this->serviceName,
        'merchantsupport',
        [
          'methods' => [
            'renderaccountissues' => [
              'path' => '{merchantId}/merchantsupport/renderaccountissues',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'languageCode' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'timeZone' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'renderproductissues' => [
              'path' => '{merchantId}/merchantsupport/renderproductissues/{productId}',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'productId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'languageCode' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'timeZone' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'triggeraction' => [
              'path' => '{merchantId}/merchantsupport/triggeraction',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'languageCode' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->ordertrackingsignals = new ShoppingContent\Resource\Ordertrackingsignals(
        $this,
        $this->serviceName,
        'ordertrackingsignals',
        [
          'methods' => [
            'create' => [
              'path' => '{merchantId}/ordertrackingsignals',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->pos = new ShoppingContent\Resource\Pos(
        $this,
        $this->serviceName,
        'pos',
        [
          'methods' => [
            'custombatch' => [
              'path' => 'pos/batch',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'delete' => [
              'path' => '{merchantId}/pos/{targetMerchantId}/store/{storeCode}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'targetMerchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'storeCode' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => '{merchantId}/pos/{targetMerchantId}/store/{storeCode}',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'targetMerchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'storeCode' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => '{merchantId}/pos/{targetMerchantId}/store',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'targetMerchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'inventory' => [
              'path' => '{merchantId}/pos/{targetMerchantId}/inventory',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'targetMerchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => '{merchantId}/pos/{targetMerchantId}/store',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'targetMerchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'sale' => [
              'path' => '{merchantId}/pos/{targetMerchantId}/sale',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'targetMerchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->productdeliverytime = new ShoppingContent\Resource\Productdeliverytime(
        $this,
        $this->serviceName,
        'productdeliverytime',
        [
          'methods' => [
            'create' => [
              'path' => '{merchantId}/productdeliverytime',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'delete' => [
              'path' => '{merchantId}/productdeliverytime/{productId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'productId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => '{merchantId}/productdeliverytime/{productId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'productId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->products = new ShoppingContent\Resource\Products(
        $this,
        $this->serviceName,
        'products',
        [
          'methods' => [
            'custombatch' => [
              'path' => 'products/batch',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'delete' => [
              'path' => '{merchantId}/products/{productId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'productId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'feedId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'get' => [
              'path' => '{merchantId}/products/{productId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'productId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => '{merchantId}/products',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'feedId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'list' => [
              'path' => '{merchantId}/products',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'update' => [
              'path' => '{merchantId}/products/{productId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'productId' => [
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
    $this->productstatuses = new ShoppingContent\Resource\Productstatuses(
        $this,
        $this->serviceName,
        'productstatuses',
        [
          'methods' => [
            'custombatch' => [
              'path' => 'productstatuses/batch',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'get' => [
              'path' => '{merchantId}/productstatuses/{productId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'productId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'destinations' => [
                  'location' => 'query',
                  'type' => 'string',
                  'repeated' => true,
                ],
              ],
            ],'list' => [
              'path' => '{merchantId}/productstatuses',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'destinations' => [
                  'location' => 'query',
                  'type' => 'string',
                  'repeated' => true,
                ],
                'maxResults' => [
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
    $this->promotions = new ShoppingContent\Resource\Promotions(
        $this,
        $this->serviceName,
        'promotions',
        [
          'methods' => [
            'create' => [
              'path' => '{merchantId}/promotions',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => '{merchantId}/promotions/{id}',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'id' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => '{merchantId}/promotions',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'countryCode' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'languageCode' => [
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
    $this->pubsubnotificationsettings = new ShoppingContent\Resource\Pubsubnotificationsettings(
        $this,
        $this->serviceName,
        'pubsubnotificationsettings',
        [
          'methods' => [
            'get' => [
              'path' => '{merchantId}/pubsubnotificationsettings',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'update' => [
              'path' => '{merchantId}/pubsubnotificationsettings',
              'httpMethod' => 'PUT',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->quotas = new ShoppingContent\Resource\Quotas(
        $this,
        $this->serviceName,
        'quotas',
        [
          'methods' => [
            'list' => [
              'path' => '{merchantId}/quotas',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
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
    $this->recommendations = new ShoppingContent\Resource\Recommendations(
        $this,
        $this->serviceName,
        'recommendations',
        [
          'methods' => [
            'generate' => [
              'path' => '{merchantId}/recommendations/generate',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'allowedTag' => [
                  'location' => 'query',
                  'type' => 'string',
                  'repeated' => true,
                ],
                'languageCode' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'reportInteraction' => [
              'path' => '{merchantId}/recommendations/reportInteraction',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->regionalinventory = new ShoppingContent\Resource\Regionalinventory(
        $this,
        $this->serviceName,
        'regionalinventory',
        [
          'methods' => [
            'custombatch' => [
              'path' => 'regionalinventory/batch',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'insert' => [
              'path' => '{merchantId}/products/{productId}/regionalinventory',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'productId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->regions = new ShoppingContent\Resource\Regions(
        $this,
        $this->serviceName,
        'regions',
        [
          'methods' => [
            'create' => [
              'path' => '{merchantId}/regions',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'regionId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'delete' => [
              'path' => '{merchantId}/regions/{regionId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'regionId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => '{merchantId}/regions/{regionId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'regionId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => '{merchantId}/regions',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
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
            ],'patch' => [
              'path' => '{merchantId}/regions/{regionId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'regionId' => [
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
    $this->reports = new ShoppingContent\Resource\Reports(
        $this,
        $this->serviceName,
        'reports',
        [
          'methods' => [
            'search' => [
              'path' => '{merchantId}/reports/search',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->returnpolicyonline = new ShoppingContent\Resource\Returnpolicyonline(
        $this,
        $this->serviceName,
        'returnpolicyonline',
        [
          'methods' => [
            'create' => [
              'path' => '{merchantId}/returnpolicyonline',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'delete' => [
              'path' => '{merchantId}/returnpolicyonline/{returnPolicyId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'returnPolicyId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => '{merchantId}/returnpolicyonline/{returnPolicyId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'returnPolicyId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => '{merchantId}/returnpolicyonline',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'patch' => [
              'path' => '{merchantId}/returnpolicyonline/{returnPolicyId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'returnPolicyId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->shippingsettings = new ShoppingContent\Resource\Shippingsettings(
        $this,
        $this->serviceName,
        'shippingsettings',
        [
          'methods' => [
            'custombatch' => [
              'path' => 'shippingsettings/batch',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'get' => [
              'path' => '{merchantId}/shippingsettings/{accountId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'getsupportedcarriers' => [
              'path' => '{merchantId}/supportedCarriers',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'getsupportedholidays' => [
              'path' => '{merchantId}/supportedHolidays',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'getsupportedpickupservices' => [
              'path' => '{merchantId}/supportedPickupServices',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => '{merchantId}/shippingsettings',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'update' => [
              'path' => '{merchantId}/shippingsettings/{accountId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'accountId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->shoppingadsprogram = new ShoppingContent\Resource\Shoppingadsprogram(
        $this,
        $this->serviceName,
        'shoppingadsprogram',
        [
          'methods' => [
            'get' => [
              'path' => '{merchantId}/shoppingadsprogram',
              'httpMethod' => 'GET',
              'parameters' => [
                'merchantId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'requestreview' => [
              'path' => '{merchantId}/shoppingadsprogram/requestreview',
              'httpMethod' => 'POST',
              'parameters' => [
                'merchantId' => [
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
class_alias(ShoppingContent::class, 'Google_Service_ShoppingContent');
