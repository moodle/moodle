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

class Client extends \Google\Model
{
  /**
   * A placeholder for an undefined client role. This value should never be
   * specified in user input for create or update method, otherwise an error
   * will be returned.
   */
  public const ROLE_CLIENT_ROLE_UNSPECIFIED = 'CLIENT_ROLE_UNSPECIFIED';
  /**
   * Users associated with this client role can only view proposals and deals in
   * the Marketplace UI. They cannot negotiate or approve proposals and deals
   * sent from publishers or send RFP to publishers.
   */
  public const ROLE_CLIENT_DEAL_VIEWER = 'CLIENT_DEAL_VIEWER';
  /**
   * Users associated with this client role can view and negotiate on the
   * proposals and deals in the Marketplace UI sent from publishers and send RFP
   * to publishers, but cannot approve the proposals and deals by themselves.
   * The buyer can approve the proposals and deals on behalf of the client.
   */
  public const ROLE_CLIENT_DEAL_NEGOTIATOR = 'CLIENT_DEAL_NEGOTIATOR';
  /**
   * Users associated with this client role can view, negotiate and approve
   * proposals and deals in the Marketplace UI and send RFP to publishers.
   */
  public const ROLE_CLIENT_DEAL_APPROVER = 'CLIENT_DEAL_APPROVER';
  /**
   * A placeholder for an undefined client state. Should not be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * A client that is currently active and allowed to access the Authorized
   * Buyers UI.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * A client that is currently inactive and not allowed to access the
   * Authorized Buyers UI.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * Required. Display name shown to publishers. Must be unique for clients
   * without partnerClientId specified. Maximum length of 255 characters is
   * allowed.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The resource name of the client. Format:
   * `buyers/{accountId}/clients/{clientAccountId}`
   *
   * @var string
   */
  public $name;
  /**
   * Arbitrary unique identifier provided by the buyer. This field can be used
   * to associate a client with an identifier in the namespace of the buyer,
   * lookup clients by that identifier and verify whether an Authorized Buyers
   * account of the client already exists. If present, must be unique across all
   * the clients.
   *
   * @var string
   */
  public $partnerClientId;
  /**
   * Required. The role assigned to the client. Each role implies a set of
   * permissions granted to the client.
   *
   * @var string
   */
  public $role;
  /**
   * Whether the client will be visible to sellers.
   *
   * @var bool
   */
  public $sellerVisible;
  /**
   * Output only. The state of the client.
   *
   * @var string
   */
  public $state;

  /**
   * Required. Display name shown to publishers. Must be unique for clients
   * without partnerClientId specified. Maximum length of 255 characters is
   * allowed.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The resource name of the client. Format:
   * `buyers/{accountId}/clients/{clientAccountId}`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Arbitrary unique identifier provided by the buyer. This field can be used
   * to associate a client with an identifier in the namespace of the buyer,
   * lookup clients by that identifier and verify whether an Authorized Buyers
   * account of the client already exists. If present, must be unique across all
   * the clients.
   *
   * @param string $partnerClientId
   */
  public function setPartnerClientId($partnerClientId)
  {
    $this->partnerClientId = $partnerClientId;
  }
  /**
   * @return string
   */
  public function getPartnerClientId()
  {
    return $this->partnerClientId;
  }
  /**
   * Required. The role assigned to the client. Each role implies a set of
   * permissions granted to the client.
   *
   * Accepted values: CLIENT_ROLE_UNSPECIFIED, CLIENT_DEAL_VIEWER,
   * CLIENT_DEAL_NEGOTIATOR, CLIENT_DEAL_APPROVER
   *
   * @param self::ROLE_* $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return self::ROLE_*
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * Whether the client will be visible to sellers.
   *
   * @param bool $sellerVisible
   */
  public function setSellerVisible($sellerVisible)
  {
    $this->sellerVisible = $sellerVisible;
  }
  /**
   * @return bool
   */
  public function getSellerVisible()
  {
    return $this->sellerVisible;
  }
  /**
   * Output only. The state of the client.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, INACTIVE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Client::class, 'Google_Service_AuthorizedBuyersMarketplace_Client');
