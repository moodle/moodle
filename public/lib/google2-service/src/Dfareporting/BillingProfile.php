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

namespace Google\Service\Dfareporting;

class BillingProfile extends \Google\Model
{
  public const INVOICE_LEVEL_ACCOUNT_LEVEL = 'ACCOUNT_LEVEL';
  public const INVOICE_LEVEL_ADVERTISER_LEVEL = 'ADVERTISER_LEVEL';
  public const INVOICE_LEVEL_CAMPAIGN_LEVEL = 'CAMPAIGN_LEVEL';
  public const STATUS_UNDER_REVIEW = 'UNDER_REVIEW';
  public const STATUS_ACTIVE = 'ACTIVE';
  public const STATUS_ARCHIVED = 'ARCHIVED';
  /**
   * Consolidated invoice option for this billing profile. Used to get a single,
   * consolidated invoice across the chosen invoice level.
   *
   * @var bool
   */
  public $consolidatedInvoice;
  /**
   * Country code of this billing profile.This is a read-only field.
   *
   * @var string
   */
  public $countryCode;
  /**
   * Billing currency code in ISO 4217 format.This is a read-only field.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * ID of this billing profile. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  /**
   * Invoice level for this billing profile. Used to group fees into separate
   * invoices by account, advertiser, or campaign.
   *
   * @var string
   */
  public $invoiceLevel;
  /**
   * True if the billing profile is the account default profile. This is a read-
   * only field.
   *
   * @var bool
   */
  public $isDefault;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#billingProfile".
   *
   * @var string
   */
  public $kind;
  /**
   * Name of this billing profile. This is a required field and must be less
   * than 256 characters long and must be unique among billing profile in the
   * same account.
   *
   * @var string
   */
  public $name;
  /**
   * The ID of the payment account the billing profile belongs to. This is a
   * read-only field.
   *
   * @var string
   */
  public $paymentsAccountId;
  /**
   * The ID of the payment customer the billing profile belongs to. This is a
   * read-only field.
   *
   * @var string
   */
  public $paymentsCustomerId;
  /**
   * Purchase order (PO) for this billing profile. This PO number is used in the
   * invoices for all of the advertisers in this billing profile.
   *
   * @var string
   */
  public $purchaseOrder;
  /**
   * The ID of the secondary payment customer the billing profile belongs to.
   * This is a read-only field.
   *
   * @var string
   */
  public $secondaryPaymentsCustomerId;
  /**
   * Status of this billing profile.This is a read-only field.
   *
   * @var string
   */
  public $status;

  /**
   * Consolidated invoice option for this billing profile. Used to get a single,
   * consolidated invoice across the chosen invoice level.
   *
   * @param bool $consolidatedInvoice
   */
  public function setConsolidatedInvoice($consolidatedInvoice)
  {
    $this->consolidatedInvoice = $consolidatedInvoice;
  }
  /**
   * @return bool
   */
  public function getConsolidatedInvoice()
  {
    return $this->consolidatedInvoice;
  }
  /**
   * Country code of this billing profile.This is a read-only field.
   *
   * @param string $countryCode
   */
  public function setCountryCode($countryCode)
  {
    $this->countryCode = $countryCode;
  }
  /**
   * @return string
   */
  public function getCountryCode()
  {
    return $this->countryCode;
  }
  /**
   * Billing currency code in ISO 4217 format.This is a read-only field.
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
   * ID of this billing profile. This is a read-only, auto-generated field.
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
   * Invoice level for this billing profile. Used to group fees into separate
   * invoices by account, advertiser, or campaign.
   *
   * Accepted values: ACCOUNT_LEVEL, ADVERTISER_LEVEL, CAMPAIGN_LEVEL
   *
   * @param self::INVOICE_LEVEL_* $invoiceLevel
   */
  public function setInvoiceLevel($invoiceLevel)
  {
    $this->invoiceLevel = $invoiceLevel;
  }
  /**
   * @return self::INVOICE_LEVEL_*
   */
  public function getInvoiceLevel()
  {
    return $this->invoiceLevel;
  }
  /**
   * True if the billing profile is the account default profile. This is a read-
   * only field.
   *
   * @param bool $isDefault
   */
  public function setIsDefault($isDefault)
  {
    $this->isDefault = $isDefault;
  }
  /**
   * @return bool
   */
  public function getIsDefault()
  {
    return $this->isDefault;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#billingProfile".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Name of this billing profile. This is a required field and must be less
   * than 256 characters long and must be unique among billing profile in the
   * same account.
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
   * The ID of the payment account the billing profile belongs to. This is a
   * read-only field.
   *
   * @param string $paymentsAccountId
   */
  public function setPaymentsAccountId($paymentsAccountId)
  {
    $this->paymentsAccountId = $paymentsAccountId;
  }
  /**
   * @return string
   */
  public function getPaymentsAccountId()
  {
    return $this->paymentsAccountId;
  }
  /**
   * The ID of the payment customer the billing profile belongs to. This is a
   * read-only field.
   *
   * @param string $paymentsCustomerId
   */
  public function setPaymentsCustomerId($paymentsCustomerId)
  {
    $this->paymentsCustomerId = $paymentsCustomerId;
  }
  /**
   * @return string
   */
  public function getPaymentsCustomerId()
  {
    return $this->paymentsCustomerId;
  }
  /**
   * Purchase order (PO) for this billing profile. This PO number is used in the
   * invoices for all of the advertisers in this billing profile.
   *
   * @param string $purchaseOrder
   */
  public function setPurchaseOrder($purchaseOrder)
  {
    $this->purchaseOrder = $purchaseOrder;
  }
  /**
   * @return string
   */
  public function getPurchaseOrder()
  {
    return $this->purchaseOrder;
  }
  /**
   * The ID of the secondary payment customer the billing profile belongs to.
   * This is a read-only field.
   *
   * @param string $secondaryPaymentsCustomerId
   */
  public function setSecondaryPaymentsCustomerId($secondaryPaymentsCustomerId)
  {
    $this->secondaryPaymentsCustomerId = $secondaryPaymentsCustomerId;
  }
  /**
   * @return string
   */
  public function getSecondaryPaymentsCustomerId()
  {
    return $this->secondaryPaymentsCustomerId;
  }
  /**
   * Status of this billing profile.This is a read-only field.
   *
   * Accepted values: UNDER_REVIEW, ACTIVE, ARCHIVED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BillingProfile::class, 'Google_Service_Dfareporting_BillingProfile');
