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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesCustomerClient extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Indicates an active account able to serve ads.
   */
  public const STATUS_ENABLED = 'ENABLED';
  /**
   * Indicates a canceled account unable to serve ads. Can be reactivated by an
   * admin user.
   */
  public const STATUS_CANCELED = 'CANCELED';
  /**
   * Indicates a suspended account unable to serve ads. May only be activated by
   * Google support.
   */
  public const STATUS_SUSPENDED = 'SUSPENDED';
  /**
   * Indicates a closed account unable to serve ads. Test account will also have
   * CLOSED status. Status is permanent and may not be reopened.
   */
  public const STATUS_CLOSED = 'CLOSED';
  protected $collection_key = 'appliedLabels';
  /**
   * Output only. The resource names of the labels owned by the requesting
   * customer that are applied to the client customer. Label resource names have
   * the form: `customers/{customer_id}/labels/{label_id}`
   *
   * @var string[]
   */
  public $appliedLabels;
  /**
   * Output only. The resource name of the client-customer which is linked to
   * the given customer. Read only.
   *
   * @var string
   */
  public $clientCustomer;
  /**
   * Output only. Currency code (for example, 'USD', 'EUR') for the client. Read
   * only.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * Output only. Descriptive name for the client. Read only.
   *
   * @var string
   */
  public $descriptiveName;
  /**
   * Output only. Specifies whether this is a hidden account. Read only.
   *
   * @var bool
   */
  public $hidden;
  /**
   * Output only. The ID of the client customer. Read only.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. Distance between given customer and client. For self link, the
   * level value will be 0. Read only.
   *
   * @var string
   */
  public $level;
  /**
   * Output only. Identifies if the client is a manager. Read only.
   *
   * @var bool
   */
  public $manager;
  /**
   * Output only. The resource name of the customer client. CustomerClient
   * resource names have the form:
   * `customers/{customer_id}/customerClients/{client_customer_id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. The status of the client customer. Read only.
   *
   * @var string
   */
  public $status;
  /**
   * Output only. Identifies if the client is a test account. Read only.
   *
   * @var bool
   */
  public $testAccount;
  /**
   * Output only. Common Locale Data Repository (CLDR) string representation of
   * the time zone of the client, for example, America/Los_Angeles. Read only.
   *
   * @var string
   */
  public $timeZone;

  /**
   * Output only. The resource names of the labels owned by the requesting
   * customer that are applied to the client customer. Label resource names have
   * the form: `customers/{customer_id}/labels/{label_id}`
   *
   * @param string[] $appliedLabels
   */
  public function setAppliedLabels($appliedLabels)
  {
    $this->appliedLabels = $appliedLabels;
  }
  /**
   * @return string[]
   */
  public function getAppliedLabels()
  {
    return $this->appliedLabels;
  }
  /**
   * Output only. The resource name of the client-customer which is linked to
   * the given customer. Read only.
   *
   * @param string $clientCustomer
   */
  public function setClientCustomer($clientCustomer)
  {
    $this->clientCustomer = $clientCustomer;
  }
  /**
   * @return string
   */
  public function getClientCustomer()
  {
    return $this->clientCustomer;
  }
  /**
   * Output only. Currency code (for example, 'USD', 'EUR') for the client. Read
   * only.
   *
   * @param string $currencyCode
   */
  public function setCurrencyCode($currencyCode)
  {
    $this->currencyCode = $currencyCode;
  }
  /**
   * @return string
   */
  public function getCurrencyCode()
  {
    return $this->currencyCode;
  }
  /**
   * Output only. Descriptive name for the client. Read only.
   *
   * @param string $descriptiveName
   */
  public function setDescriptiveName($descriptiveName)
  {
    $this->descriptiveName = $descriptiveName;
  }
  /**
   * @return string
   */
  public function getDescriptiveName()
  {
    return $this->descriptiveName;
  }
  /**
   * Output only. Specifies whether this is a hidden account. Read only.
   *
   * @param bool $hidden
   */
  public function setHidden($hidden)
  {
    $this->hidden = $hidden;
  }
  /**
   * @return bool
   */
  public function getHidden()
  {
    return $this->hidden;
  }
  /**
   * Output only. The ID of the client customer. Read only.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. Distance between given customer and client. For self link, the
   * level value will be 0. Read only.
   *
   * @param string $level
   */
  public function setLevel($level)
  {
    $this->level = $level;
  }
  /**
   * @return string
   */
  public function getLevel()
  {
    return $this->level;
  }
  /**
   * Output only. Identifies if the client is a manager. Read only.
   *
   * @param bool $manager
   */
  public function setManager($manager)
  {
    $this->manager = $manager;
  }
  /**
   * @return bool
   */
  public function getManager()
  {
    return $this->manager;
  }
  /**
   * Output only. The resource name of the customer client. CustomerClient
   * resource names have the form:
   * `customers/{customer_id}/customerClients/{client_customer_id}`
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Output only. The status of the client customer. Read only.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ENABLED, CANCELED, SUSPENDED, CLOSED
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
   * Output only. Identifies if the client is a test account. Read only.
   *
   * @param bool $testAccount
   */
  public function setTestAccount($testAccount)
  {
    $this->testAccount = $testAccount;
  }
  /**
   * @return bool
   */
  public function getTestAccount()
  {
    return $this->testAccount;
  }
  /**
   * Output only. Common Locale Data Repository (CLDR) string representation of
   * the time zone of the client, for example, America/Los_Angeles. Read only.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesCustomerClient::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesCustomerClient');
