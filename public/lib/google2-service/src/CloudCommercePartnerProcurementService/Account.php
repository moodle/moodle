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

namespace Google\Service\CloudCommercePartnerProcurementService;

class Account extends \Google\Collection
{
  /**
   * Default state of the account. It's only set to this value when the account
   * is first created and has not been initialized.
   */
  public const STATE_ACCOUNT_STATE_UNSPECIFIED = 'ACCOUNT_STATE_UNSPECIFIED';
  /**
   * The customer has requested the creation of the account resource, and the
   * provider notification message is dispatched. This state has been
   * deprecated, as accounts now immediately transition to
   * AccountState.ACCOUNT_ACTIVE.
   */
  public const STATE_ACCOUNT_ACTIVATION_REQUESTED = 'ACCOUNT_ACTIVATION_REQUESTED';
  /**
   * The account is active and ready for use. The next possible states are: -
   * Account getting deleted: After the user invokes delete from another API.
   */
  public const STATE_ACCOUNT_ACTIVE = 'ACCOUNT_ACTIVE';
  protected $collection_key = 'approvals';
  protected $approvalsType = Approval::class;
  protected $approvalsDataType = 'array';
  /**
   * Output only. The creation timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The custom properties that were collected from the user to
   * create this account.
   *
   * @deprecated
   * @var array[]
   */
  public $inputProperties;
  /**
   * Output only. The resource name of the account. Account names have the form
   * `accounts/{account_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The identifier of the service provider that this account was
   * created against. Each service provider is assigned a unique provider value
   * when they onboard with Cloud Commerce platform.
   *
   * @var string
   */
  public $provider;
  /**
   * Output only. The reseller parent billing account of the account's
   * corresponding billing account, applicable only when the corresponding
   * billing account is a subaccount of a reseller. Included in responses only
   * for view: ACCOUNT_VIEW_FULL. Format: billingAccounts/{billing_account_id}
   *
   * @var string
   */
  public $resellerParentBillingAccount;
  /**
   * Output only. The state of the account. This is used to decide whether the
   * customer is in good standing with the provider and is able to make
   * purchases. An account might not be able to make a purchase if the billing
   * account is suspended, for example.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The last update timestamp.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The approvals for this account. These approvals are used to
   * track actions that are permitted or have been completed by a customer
   * within the context of the provider. This might include a sign up flow or a
   * provisioning step, for example, that the provider can admit to having
   * happened.
   *
   * @param Approval[] $approvals
   */
  public function setApprovals($approvals)
  {
    $this->approvals = $approvals;
  }
  /**
   * @return Approval[]
   */
  public function getApprovals()
  {
    return $this->approvals;
  }
  /**
   * Output only. The creation timestamp.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The custom properties that were collected from the user to
   * create this account.
   *
   * @deprecated
   * @param array[] $inputProperties
   */
  public function setInputProperties($inputProperties)
  {
    $this->inputProperties = $inputProperties;
  }
  /**
   * @deprecated
   * @return array[]
   */
  public function getInputProperties()
  {
    return $this->inputProperties;
  }
  /**
   * Output only. The resource name of the account. Account names have the form
   * `accounts/{account_id}`.
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
   * Output only. The identifier of the service provider that this account was
   * created against. Each service provider is assigned a unique provider value
   * when they onboard with Cloud Commerce platform.
   *
   * @param string $provider
   */
  public function setProvider($provider)
  {
    $this->provider = $provider;
  }
  /**
   * @return string
   */
  public function getProvider()
  {
    return $this->provider;
  }
  /**
   * Output only. The reseller parent billing account of the account's
   * corresponding billing account, applicable only when the corresponding
   * billing account is a subaccount of a reseller. Included in responses only
   * for view: ACCOUNT_VIEW_FULL. Format: billingAccounts/{billing_account_id}
   *
   * @param string $resellerParentBillingAccount
   */
  public function setResellerParentBillingAccount($resellerParentBillingAccount)
  {
    $this->resellerParentBillingAccount = $resellerParentBillingAccount;
  }
  /**
   * @return string
   */
  public function getResellerParentBillingAccount()
  {
    return $this->resellerParentBillingAccount;
  }
  /**
   * Output only. The state of the account. This is used to decide whether the
   * customer is in good standing with the provider and is able to make
   * purchases. An account might not be able to make a purchase if the billing
   * account is suspended, for example.
   *
   * Accepted values: ACCOUNT_STATE_UNSPECIFIED, ACCOUNT_ACTIVATION_REQUESTED,
   * ACCOUNT_ACTIVE
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
  /**
   * Output only. The last update timestamp.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Account::class, 'Google_Service_CloudCommercePartnerProcurementService_Account');
