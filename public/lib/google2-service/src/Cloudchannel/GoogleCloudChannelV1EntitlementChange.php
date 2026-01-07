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

class GoogleCloudChannelV1EntitlementChange extends \Google\Collection
{
  /**
   * Not used.
   */
  public const ACTIVATION_REASON_ACTIVATION_REASON_UNSPECIFIED = 'ACTIVATION_REASON_UNSPECIFIED';
  /**
   * Reseller reactivated a suspended Entitlement.
   */
  public const ACTIVATION_REASON_RESELLER_REVOKED_SUSPENSION = 'RESELLER_REVOKED_SUSPENSION';
  /**
   * Customer accepted pending terms of service.
   */
  public const ACTIVATION_REASON_CUSTOMER_ACCEPTED_PENDING_TOS = 'CUSTOMER_ACCEPTED_PENDING_TOS';
  /**
   * Reseller updated the renewal settings on an entitlement that was suspended
   * due to cancellation, and this update reactivated the entitlement.
   */
  public const ACTIVATION_REASON_RENEWAL_SETTINGS_CHANGED = 'RENEWAL_SETTINGS_CHANGED';
  /**
   * Other reasons (Activated temporarily for cancellation, added a payment plan
   * to a trial entitlement, etc.)
   */
  public const ACTIVATION_REASON_OTHER_ACTIVATION_REASON = 'OTHER_ACTIVATION_REASON';
  /**
   * Not used.
   */
  public const CANCELLATION_REASON_CANCELLATION_REASON_UNSPECIFIED = 'CANCELLATION_REASON_UNSPECIFIED';
  /**
   * Reseller triggered a cancellation of the service.
   */
  public const CANCELLATION_REASON_SERVICE_TERMINATED = 'SERVICE_TERMINATED';
  /**
   * Relationship between the reseller and customer has ended due to a transfer.
   */
  public const CANCELLATION_REASON_RELATIONSHIP_ENDED = 'RELATIONSHIP_ENDED';
  /**
   * Entitlement transferred away from reseller while still keeping other
   * entitlement(s) with the reseller.
   */
  public const CANCELLATION_REASON_PARTIAL_TRANSFER = 'PARTIAL_TRANSFER';
  /**
   * Not used.
   */
  public const CHANGE_TYPE_CHANGE_TYPE_UNSPECIFIED = 'CHANGE_TYPE_UNSPECIFIED';
  /**
   * New Entitlement was created.
   */
  public const CHANGE_TYPE_CREATED = 'CREATED';
  /**
   * Price plan associated with an Entitlement was changed.
   */
  public const CHANGE_TYPE_PRICE_PLAN_SWITCHED = 'PRICE_PLAN_SWITCHED';
  /**
   * Number of seats committed for a commitment Entitlement was changed.
   */
  public const CHANGE_TYPE_COMMITMENT_CHANGED = 'COMMITMENT_CHANGED';
  /**
   * An annual Entitlement was renewed.
   */
  public const CHANGE_TYPE_RENEWED = 'RENEWED';
  /**
   * Entitlement was suspended.
   */
  public const CHANGE_TYPE_SUSPENDED = 'SUSPENDED';
  /**
   * Entitlement was activated.
   */
  public const CHANGE_TYPE_ACTIVATED = 'ACTIVATED';
  /**
   * Entitlement was cancelled.
   */
  public const CHANGE_TYPE_CANCELLED = 'CANCELLED';
  /**
   * Entitlement was upgraded or downgraded for ex. from Google Workspace
   * Business Standard to Google Workspace Business Plus.
   */
  public const CHANGE_TYPE_SKU_CHANGED = 'SKU_CHANGED';
  /**
   * The settings for renewal of an Entitlement have changed.
   */
  public const CHANGE_TYPE_RENEWAL_SETTING_CHANGED = 'RENEWAL_SETTING_CHANGED';
  /**
   * Use for Google Workspace subscription. Either a trial was converted to a
   * paid subscription or a new subscription with no trial is created.
   */
  public const CHANGE_TYPE_PAID_SUBSCRIPTION_STARTED = 'PAID_SUBSCRIPTION_STARTED';
  /**
   * License cap was changed for the entitlement.
   */
  public const CHANGE_TYPE_LICENSE_CAP_CHANGED = 'LICENSE_CAP_CHANGED';
  /**
   * The suspension details have changed (but it is still suspended).
   */
  public const CHANGE_TYPE_SUSPENSION_DETAILS_CHANGED = 'SUSPENSION_DETAILS_CHANGED';
  /**
   * The trial end date was extended.
   */
  public const CHANGE_TYPE_TRIAL_END_DATE_EXTENDED = 'TRIAL_END_DATE_EXTENDED';
  /**
   * Entitlement started trial.
   */
  public const CHANGE_TYPE_TRIAL_STARTED = 'TRIAL_STARTED';
  /**
   * Not used.
   */
  public const OPERATOR_TYPE_OPERATOR_TYPE_UNSPECIFIED = 'OPERATOR_TYPE_UNSPECIFIED';
  /**
   * Customer service representative.
   */
  public const OPERATOR_TYPE_CUSTOMER_SERVICE_REPRESENTATIVE = 'CUSTOMER_SERVICE_REPRESENTATIVE';
  /**
   * System auto job.
   */
  public const OPERATOR_TYPE_SYSTEM = 'SYSTEM';
  /**
   * Customer user.
   */
  public const OPERATOR_TYPE_CUSTOMER = 'CUSTOMER';
  /**
   * Reseller user.
   */
  public const OPERATOR_TYPE_RESELLER = 'RESELLER';
  /**
   * Not used.
   */
  public const SUSPENSION_REASON_SUSPENSION_REASON_UNSPECIFIED = 'SUSPENSION_REASON_UNSPECIFIED';
  /**
   * Entitlement was manually suspended by the Reseller.
   */
  public const SUSPENSION_REASON_RESELLER_INITIATED = 'RESELLER_INITIATED';
  /**
   * Trial ended.
   */
  public const SUSPENSION_REASON_TRIAL_ENDED = 'TRIAL_ENDED';
  /**
   * Entitlement renewal was canceled.
   */
  public const SUSPENSION_REASON_RENEWAL_WITH_TYPE_CANCEL = 'RENEWAL_WITH_TYPE_CANCEL';
  /**
   * Entitlement was automatically suspended on creation for pending ToS
   * acceptance on customer.
   */
  public const SUSPENSION_REASON_PENDING_TOS_ACCEPTANCE = 'PENDING_TOS_ACCEPTANCE';
  /**
   * Other reasons (internal reasons, abuse, etc.).
   */
  public const SUSPENSION_REASON_OTHER = 'OTHER';
  protected $collection_key = 'parameters';
  /**
   * The Entitlement's activation reason
   *
   * @var string
   */
  public $activationReason;
  /**
   * Cancellation reason for the Entitlement.
   *
   * @var string
   */
  public $cancellationReason;
  /**
   * The change action type.
   *
   * @var string
   */
  public $changeType;
  /**
   * The submitted time of the change.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. Resource name of an entitlement in the form:
   * accounts/{account_id}/customers/{customer_id}/entitlements/{entitlement_id}
   *
   * @var string
   */
  public $entitlement;
  /**
   * Required. Resource name of the Offer at the time of change. Takes the form:
   * accounts/{account_id}/offers/{offer_id}.
   *
   * @var string
   */
  public $offer;
  /**
   * Human-readable identifier that shows what operator made a change. When the
   * operator_type is RESELLER, this is the user's email address. For all other
   * operator types, this is empty.
   *
   * @var string
   */
  public $operator;
  /**
   * Operator type responsible for the change.
   *
   * @var string
   */
  public $operatorType;
  /**
   * e.g. purchase_number change reason, entered by CRS.
   *
   * @var string
   */
  public $otherChangeReason;
  protected $parametersType = GoogleCloudChannelV1Parameter::class;
  protected $parametersDataType = 'array';
  protected $provisionedServiceType = GoogleCloudChannelV1ProvisionedService::class;
  protected $provisionedServiceDataType = '';
  /**
   * Suspension reason for the Entitlement.
   *
   * @var string
   */
  public $suspensionReason;

  /**
   * The Entitlement's activation reason
   *
   * Accepted values: ACTIVATION_REASON_UNSPECIFIED,
   * RESELLER_REVOKED_SUSPENSION, CUSTOMER_ACCEPTED_PENDING_TOS,
   * RENEWAL_SETTINGS_CHANGED, OTHER_ACTIVATION_REASON
   *
   * @param self::ACTIVATION_REASON_* $activationReason
   */
  public function setActivationReason($activationReason)
  {
    $this->activationReason = $activationReason;
  }
  /**
   * @return self::ACTIVATION_REASON_*
   */
  public function getActivationReason()
  {
    return $this->activationReason;
  }
  /**
   * Cancellation reason for the Entitlement.
   *
   * Accepted values: CANCELLATION_REASON_UNSPECIFIED, SERVICE_TERMINATED,
   * RELATIONSHIP_ENDED, PARTIAL_TRANSFER
   *
   * @param self::CANCELLATION_REASON_* $cancellationReason
   */
  public function setCancellationReason($cancellationReason)
  {
    $this->cancellationReason = $cancellationReason;
  }
  /**
   * @return self::CANCELLATION_REASON_*
   */
  public function getCancellationReason()
  {
    return $this->cancellationReason;
  }
  /**
   * The change action type.
   *
   * Accepted values: CHANGE_TYPE_UNSPECIFIED, CREATED, PRICE_PLAN_SWITCHED,
   * COMMITMENT_CHANGED, RENEWED, SUSPENDED, ACTIVATED, CANCELLED, SKU_CHANGED,
   * RENEWAL_SETTING_CHANGED, PAID_SUBSCRIPTION_STARTED, LICENSE_CAP_CHANGED,
   * SUSPENSION_DETAILS_CHANGED, TRIAL_END_DATE_EXTENDED, TRIAL_STARTED
   *
   * @param self::CHANGE_TYPE_* $changeType
   */
  public function setChangeType($changeType)
  {
    $this->changeType = $changeType;
  }
  /**
   * @return self::CHANGE_TYPE_*
   */
  public function getChangeType()
  {
    return $this->changeType;
  }
  /**
   * The submitted time of the change.
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
   * Required. Resource name of an entitlement in the form:
   * accounts/{account_id}/customers/{customer_id}/entitlements/{entitlement_id}
   *
   * @param string $entitlement
   */
  public function setEntitlement($entitlement)
  {
    $this->entitlement = $entitlement;
  }
  /**
   * @return string
   */
  public function getEntitlement()
  {
    return $this->entitlement;
  }
  /**
   * Required. Resource name of the Offer at the time of change. Takes the form:
   * accounts/{account_id}/offers/{offer_id}.
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
   * Human-readable identifier that shows what operator made a change. When the
   * operator_type is RESELLER, this is the user's email address. For all other
   * operator types, this is empty.
   *
   * @param string $operator
   */
  public function setOperator($operator)
  {
    $this->operator = $operator;
  }
  /**
   * @return string
   */
  public function getOperator()
  {
    return $this->operator;
  }
  /**
   * Operator type responsible for the change.
   *
   * Accepted values: OPERATOR_TYPE_UNSPECIFIED,
   * CUSTOMER_SERVICE_REPRESENTATIVE, SYSTEM, CUSTOMER, RESELLER
   *
   * @param self::OPERATOR_TYPE_* $operatorType
   */
  public function setOperatorType($operatorType)
  {
    $this->operatorType = $operatorType;
  }
  /**
   * @return self::OPERATOR_TYPE_*
   */
  public function getOperatorType()
  {
    return $this->operatorType;
  }
  /**
   * e.g. purchase_number change reason, entered by CRS.
   *
   * @param string $otherChangeReason
   */
  public function setOtherChangeReason($otherChangeReason)
  {
    $this->otherChangeReason = $otherChangeReason;
  }
  /**
   * @return string
   */
  public function getOtherChangeReason()
  {
    return $this->otherChangeReason;
  }
  /**
   * Extended parameters, such as: purchase_order_number, gcp_details;
   * internal_correlation_id, long_running_operation_id, order_id; etc.
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
   * Service provisioned for an Entitlement.
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
   * Suspension reason for the Entitlement.
   *
   * Accepted values: SUSPENSION_REASON_UNSPECIFIED, RESELLER_INITIATED,
   * TRIAL_ENDED, RENEWAL_WITH_TYPE_CANCEL, PENDING_TOS_ACCEPTANCE, OTHER
   *
   * @param self::SUSPENSION_REASON_* $suspensionReason
   */
  public function setSuspensionReason($suspensionReason)
  {
    $this->suspensionReason = $suspensionReason;
  }
  /**
   * @return self::SUSPENSION_REASON_*
   */
  public function getSuspensionReason()
  {
    return $this->suspensionReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1EntitlementChange::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1EntitlementChange');
