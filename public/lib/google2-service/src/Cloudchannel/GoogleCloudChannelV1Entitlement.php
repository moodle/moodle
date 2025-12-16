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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1Entitlement extends \Google\Collection
{
  /**
   * Not used.
   */
  public const PROVISIONING_STATE_PROVISIONING_STATE_UNSPECIFIED = 'PROVISIONING_STATE_UNSPECIFIED';
  /**
   * The entitlement is currently active.
   */
  public const PROVISIONING_STATE_ACTIVE = 'ACTIVE';
  /**
   * The entitlement is currently suspended.
   */
  public const PROVISIONING_STATE_SUSPENDED = 'SUSPENDED';
  protected $collection_key = 'suspensionReasons';
  protected $associationInfoType = GoogleCloudChannelV1AssociationInfo::class;
  protected $associationInfoDataType = '';
  /**
   * Optional. The billing account resource name that is used to pay for this
   * entitlement.
   *
   * @var string
   */
  public $billingAccount;
  protected $commitmentSettingsType = GoogleCloudChannelV1CommitmentSettings::class;
  protected $commitmentSettingsDataType = '';
  /**
   * Output only. The time at which the entitlement is created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Resource name of an entitlement in the form: accounts/{account
   * _id}/customers/{customer_id}/entitlements/{entitlement_id}.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The offer resource name for which the entitlement is to be
   * created. Takes the form: accounts/{account_id}/offers/{offer_id}.
   *
   * @var string
   */
  public $offer;
  protected $parametersType = GoogleCloudChannelV1Parameter::class;
  protected $parametersDataType = 'array';
  /**
   * Optional. Price reference ID for the offer. Only for offers that require
   * additional price information. Used to guarantee that the pricing is
   * consistent between quoting the offer and placing the order.
   *
   * @var string
   */
  public $priceReferenceId;
  protected $provisionedServiceType = GoogleCloudChannelV1ProvisionedService::class;
  protected $provisionedServiceDataType = '';
  /**
   * Output only. Current provisioning state of the entitlement.
   *
   * @var string
   */
  public $provisioningState;
  /**
   * Optional. This purchase order (PO) information is for resellers to use for
   * their company tracking usage. If a purchaseOrderId value is given, it
   * appears in the API responses and shows up in the invoice. The property
   * accepts up to 80 plain text characters. This is only supported for Google
   * Workspace entitlements.
   *
   * @var string
   */
  public $purchaseOrderId;
  /**
   * Output only. Enumerable of all current suspension reasons for an
   * entitlement.
   *
   * @var string[]
   */
  public $suspensionReasons;
  protected $trialSettingsType = GoogleCloudChannelV1TrialSettings::class;
  protected $trialSettingsDataType = '';
  /**
   * Output only. The time at which the entitlement is updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Association information to other entitlements.
   *
   * @param GoogleCloudChannelV1AssociationInfo $associationInfo
   */
  public function setAssociationInfo(GoogleCloudChannelV1AssociationInfo $associationInfo)
  {
    $this->associationInfo = $associationInfo;
  }
  /**
   * @return GoogleCloudChannelV1AssociationInfo
   */
  public function getAssociationInfo()
  {
    return $this->associationInfo;
  }
  /**
   * Optional. The billing account resource name that is used to pay for this
   * entitlement.
   *
   * @param string $billingAccount
   */
  public function setBillingAccount($billingAccount)
  {
    $this->billingAccount = $billingAccount;
  }
  /**
   * @return string
   */
  public function getBillingAccount()
  {
    return $this->billingAccount;
  }
  /**
   * Commitment settings for a commitment-based Offer. Required for commitment
   * based offers.
   *
   * @param GoogleCloudChannelV1CommitmentSettings $commitmentSettings
   */
  public function setCommitmentSettings(GoogleCloudChannelV1CommitmentSettings $commitmentSettings)
  {
    $this->commitmentSettings = $commitmentSettings;
  }
  /**
   * @return GoogleCloudChannelV1CommitmentSettings
   */
  public function getCommitmentSettings()
  {
    return $this->commitmentSettings;
  }
  /**
   * Output only. The time at which the entitlement is created.
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
   * Output only. Resource name of an entitlement in the form: accounts/{account
   * _id}/customers/{customer_id}/entitlements/{entitlement_id}.
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
   * Required. The offer resource name for which the entitlement is to be
   * created. Takes the form: accounts/{account_id}/offers/{offer_id}.
   *
   * @param string $offer
   */
  public function setOffer($offer)
  {
    $this->offer = $offer;
  }
  /**
   * @return string
   */
  public function getOffer()
  {
    return $this->offer;
  }
  /**
   * Extended entitlement parameters. When creating an entitlement, valid
   * parameter names and values are defined in the Offer.parameter_definitions.
   * For Google Workspace, the following Parameters may be accepted as input: -
   * max_units: The maximum assignable units for a flexible offer OR -
   * num_units: The total commitment for commitment-based offers The response
   * may additionally include the following output-only Parameters: -
   * assigned_units: The number of licenses assigned to users. For Google Cloud
   * billing subaccounts, the following Parameter may be accepted as input: -
   * display_name: The display name of the billing subaccount.
   *
   * @param GoogleCloudChannelV1Parameter[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleCloudChannelV1Parameter[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Optional. Price reference ID for the offer. Only for offers that require
   * additional price information. Used to guarantee that the pricing is
   * consistent between quoting the offer and placing the order.
   *
   * @param string $priceReferenceId
   */
  public function setPriceReferenceId($priceReferenceId)
  {
    $this->priceReferenceId = $priceReferenceId;
  }
  /**
   * @return string
   */
  public function getPriceReferenceId()
  {
    return $this->priceReferenceId;
  }
  /**
   * Output only. Service provisioning details for the entitlement.
   *
   * @param GoogleCloudChannelV1ProvisionedService $provisionedService
   */
  public function setProvisionedService(GoogleCloudChannelV1ProvisionedService $provisionedService)
  {
    $this->provisionedService = $provisionedService;
  }
  /**
   * @return GoogleCloudChannelV1ProvisionedService
   */
  public function getProvisionedService()
  {
    return $this->provisionedService;
  }
  /**
   * Output only. Current provisioning state of the entitlement.
   *
   * Accepted values: PROVISIONING_STATE_UNSPECIFIED, ACTIVE, SUSPENDED
   *
   * @param self::PROVISIONING_STATE_* $provisioningState
   */
  public function setProvisioningState($provisioningState)
  {
    $this->provisioningState = $provisioningState;
  }
  /**
   * @return self::PROVISIONING_STATE_*
   */
  public function getProvisioningState()
  {
    return $this->provisioningState;
  }
  /**
   * Optional. This purchase order (PO) information is for resellers to use for
   * their company tracking usage. If a purchaseOrderId value is given, it
   * appears in the API responses and shows up in the invoice. The property
   * accepts up to 80 plain text characters. This is only supported for Google
   * Workspace entitlements.
   *
   * @param string $purchaseOrderId
   */
  public function setPurchaseOrderId($purchaseOrderId)
  {
    $this->purchaseOrderId = $purchaseOrderId;
  }
  /**
   * @return string
   */
  public function getPurchaseOrderId()
  {
    return $this->purchaseOrderId;
  }
  /**
   * Output only. Enumerable of all current suspension reasons for an
   * entitlement.
   *
   * @param string[] $suspensionReasons
   */
  public function setSuspensionReasons($suspensionReasons)
  {
    $this->suspensionReasons = $suspensionReasons;
  }
  /**
   * @return string[]
   */
  public function getSuspensionReasons()
  {
    return $this->suspensionReasons;
  }
  /**
   * Output only. Settings for trial offers.
   *
   * @param GoogleCloudChannelV1TrialSettings $trialSettings
   */
  public function setTrialSettings(GoogleCloudChannelV1TrialSettings $trialSettings)
  {
    $this->trialSettings = $trialSettings;
  }
  /**
   * @return GoogleCloudChannelV1TrialSettings
   */
  public function getTrialSettings()
  {
    return $this->trialSettings;
  }
  /**
   * Output only. The time at which the entitlement is updated.
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
class_alias(GoogleCloudChannelV1Entitlement::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1Entitlement');
