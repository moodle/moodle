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

namespace Google\Service\CloudRedis;

class Entitlement extends \Google\Model
{
  public const ENTITLEMENT_STATE_ENTITLEMENT_STATE_UNSPECIFIED = 'ENTITLEMENT_STATE_UNSPECIFIED';
  /**
   * User is entitled to a feature/benefit, but whether it has been successfully
   * provisioned is decided by provisioning state.
   */
  public const ENTITLEMENT_STATE_ENTITLED = 'ENTITLED';
  /**
   * User is entitled to a feature/benefit, but it was requested to be revoked.
   * Whether the revoke has been successful is decided by provisioning state.
   */
  public const ENTITLEMENT_STATE_REVOKED = 'REVOKED';
  /**
   * The entitlement type is unspecified.
   */
  public const TYPE_ENTITLEMENT_TYPE_UNSPECIFIED = 'ENTITLEMENT_TYPE_UNSPECIFIED';
  /**
   * The root entitlement representing Gemini package ownership.This will no
   * longer be supported in the future.
   *
   * @deprecated
   */
  public const TYPE_GEMINI = 'GEMINI';
  /**
   * The entitlement representing Native Tier, This will be the default
   * Entitlement going forward with GCA Enablement.
   */
  public const TYPE_NATIVE = 'NATIVE';
  /**
   * The entitlement representing GCA-Standard Tier.
   */
  public const TYPE_GCA_STANDARD = 'GCA_STANDARD';
  /**
   * The current state of user's accessibility to a feature/benefit.
   *
   * @var string
   */
  public $entitlementState;
  /**
   * An enum that represents the type of this entitlement.
   *
   * @var string
   */
  public $type;

  /**
   * The current state of user's accessibility to a feature/benefit.
   *
   * Accepted values: ENTITLEMENT_STATE_UNSPECIFIED, ENTITLED, REVOKED
   *
   * @param self::ENTITLEMENT_STATE_* $entitlementState
   */
  public function setEntitlementState($entitlementState)
  {
    $this->entitlementState = $entitlementState;
  }
  /**
   * @return self::ENTITLEMENT_STATE_*
   */
  public function getEntitlementState()
  {
    return $this->entitlementState;
  }
  /**
   * An enum that represents the type of this entitlement.
   *
   * Accepted values: ENTITLEMENT_TYPE_UNSPECIFIED, GEMINI, NATIVE, GCA_STANDARD
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Entitlement::class, 'Google_Service_CloudRedis_Entitlement');
