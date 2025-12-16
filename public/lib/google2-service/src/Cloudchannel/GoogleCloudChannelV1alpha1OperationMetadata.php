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

class GoogleCloudChannelV1alpha1OperationMetadata extends \Google\Model
{
  /**
   * Not used.
   */
  public const OPERATION_TYPE_OPERATION_TYPE_UNSPECIFIED = 'OPERATION_TYPE_UNSPECIFIED';
  /**
   * Long Running Operation was triggered by CreateEntitlement.
   */
  public const OPERATION_TYPE_CREATE_ENTITLEMENT = 'CREATE_ENTITLEMENT';
  /**
   * Long Running Operation was triggered by ChangeQuantity.
   */
  public const OPERATION_TYPE_CHANGE_QUANTITY = 'CHANGE_QUANTITY';
  /**
   * Long Running Operation was triggered by ChangeRenewalSettings.
   */
  public const OPERATION_TYPE_CHANGE_RENEWAL_SETTINGS = 'CHANGE_RENEWAL_SETTINGS';
  /**
   * Long Running Operation was triggered by ChangePlan.
   */
  public const OPERATION_TYPE_CHANGE_PLAN = 'CHANGE_PLAN';
  /**
   * Long Running Operation was triggered by StartPaidService.
   */
  public const OPERATION_TYPE_START_PAID_SERVICE = 'START_PAID_SERVICE';
  /**
   * Long Running Operation was triggered by ChangeSku.
   */
  public const OPERATION_TYPE_CHANGE_SKU = 'CHANGE_SKU';
  /**
   * Long Running Operation was triggered by ActivateEntitlement.
   */
  public const OPERATION_TYPE_ACTIVATE_ENTITLEMENT = 'ACTIVATE_ENTITLEMENT';
  /**
   * Long Running Operation was triggered by SuspendEntitlement.
   */
  public const OPERATION_TYPE_SUSPEND_ENTITLEMENT = 'SUSPEND_ENTITLEMENT';
  /**
   * Long Running Operation was triggered by CancelEntitlement.
   */
  public const OPERATION_TYPE_CANCEL_ENTITLEMENT = 'CANCEL_ENTITLEMENT';
  /**
   * Long Running Operation was triggered by TransferEntitlements.
   */
  public const OPERATION_TYPE_TRANSFER_ENTITLEMENTS = 'TRANSFER_ENTITLEMENTS';
  /**
   * Long Running Operation was triggered by TransferEntitlementsToGoogle.
   */
  public const OPERATION_TYPE_TRANSFER_ENTITLEMENTS_TO_GOOGLE = 'TRANSFER_ENTITLEMENTS_TO_GOOGLE';
  /**
   * Long Running Operation was triggered by ChangeOffer.
   */
  public const OPERATION_TYPE_CHANGE_OFFER = 'CHANGE_OFFER';
  /**
   * Long Running Operation was triggered by ChangeParameters.
   */
  public const OPERATION_TYPE_CHANGE_PARAMETERS = 'CHANGE_PARAMETERS';
  /**
   * Long Running Operation was triggered by ProvisionCloudIdentity.
   */
  public const OPERATION_TYPE_PROVISION_CLOUD_IDENTITY = 'PROVISION_CLOUD_IDENTITY';
  /**
   * The RPC that initiated this Long Running Operation.
   *
   * @var string
   */
  public $operationType;

  /**
   * The RPC that initiated this Long Running Operation.
   *
   * Accepted values: OPERATION_TYPE_UNSPECIFIED, CREATE_ENTITLEMENT,
   * CHANGE_QUANTITY, CHANGE_RENEWAL_SETTINGS, CHANGE_PLAN, START_PAID_SERVICE,
   * CHANGE_SKU, ACTIVATE_ENTITLEMENT, SUSPEND_ENTITLEMENT, CANCEL_ENTITLEMENT,
   * TRANSFER_ENTITLEMENTS, TRANSFER_ENTITLEMENTS_TO_GOOGLE, CHANGE_OFFER,
   * CHANGE_PARAMETERS, PROVISION_CLOUD_IDENTITY
   *
   * @param self::OPERATION_TYPE_* $operationType
   */
  public function setOperationType($operationType)
  {
    $this->operationType = $operationType;
  }
  /**
   * @return self::OPERATION_TYPE_*
   */
  public function getOperationType()
  {
    return $this->operationType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1alpha1OperationMetadata::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1alpha1OperationMetadata');
