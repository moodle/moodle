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

namespace Google\Service\Cloudbilling;

class BillingAccount extends \Google\Model
{
  /**
   * Optional. The currency in which the billing account is billed and charged,
   * represented as an ISO 4217 code such as `USD`. Billing account currency is
   * determined at the time of billing account creation and cannot be updated
   * subsequently, so this field should not be set on update requests. In
   * addition, a subaccount always matches the currency of its parent billing
   * account, so this field should not be set on subaccount creation requests.
   * Clients can read this field to determine the currency of an existing
   * billing account.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * The display name given to the billing account, such as `My Billing
   * Account`. This name is displayed in the Google Cloud Console.
   *
   * @var string
   */
  public $displayName;
  /**
   * If this account is a
   * [subaccount](https://cloud.google.com/billing/docs/concepts), then this
   * will be the resource name of the parent billing account that it is being
   * resold through. Otherwise this will be empty.
   *
   * @var string
   */
  public $masterBillingAccount;
  /**
   * Output only. The resource name of the billing account. The resource name
   * has the form `billingAccounts/{billing_account_id}`. For example,
   * `billingAccounts/012345-567890-ABCDEF` would be the resource name for
   * billing account `012345-567890-ABCDEF`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. True if the billing account is open, and will therefore be
   * charged for any usage on associated projects. False if the billing account
   * is closed, and therefore projects associated with it are unable to use paid
   * services.
   *
   * @var bool
   */
  public $open;
  /**
   * Output only. The billing account's parent resource identifier. Use the
   * `MoveBillingAccount` method to update the account's parent resource if it
   * is a organization. Format: - `organizations/{organization_id}`, for
   * example, `organizations/12345678` - `billingAccounts/{billing_account_id}`,
   * for example, `billingAccounts/012345-567890-ABCDEF`
   *
   * @var string
   */
  public $parent;

  /**
   * Optional. The currency in which the billing account is billed and charged,
   * represented as an ISO 4217 code such as `USD`. Billing account currency is
   * determined at the time of billing account creation and cannot be updated
   * subsequently, so this field should not be set on update requests. In
   * addition, a subaccount always matches the currency of its parent billing
   * account, so this field should not be set on subaccount creation requests.
   * Clients can read this field to determine the currency of an existing
   * billing account.
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
   * The display name given to the billing account, such as `My Billing
   * Account`. This name is displayed in the Google Cloud Console.
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
   * If this account is a
   * [subaccount](https://cloud.google.com/billing/docs/concepts), then this
   * will be the resource name of the parent billing account that it is being
   * resold through. Otherwise this will be empty.
   *
   * @param string $masterBillingAccount
   */
  public function setMasterBillingAccount($masterBillingAccount)
  {
    $this->masterBillingAccount = $masterBillingAccount;
  }
  /**
   * @return string
   */
  public function getMasterBillingAccount()
  {
    return $this->masterBillingAccount;
  }
  /**
   * Output only. The resource name of the billing account. The resource name
   * has the form `billingAccounts/{billing_account_id}`. For example,
   * `billingAccounts/012345-567890-ABCDEF` would be the resource name for
   * billing account `012345-567890-ABCDEF`.
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
   * Output only. True if the billing account is open, and will therefore be
   * charged for any usage on associated projects. False if the billing account
   * is closed, and therefore projects associated with it are unable to use paid
   * services.
   *
   * @param bool $open
   */
  public function setOpen($open)
  {
    $this->open = $open;
  }
  /**
   * @return bool
   */
  public function getOpen()
  {
    return $this->open;
  }
  /**
   * Output only. The billing account's parent resource identifier. Use the
   * `MoveBillingAccount` method to update the account's parent resource if it
   * is a organization. Format: - `organizations/{organization_id}`, for
   * example, `organizations/12345678` - `billingAccounts/{billing_account_id}`,
   * for example, `billingAccounts/012345-567890-ABCDEF`
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BillingAccount::class, 'Google_Service_Cloudbilling_BillingAccount');
