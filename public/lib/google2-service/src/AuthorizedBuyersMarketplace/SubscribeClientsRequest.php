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

namespace Google\Service\AuthorizedBuyersMarketplace;

class SubscribeClientsRequest extends \Google\Collection
{
  protected $collection_key = 'clients';
  /**
   * Optional. A list of client buyers to subscribe to the auction package, with
   * client buyer in the format `buyers/{accountId}/clients/{clientAccountId}`.
   * The current buyer will be subscribed to the auction package regardless of
   * the list contents if not already.
   *
   * @var string[]
   */
  public $clients;

  /**
   * Optional. A list of client buyers to subscribe to the auction package, with
   * client buyer in the format `buyers/{accountId}/clients/{clientAccountId}`.
   * The current buyer will be subscribed to the auction package regardless of
   * the list contents if not already.
   *
   * @param string[] $clients
   */
  public function setClients($clients)
  {
    $this->clients = $clients;
  }
  /**
   * @return string[]
   */
  public function getClients()
  {
    return $this->clients;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubscribeClientsRequest::class, 'Google_Service_AuthorizedBuyersMarketplace_SubscribeClientsRequest');
