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
 * Service definition for Walletobjects (v1).
 *
 * <p>
 * API for issuers to save and manage Google Wallet Objects.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/pay/passes" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Walletobjects extends \Google\Service
{
  /** Private Service: https://www.googleapis.com/auth/wallet_object.issuer. */
  const WALLET_OBJECT_ISSUER =
      "https://www.googleapis.com/auth/wallet_object.issuer";

  public $eventticketclass;
  public $eventticketobject;
  public $flightclass;
  public $flightobject;
  public $genericclass;
  public $genericobject;
  public $giftcardclass;
  public $giftcardobject;
  public $issuer;
  public $jwt;
  public $loyaltyclass;
  public $loyaltyobject;
  public $media;
  public $offerclass;
  public $offerobject;
  public $permissions;
  public $smarttap;
  public $transitclass;
  public $transitobject;
  public $walletobjects_v1_privateContent;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the Walletobjects service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://walletobjects.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://walletobjects.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v1';
    $this->serviceName = 'walletobjects';

    $this->eventticketclass = new Walletobjects\Resource\Eventticketclass(
        $this,
        $this->serviceName,
        'eventticketclass',
        [
          'methods' => [
            'addmessage' => [
              'path' => 'walletobjects/v1/eventTicketClass/{resourceId}/addMessage',
              'httpMethod' => 'POST',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'walletobjects/v1/eventTicketClass/{resourceId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => 'walletobjects/v1/eventTicketClass',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'list' => [
              'path' => 'walletobjects/v1/eventTicketClass',
              'httpMethod' => 'GET',
              'parameters' => [
                'issuerId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'token' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'walletobjects/v1/eventTicketClass/{resourceId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'update' => [
              'path' => 'walletobjects/v1/eventTicketClass/{resourceId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->eventticketobject = new Walletobjects\Resource\Eventticketobject(
        $this,
        $this->serviceName,
        'eventticketobject',
        [
          'methods' => [
            'addmessage' => [
              'path' => 'walletobjects/v1/eventTicketObject/{resourceId}/addMessage',
              'httpMethod' => 'POST',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'walletobjects/v1/eventTicketObject/{resourceId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => 'walletobjects/v1/eventTicketObject',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'list' => [
              'path' => 'walletobjects/v1/eventTicketObject',
              'httpMethod' => 'GET',
              'parameters' => [
                'classId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'token' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'modifylinkedofferobjects' => [
              'path' => 'walletobjects/v1/eventTicketObject/{resourceId}/modifyLinkedOfferObjects',
              'httpMethod' => 'POST',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'patch' => [
              'path' => 'walletobjects/v1/eventTicketObject/{resourceId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'update' => [
              'path' => 'walletobjects/v1/eventTicketObject/{resourceId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->flightclass = new Walletobjects\Resource\Flightclass(
        $this,
        $this->serviceName,
        'flightclass',
        [
          'methods' => [
            'addmessage' => [
              'path' => 'walletobjects/v1/flightClass/{resourceId}/addMessage',
              'httpMethod' => 'POST',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'walletobjects/v1/flightClass/{resourceId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => 'walletobjects/v1/flightClass',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'list' => [
              'path' => 'walletobjects/v1/flightClass',
              'httpMethod' => 'GET',
              'parameters' => [
                'issuerId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'token' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'walletobjects/v1/flightClass/{resourceId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'update' => [
              'path' => 'walletobjects/v1/flightClass/{resourceId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->flightobject = new Walletobjects\Resource\Flightobject(
        $this,
        $this->serviceName,
        'flightobject',
        [
          'methods' => [
            'addmessage' => [
              'path' => 'walletobjects/v1/flightObject/{resourceId}/addMessage',
              'httpMethod' => 'POST',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'walletobjects/v1/flightObject/{resourceId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => 'walletobjects/v1/flightObject',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'list' => [
              'path' => 'walletobjects/v1/flightObject',
              'httpMethod' => 'GET',
              'parameters' => [
                'classId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'token' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'walletobjects/v1/flightObject/{resourceId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'update' => [
              'path' => 'walletobjects/v1/flightObject/{resourceId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->genericclass = new Walletobjects\Resource\Genericclass(
        $this,
        $this->serviceName,
        'genericclass',
        [
          'methods' => [
            'addmessage' => [
              'path' => 'walletobjects/v1/genericClass/{resourceId}/addMessage',
              'httpMethod' => 'POST',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'walletobjects/v1/genericClass/{resourceId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => 'walletobjects/v1/genericClass',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'list' => [
              'path' => 'walletobjects/v1/genericClass',
              'httpMethod' => 'GET',
              'parameters' => [
                'issuerId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'token' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'walletobjects/v1/genericClass/{resourceId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'update' => [
              'path' => 'walletobjects/v1/genericClass/{resourceId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->genericobject = new Walletobjects\Resource\Genericobject(
        $this,
        $this->serviceName,
        'genericobject',
        [
          'methods' => [
            'addmessage' => [
              'path' => 'walletobjects/v1/genericObject/{resourceId}/addMessage',
              'httpMethod' => 'POST',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'walletobjects/v1/genericObject/{resourceId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => 'walletobjects/v1/genericObject',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'list' => [
              'path' => 'walletobjects/v1/genericObject',
              'httpMethod' => 'GET',
              'parameters' => [
                'classId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'token' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'walletobjects/v1/genericObject/{resourceId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'update' => [
              'path' => 'walletobjects/v1/genericObject/{resourceId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->giftcardclass = new Walletobjects\Resource\Giftcardclass(
        $this,
        $this->serviceName,
        'giftcardclass',
        [
          'methods' => [
            'addmessage' => [
              'path' => 'walletobjects/v1/giftCardClass/{resourceId}/addMessage',
              'httpMethod' => 'POST',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'walletobjects/v1/giftCardClass/{resourceId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => 'walletobjects/v1/giftCardClass',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'list' => [
              'path' => 'walletobjects/v1/giftCardClass',
              'httpMethod' => 'GET',
              'parameters' => [
                'issuerId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'token' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'walletobjects/v1/giftCardClass/{resourceId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'update' => [
              'path' => 'walletobjects/v1/giftCardClass/{resourceId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->giftcardobject = new Walletobjects\Resource\Giftcardobject(
        $this,
        $this->serviceName,
        'giftcardobject',
        [
          'methods' => [
            'addmessage' => [
              'path' => 'walletobjects/v1/giftCardObject/{resourceId}/addMessage',
              'httpMethod' => 'POST',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'walletobjects/v1/giftCardObject/{resourceId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => 'walletobjects/v1/giftCardObject',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'list' => [
              'path' => 'walletobjects/v1/giftCardObject',
              'httpMethod' => 'GET',
              'parameters' => [
                'classId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'token' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'walletobjects/v1/giftCardObject/{resourceId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'update' => [
              'path' => 'walletobjects/v1/giftCardObject/{resourceId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->issuer = new Walletobjects\Resource\Issuer(
        $this,
        $this->serviceName,
        'issuer',
        [
          'methods' => [
            'get' => [
              'path' => 'walletobjects/v1/issuer/{resourceId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => 'walletobjects/v1/issuer',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'list' => [
              'path' => 'walletobjects/v1/issuer',
              'httpMethod' => 'GET',
              'parameters' => [],
            ],'patch' => [
              'path' => 'walletobjects/v1/issuer/{resourceId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'update' => [
              'path' => 'walletobjects/v1/issuer/{resourceId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->jwt = new Walletobjects\Resource\Jwt(
        $this,
        $this->serviceName,
        'jwt',
        [
          'methods' => [
            'insert' => [
              'path' => 'walletobjects/v1/jwt',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],
          ]
        ]
    );
    $this->loyaltyclass = new Walletobjects\Resource\Loyaltyclass(
        $this,
        $this->serviceName,
        'loyaltyclass',
        [
          'methods' => [
            'addmessage' => [
              'path' => 'walletobjects/v1/loyaltyClass/{resourceId}/addMessage',
              'httpMethod' => 'POST',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'walletobjects/v1/loyaltyClass/{resourceId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => 'walletobjects/v1/loyaltyClass',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'list' => [
              'path' => 'walletobjects/v1/loyaltyClass',
              'httpMethod' => 'GET',
              'parameters' => [
                'issuerId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'token' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'walletobjects/v1/loyaltyClass/{resourceId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'update' => [
              'path' => 'walletobjects/v1/loyaltyClass/{resourceId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->loyaltyobject = new Walletobjects\Resource\Loyaltyobject(
        $this,
        $this->serviceName,
        'loyaltyobject',
        [
          'methods' => [
            'addmessage' => [
              'path' => 'walletobjects/v1/loyaltyObject/{resourceId}/addMessage',
              'httpMethod' => 'POST',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'walletobjects/v1/loyaltyObject/{resourceId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => 'walletobjects/v1/loyaltyObject',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'list' => [
              'path' => 'walletobjects/v1/loyaltyObject',
              'httpMethod' => 'GET',
              'parameters' => [
                'classId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'token' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'modifylinkedofferobjects' => [
              'path' => 'walletobjects/v1/loyaltyObject/{resourceId}/modifyLinkedOfferObjects',
              'httpMethod' => 'POST',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'patch' => [
              'path' => 'walletobjects/v1/loyaltyObject/{resourceId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'update' => [
              'path' => 'walletobjects/v1/loyaltyObject/{resourceId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->media = new Walletobjects\Resource\Media(
        $this,
        $this->serviceName,
        'media',
        [
          'methods' => [
            'download' => [
              'path' => 'walletobjects/v1/transitObject/{resourceId}/downloadRotatingBarcodeValues',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'upload' => [
              'path' => 'walletobjects/v1/transitObject/{resourceId}/uploadRotatingBarcodeValues',
              'httpMethod' => 'POST',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->offerclass = new Walletobjects\Resource\Offerclass(
        $this,
        $this->serviceName,
        'offerclass',
        [
          'methods' => [
            'addmessage' => [
              'path' => 'walletobjects/v1/offerClass/{resourceId}/addMessage',
              'httpMethod' => 'POST',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'walletobjects/v1/offerClass/{resourceId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => 'walletobjects/v1/offerClass',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'list' => [
              'path' => 'walletobjects/v1/offerClass',
              'httpMethod' => 'GET',
              'parameters' => [
                'issuerId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'token' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'walletobjects/v1/offerClass/{resourceId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'update' => [
              'path' => 'walletobjects/v1/offerClass/{resourceId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->offerobject = new Walletobjects\Resource\Offerobject(
        $this,
        $this->serviceName,
        'offerobject',
        [
          'methods' => [
            'addmessage' => [
              'path' => 'walletobjects/v1/offerObject/{resourceId}/addMessage',
              'httpMethod' => 'POST',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'walletobjects/v1/offerObject/{resourceId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => 'walletobjects/v1/offerObject',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'list' => [
              'path' => 'walletobjects/v1/offerObject',
              'httpMethod' => 'GET',
              'parameters' => [
                'classId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'token' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'walletobjects/v1/offerObject/{resourceId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'update' => [
              'path' => 'walletobjects/v1/offerObject/{resourceId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->permissions = new Walletobjects\Resource\Permissions(
        $this,
        $this->serviceName,
        'permissions',
        [
          'methods' => [
            'get' => [
              'path' => 'walletobjects/v1/permissions/{resourceId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'update' => [
              'path' => 'walletobjects/v1/permissions/{resourceId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->smarttap = new Walletobjects\Resource\Smarttap(
        $this,
        $this->serviceName,
        'smarttap',
        [
          'methods' => [
            'insert' => [
              'path' => 'walletobjects/v1/smartTap',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],
          ]
        ]
    );
    $this->transitclass = new Walletobjects\Resource\Transitclass(
        $this,
        $this->serviceName,
        'transitclass',
        [
          'methods' => [
            'addmessage' => [
              'path' => 'walletobjects/v1/transitClass/{resourceId}/addMessage',
              'httpMethod' => 'POST',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'walletobjects/v1/transitClass/{resourceId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => 'walletobjects/v1/transitClass',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'list' => [
              'path' => 'walletobjects/v1/transitClass',
              'httpMethod' => 'GET',
              'parameters' => [
                'issuerId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'token' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'walletobjects/v1/transitClass/{resourceId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'update' => [
              'path' => 'walletobjects/v1/transitClass/{resourceId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->transitobject = new Walletobjects\Resource\Transitobject(
        $this,
        $this->serviceName,
        'transitobject',
        [
          'methods' => [
            'addmessage' => [
              'path' => 'walletobjects/v1/transitObject/{resourceId}/addMessage',
              'httpMethod' => 'POST',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'walletobjects/v1/transitObject/{resourceId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'insert' => [
              'path' => 'walletobjects/v1/transitObject',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'list' => [
              'path' => 'walletobjects/v1/transitObject',
              'httpMethod' => 'GET',
              'parameters' => [
                'classId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'maxResults' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'token' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'walletobjects/v1/transitObject/{resourceId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'update' => [
              'path' => 'walletobjects/v1/transitObject/{resourceId}',
              'httpMethod' => 'PUT',
              'parameters' => [
                'resourceId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->walletobjects_v1_privateContent = new Walletobjects\Resource\WalletobjectsV1PrivateContent(
        $this,
        $this->serviceName,
        'privateContent',
        [
          'methods' => [
            'setPassUpdateNotice' => [
              'path' => 'walletobjects/v1/privateContent/setPassUpdateNotice',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],
          ]
        ]
    );
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Walletobjects::class, 'Google_Service_Walletobjects');
