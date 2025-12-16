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

namespace Google\Service\Merchant;

class ProductStatusChangeMessage extends \Google\Collection
{
  /**
   * Unspecified attribute
   */
  public const ATTRIBUTE_ATTRIBUTE_UNSPECIFIED = 'ATTRIBUTE_UNSPECIFIED';
  /**
   * Status of the changed entity
   */
  public const ATTRIBUTE_STATUS = 'STATUS';
  /**
   * Unspecified resource
   */
  public const RESOURCE_TYPE_RESOURCE_UNSPECIFIED = 'RESOURCE_UNSPECIFIED';
  /**
   * Resource type : product
   */
  public const RESOURCE_TYPE_PRODUCT = 'PRODUCT';
  protected $collection_key = 'changes';
  /**
   * The target account that owns the entity that changed. Format :
   * `accounts/{merchant_id}`
   *
   * @var string
   */
  public $account;
  /**
   * The attribute in the resource that changed, in this case it will be always
   * `Status`.
   *
   * @var string
   */
  public $attribute;
  protected $changesType = ProductChange::class;
  protected $changesDataType = 'array';
  /**
   * The time at which the event was generated. If you want to order the
   * notification messages you receive you should rely on this field not on the
   * order of receiving the notifications.
   *
   * @var string
   */
  public $eventTime;
  /**
   * Optional. The product expiration time. This field will not be set if the
   * notification is sent for a product deletion event.
   *
   * @var string
   */
  public $expirationTime;
  /**
   * The account that manages the merchant's account. can be the same as
   * merchant id if it is standalone account. Format :
   * `accounts/{service_provider_id}`
   *
   * @var string
   */
  public $managingAccount;
  /**
   * The product name. Format: `accounts/{account}/products/{product}`
   *
   * @var string
   */
  public $resource;
  /**
   * The product id.
   *
   * @var string
   */
  public $resourceId;
  /**
   * The resource that changed, in this case it will always be `Product`.
   *
   * @var string
   */
  public $resourceType;

  /**
   * The target account that owns the entity that changed. Format :
   * `accounts/{merchant_id}`
   *
   * @param string $account
   */
  public function setAccount($account)
  {
    $this->account = $account;
  }
  /**
   * @return string
   */
  public function getAccount()
  {
    return $this->account;
  }
  /**
   * The attribute in the resource that changed, in this case it will be always
   * `Status`.
   *
   * Accepted values: ATTRIBUTE_UNSPECIFIED, STATUS
   *
   * @param self::ATTRIBUTE_* $attribute
   */
  public function setAttribute($attribute)
  {
    $this->attribute = $attribute;
  }
  /**
   * @return self::ATTRIBUTE_*
   */
  public function getAttribute()
  {
    return $this->attribute;
  }
  /**
   * A message to describe the change that happened to the product
   *
   * @param ProductChange[] $changes
   */
  public function setChanges($changes)
  {
    $this->changes = $changes;
  }
  /**
   * @return ProductChange[]
   */
  public function getChanges()
  {
    return $this->changes;
  }
  /**
   * The time at which the event was generated. If you want to order the
   * notification messages you receive you should rely on this field not on the
   * order of receiving the notifications.
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * Optional. The product expiration time. This field will not be set if the
   * notification is sent for a product deletion event.
   *
   * @param string $expirationTime
   */
  public function setExpirationTime($expirationTime)
  {
    $this->expirationTime = $expirationTime;
  }
  /**
   * @return string
   */
  public function getExpirationTime()
  {
    return $this->expirationTime;
  }
  /**
   * The account that manages the merchant's account. can be the same as
   * merchant id if it is standalone account. Format :
   * `accounts/{service_provider_id}`
   *
   * @param string $managingAccount
   */
  public function setManagingAccount($managingAccount)
  {
    $this->managingAccount = $managingAccount;
  }
  /**
   * @return string
   */
  public function getManagingAccount()
  {
    return $this->managingAccount;
  }
  /**
   * The product name. Format: `accounts/{account}/products/{product}`
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * The product id.
   *
   * @param string $resourceId
   */
  public function setResourceId($resourceId)
  {
    $this->resourceId = $resourceId;
  }
  /**
   * @return string
   */
  public function getResourceId()
  {
    return $this->resourceId;
  }
  /**
   * The resource that changed, in this case it will always be `Product`.
   *
   * Accepted values: RESOURCE_UNSPECIFIED, PRODUCT
   *
   * @param self::RESOURCE_TYPE_* $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return self::RESOURCE_TYPE_*
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductStatusChangeMessage::class, 'Google_Service_Merchant_ProductStatusChangeMessage');
