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

namespace Google\Service\OracleDatabase;

class Entitlement extends \Google\Model
{
  /**
   * Default unspecified value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Account not linked.
   */
  public const STATE_ACCOUNT_NOT_LINKED = 'ACCOUNT_NOT_LINKED';
  /**
   * Account is linked but not active.
   */
  public const STATE_ACCOUNT_NOT_ACTIVE = 'ACCOUNT_NOT_ACTIVE';
  /**
   * Entitlement and Account are active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Account is suspended.
   */
  public const STATE_ACCOUNT_SUSPENDED = 'ACCOUNT_SUSPENDED';
  /**
   * Entitlement is not approved in private marketplace.
   */
  public const STATE_NOT_APPROVED_IN_PRIVATE_MARKETPLACE = 'NOT_APPROVED_IN_PRIVATE_MARKETPLACE';
  protected $cloudAccountDetailsType = CloudAccountDetails::class;
  protected $cloudAccountDetailsDataType = '';
  /**
   * Output only. Google Cloud Marketplace order ID (aka entitlement ID)
   *
   * @var string
   */
  public $entitlementId;
  /**
   * Identifier. The name of the Entitlement resource with the format:
   * projects/{project}/locations/{region}/entitlements/{entitlement}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Entitlement State.
   *
   * @var string
   */
  public $state;

  /**
   * Details of the OCI Cloud Account.
   *
   * @param CloudAccountDetails $cloudAccountDetails
   */
  public function setCloudAccountDetails(CloudAccountDetails $cloudAccountDetails)
  {
    $this->cloudAccountDetails = $cloudAccountDetails;
  }
  /**
   * @return CloudAccountDetails
   */
  public function getCloudAccountDetails()
  {
    return $this->cloudAccountDetails;
  }
  /**
   * Output only. Google Cloud Marketplace order ID (aka entitlement ID)
   *
   * @param string $entitlementId
   */
  public function setEntitlementId($entitlementId)
  {
    $this->entitlementId = $entitlementId;
  }
  /**
   * @return string
   */
  public function getEntitlementId()
  {
    return $this->entitlementId;
  }
  /**
   * Identifier. The name of the Entitlement resource with the format:
   * projects/{project}/locations/{region}/entitlements/{entitlement}
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
   * Output only. Entitlement State.
   *
   * Accepted values: STATE_UNSPECIFIED, ACCOUNT_NOT_LINKED, ACCOUNT_NOT_ACTIVE,
   * ACTIVE, ACCOUNT_SUSPENDED, NOT_APPROVED_IN_PRIVATE_MARKETPLACE
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
class_alias(Entitlement::class, 'Google_Service_OracleDatabase_Entitlement');
