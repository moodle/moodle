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

namespace Google\Service\AdExchangeBuyerII;

class Client extends \Google\Model
{
  /**
   * A placeholder for an undefined client entity type. Should not be used.
   */
  public const ENTITY_TYPE_ENTITY_TYPE_UNSPECIFIED = 'ENTITY_TYPE_UNSPECIFIED';
  /**
   * An advertiser.
   */
  public const ENTITY_TYPE_ADVERTISER = 'ADVERTISER';
  /**
   * A brand.
   */
  public const ENTITY_TYPE_BRAND = 'BRAND';
  /**
   * An advertising agency.
   */
  public const ENTITY_TYPE_AGENCY = 'AGENCY';
  /**
   * An explicit value for a client that was not yet classified as any
   * particular entity.
   */
  public const ENTITY_TYPE_ENTITY_TYPE_UNCLASSIFIED = 'ENTITY_TYPE_UNCLASSIFIED';
  /**
   * A placeholder for an undefined client role.
   */
  public const ROLE_CLIENT_ROLE_UNSPECIFIED = 'CLIENT_ROLE_UNSPECIFIED';
  /**
   * Users associated with this client can see publisher deal offers in the
   * Marketplace. They can neither negotiate proposals nor approve deals. If
   * this client is visible to publishers, they can send deal proposals to this
   * client.
   */
  public const ROLE_CLIENT_DEAL_VIEWER = 'CLIENT_DEAL_VIEWER';
  /**
   * Users associated with this client can respond to deal proposals sent to
   * them by publishers. They can also initiate deal proposals of their own.
   */
  public const ROLE_CLIENT_DEAL_NEGOTIATOR = 'CLIENT_DEAL_NEGOTIATOR';
  /**
   * Users associated with this client can approve eligible deals on your
   * behalf. Some deals may still explicitly require publisher finalization. If
   * this role is not selected, the sponsor buyer will need to manually approve
   * each of their deals.
   */
  public const ROLE_CLIENT_DEAL_APPROVER = 'CLIENT_DEAL_APPROVER';
  /**
   * A placeholder for an undefined client status.
   */
  public const STATUS_CLIENT_STATUS_UNSPECIFIED = 'CLIENT_STATUS_UNSPECIFIED';
  /**
   * A client that is currently disabled.
   */
  public const STATUS_DISABLED = 'DISABLED';
  /**
   * A client that is currently active.
   */
  public const STATUS_ACTIVE = 'ACTIVE';
  /**
   * The globally-unique numerical ID of the client. The value of this field is
   * ignored in create and update operations.
   *
   * @var string
   */
  public $clientAccountId;
  /**
   * Name used to represent this client to publishers. You may have multiple
   * clients that map to the same entity, but for each client the combination of
   * `clientName` and entity must be unique. You can specify this field as
   * empty. Maximum length of 255 characters is allowed.
   *
   * @var string
   */
  public $clientName;
  /**
   * Numerical identifier of the client entity. The entity can be an advertiser,
   * a brand, or an agency. This identifier is unique among all the entities
   * with the same type. The value of this field is ignored if the entity type
   * is not provided. A list of all known advertisers with their identifiers is
   * available in the [advertisers.txt](https://storage.googleapis.com/adx-rtb-
   * dictionaries/advertisers.txt) file. A list of all known brands with their
   * identifiers is available in the
   * [brands.txt](https://storage.googleapis.com/adx-rtb-
   * dictionaries/brands.txt) file. A list of all known agencies with their
   * identifiers is available in the
   * [agencies.txt](https://storage.googleapis.com/adx-rtb-
   * dictionaries/agencies.txt) file.
   *
   * @var string
   */
  public $entityId;
  /**
   * The name of the entity. This field is automatically fetched based on the
   * type and ID. The value of this field is ignored in create and update
   * operations.
   *
   * @var string
   */
  public $entityName;
  /**
   * An optional field for specifying the type of the client entity:
   * `ADVERTISER`, `BRAND`, or `AGENCY`.
   *
   * @var string
   */
  public $entityType;
  /**
   * Optional arbitrary unique identifier of this client buyer from the
   * standpoint of its Ad Exchange sponsor buyer. This field can be used to
   * associate a client buyer with the identifier in the namespace of its
   * sponsor buyer, lookup client buyers by that identifier and verify whether
   * an Ad Exchange counterpart of a given client buyer already exists. If
   * present, must be unique among all the client buyers for its Ad Exchange
   * sponsor buyer.
   *
   * @var string
   */
  public $partnerClientId;
  /**
   * The role which is assigned to the client buyer. Each role implies a set of
   * permissions granted to the client. Must be one of `CLIENT_DEAL_VIEWER`,
   * `CLIENT_DEAL_NEGOTIATOR` or `CLIENT_DEAL_APPROVER`.
   *
   * @var string
   */
  public $role;
  /**
   * The status of the client buyer.
   *
   * @var string
   */
  public $status;
  /**
   * Whether the client buyer will be visible to sellers.
   *
   * @var bool
   */
  public $visibleToSeller;

  /**
   * The globally-unique numerical ID of the client. The value of this field is
   * ignored in create and update operations.
   *
   * @param string $clientAccountId
   */
  public function setClientAccountId($clientAccountId)
  {
    $this->clientAccountId = $clientAccountId;
  }
  /**
   * @return string
   */
  public function getClientAccountId()
  {
    return $this->clientAccountId;
  }
  /**
   * Name used to represent this client to publishers. You may have multiple
   * clients that map to the same entity, but for each client the combination of
   * `clientName` and entity must be unique. You can specify this field as
   * empty. Maximum length of 255 characters is allowed.
   *
   * @param string $clientName
   */
  public function setClientName($clientName)
  {
    $this->clientName = $clientName;
  }
  /**
   * @return string
   */
  public function getClientName()
  {
    return $this->clientName;
  }
  /**
   * Numerical identifier of the client entity. The entity can be an advertiser,
   * a brand, or an agency. This identifier is unique among all the entities
   * with the same type. The value of this field is ignored if the entity type
   * is not provided. A list of all known advertisers with their identifiers is
   * available in the [advertisers.txt](https://storage.googleapis.com/adx-rtb-
   * dictionaries/advertisers.txt) file. A list of all known brands with their
   * identifiers is available in the
   * [brands.txt](https://storage.googleapis.com/adx-rtb-
   * dictionaries/brands.txt) file. A list of all known agencies with their
   * identifiers is available in the
   * [agencies.txt](https://storage.googleapis.com/adx-rtb-
   * dictionaries/agencies.txt) file.
   *
   * @param string $entityId
   */
  public function setEntityId($entityId)
  {
    $this->entityId = $entityId;
  }
  /**
   * @return string
   */
  public function getEntityId()
  {
    return $this->entityId;
  }
  /**
   * The name of the entity. This field is automatically fetched based on the
   * type and ID. The value of this field is ignored in create and update
   * operations.
   *
   * @param string $entityName
   */
  public function setEntityName($entityName)
  {
    $this->entityName = $entityName;
  }
  /**
   * @return string
   */
  public function getEntityName()
  {
    return $this->entityName;
  }
  /**
   * An optional field for specifying the type of the client entity:
   * `ADVERTISER`, `BRAND`, or `AGENCY`.
   *
   * Accepted values: ENTITY_TYPE_UNSPECIFIED, ADVERTISER, BRAND, AGENCY,
   * ENTITY_TYPE_UNCLASSIFIED
   *
   * @param self::ENTITY_TYPE_* $entityType
   */
  public function setEntityType($entityType)
  {
    $this->entityType = $entityType;
  }
  /**
   * @return self::ENTITY_TYPE_*
   */
  public function getEntityType()
  {
    return $this->entityType;
  }
  /**
   * Optional arbitrary unique identifier of this client buyer from the
   * standpoint of its Ad Exchange sponsor buyer. This field can be used to
   * associate a client buyer with the identifier in the namespace of its
   * sponsor buyer, lookup client buyers by that identifier and verify whether
   * an Ad Exchange counterpart of a given client buyer already exists. If
   * present, must be unique among all the client buyers for its Ad Exchange
   * sponsor buyer.
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
   * The role which is assigned to the client buyer. Each role implies a set of
   * permissions granted to the client. Must be one of `CLIENT_DEAL_VIEWER`,
   * `CLIENT_DEAL_NEGOTIATOR` or `CLIENT_DEAL_APPROVER`.
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
   * The status of the client buyer.
   *
   * Accepted values: CLIENT_STATUS_UNSPECIFIED, DISABLED, ACTIVE
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Whether the client buyer will be visible to sellers.
   *
   * @param bool $visibleToSeller
   */
  public function setVisibleToSeller($visibleToSeller)
  {
    $this->visibleToSeller = $visibleToSeller;
  }
  /**
   * @return bool
   */
  public function getVisibleToSeller()
  {
    return $this->visibleToSeller;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Client::class, 'Google_Service_AdExchangeBuyerII_Client');
