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

namespace Google\Service\CloudDomains;

class ManagementSettings extends \Google\Model
{
  /**
   * The state is unspecified.
   */
  public const EFFECTIVE_TRANSFER_LOCK_STATE_TRANSFER_LOCK_STATE_UNSPECIFIED = 'TRANSFER_LOCK_STATE_UNSPECIFIED';
  /**
   * The domain is unlocked and can be transferred to another registrar.
   */
  public const EFFECTIVE_TRANSFER_LOCK_STATE_UNLOCKED = 'UNLOCKED';
  /**
   * The domain is locked and cannot be transferred to another registrar.
   */
  public const EFFECTIVE_TRANSFER_LOCK_STATE_LOCKED = 'LOCKED';
  /**
   * The renewal method is undefined.
   */
  public const PREFERRED_RENEWAL_METHOD_RENEWAL_METHOD_UNSPECIFIED = 'RENEWAL_METHOD_UNSPECIFIED';
  /**
   * The domain is automatically renewed each year.
   */
  public const PREFERRED_RENEWAL_METHOD_AUTOMATIC_RENEWAL = 'AUTOMATIC_RENEWAL';
  /**
   * Deprecated: For more information, see [Cloud Domains feature
   * deprecation](https://cloud.google.com/domains/docs/deprecations/feature-
   * deprecations). This option was never used. Use `RENEWAL_DISABLED` instead.
   *
   * @deprecated
   */
  public const PREFERRED_RENEWAL_METHOD_MANUAL_RENEWAL = 'MANUAL_RENEWAL';
  /**
   * The domain won't be renewed and will expire at its expiration time.
   */
  public const PREFERRED_RENEWAL_METHOD_RENEWAL_DISABLED = 'RENEWAL_DISABLED';
  /**
   * The renewal method is undefined.
   */
  public const RENEWAL_METHOD_RENEWAL_METHOD_UNSPECIFIED = 'RENEWAL_METHOD_UNSPECIFIED';
  /**
   * The domain is automatically renewed each year.
   */
  public const RENEWAL_METHOD_AUTOMATIC_RENEWAL = 'AUTOMATIC_RENEWAL';
  /**
   * Deprecated: For more information, see [Cloud Domains feature
   * deprecation](https://cloud.google.com/domains/docs/deprecations/feature-
   * deprecations). This option was never used. Use `RENEWAL_DISABLED` instead.
   *
   * @deprecated
   */
  public const RENEWAL_METHOD_MANUAL_RENEWAL = 'MANUAL_RENEWAL';
  /**
   * The domain won't be renewed and will expire at its expiration time.
   */
  public const RENEWAL_METHOD_RENEWAL_DISABLED = 'RENEWAL_DISABLED';
  /**
   * The state is unspecified.
   */
  public const TRANSFER_LOCK_STATE_TRANSFER_LOCK_STATE_UNSPECIFIED = 'TRANSFER_LOCK_STATE_UNSPECIFIED';
  /**
   * The domain is unlocked and can be transferred to another registrar.
   */
  public const TRANSFER_LOCK_STATE_UNLOCKED = 'UNLOCKED';
  /**
   * The domain is locked and cannot be transferred to another registrar.
   */
  public const TRANSFER_LOCK_STATE_LOCKED = 'LOCKED';
  /**
   * Output only. The actual transfer lock state for this `Registration`.
   *
   * @var string
   */
  public $effectiveTransferLockState;
  /**
   * Optional. The desired renewal method for this `Registration`. The actual
   * `renewal_method` is automatically updated to reflect this choice. If unset
   * or equal to `RENEWAL_METHOD_UNSPECIFIED`, the actual `renewalMethod` is
   * treated as if it were set to `AUTOMATIC_RENEWAL`. You cannot use
   * `RENEWAL_DISABLED` during resource creation, and you can update the renewal
   * status only when the `Registration` resource has state `ACTIVE` or
   * `SUSPENDED`. When `preferred_renewal_method` is set to `AUTOMATIC_RENEWAL`,
   * the actual `renewal_method` can be set to `RENEWAL_DISABLED` in case of
   * problems with the billing account or reported domain abuse. In such cases,
   * check the `issues` field on the `Registration`. After the problem is
   * resolved, the `renewal_method` is automatically updated to
   * `preferred_renewal_method` in a few hours.
   *
   * @var string
   */
  public $preferredRenewalMethod;
  /**
   * Output only. The actual renewal method for this `Registration`. When
   * `preferred_renewal_method` is set to `AUTOMATIC_RENEWAL`, the actual
   * `renewal_method` can be equal to `RENEWAL_DISABLED`—for example, when there
   * are problems with the billing account or reported domain abuse. In such
   * cases, check the `issues` field on the `Registration`. After the problem is
   * resolved, the `renewal_method` is automatically updated to
   * `preferred_renewal_method` in a few hours.
   *
   * @var string
   */
  public $renewalMethod;
  /**
   * This is the desired transfer lock state for this `Registration`. A transfer
   * lock controls whether the domain can be transferred to another registrar.
   * The transfer lock state of the domain is returned in the
   * `effective_transfer_lock_state` property. The transfer lock state values
   * might be different for the following reasons: * `transfer_lock_state` was
   * updated only a short time ago. * Domains with the
   * `TRANSFER_LOCK_UNSUPPORTED_BY_REGISTRY` state are in the list of
   * `domain_properties`. These domains are always in the `UNLOCKED` state.
   *
   * @var string
   */
  public $transferLockState;

  /**
   * Output only. The actual transfer lock state for this `Registration`.
   *
   * Accepted values: TRANSFER_LOCK_STATE_UNSPECIFIED, UNLOCKED, LOCKED
   *
   * @param self::EFFECTIVE_TRANSFER_LOCK_STATE_* $effectiveTransferLockState
   */
  public function setEffectiveTransferLockState($effectiveTransferLockState)
  {
    $this->effectiveTransferLockState = $effectiveTransferLockState;
  }
  /**
   * @return self::EFFECTIVE_TRANSFER_LOCK_STATE_*
   */
  public function getEffectiveTransferLockState()
  {
    return $this->effectiveTransferLockState;
  }
  /**
   * Optional. The desired renewal method for this `Registration`. The actual
   * `renewal_method` is automatically updated to reflect this choice. If unset
   * or equal to `RENEWAL_METHOD_UNSPECIFIED`, the actual `renewalMethod` is
   * treated as if it were set to `AUTOMATIC_RENEWAL`. You cannot use
   * `RENEWAL_DISABLED` during resource creation, and you can update the renewal
   * status only when the `Registration` resource has state `ACTIVE` or
   * `SUSPENDED`. When `preferred_renewal_method` is set to `AUTOMATIC_RENEWAL`,
   * the actual `renewal_method` can be set to `RENEWAL_DISABLED` in case of
   * problems with the billing account or reported domain abuse. In such cases,
   * check the `issues` field on the `Registration`. After the problem is
   * resolved, the `renewal_method` is automatically updated to
   * `preferred_renewal_method` in a few hours.
   *
   * Accepted values: RENEWAL_METHOD_UNSPECIFIED, AUTOMATIC_RENEWAL,
   * MANUAL_RENEWAL, RENEWAL_DISABLED
   *
   * @param self::PREFERRED_RENEWAL_METHOD_* $preferredRenewalMethod
   */
  public function setPreferredRenewalMethod($preferredRenewalMethod)
  {
    $this->preferredRenewalMethod = $preferredRenewalMethod;
  }
  /**
   * @return self::PREFERRED_RENEWAL_METHOD_*
   */
  public function getPreferredRenewalMethod()
  {
    return $this->preferredRenewalMethod;
  }
  /**
   * Output only. The actual renewal method for this `Registration`. When
   * `preferred_renewal_method` is set to `AUTOMATIC_RENEWAL`, the actual
   * `renewal_method` can be equal to `RENEWAL_DISABLED`—for example, when there
   * are problems with the billing account or reported domain abuse. In such
   * cases, check the `issues` field on the `Registration`. After the problem is
   * resolved, the `renewal_method` is automatically updated to
   * `preferred_renewal_method` in a few hours.
   *
   * Accepted values: RENEWAL_METHOD_UNSPECIFIED, AUTOMATIC_RENEWAL,
   * MANUAL_RENEWAL, RENEWAL_DISABLED
   *
   * @param self::RENEWAL_METHOD_* $renewalMethod
   */
  public function setRenewalMethod($renewalMethod)
  {
    $this->renewalMethod = $renewalMethod;
  }
  /**
   * @return self::RENEWAL_METHOD_*
   */
  public function getRenewalMethod()
  {
    return $this->renewalMethod;
  }
  /**
   * This is the desired transfer lock state for this `Registration`. A transfer
   * lock controls whether the domain can be transferred to another registrar.
   * The transfer lock state of the domain is returned in the
   * `effective_transfer_lock_state` property. The transfer lock state values
   * might be different for the following reasons: * `transfer_lock_state` was
   * updated only a short time ago. * Domains with the
   * `TRANSFER_LOCK_UNSUPPORTED_BY_REGISTRY` state are in the list of
   * `domain_properties`. These domains are always in the `UNLOCKED` state.
   *
   * Accepted values: TRANSFER_LOCK_STATE_UNSPECIFIED, UNLOCKED, LOCKED
   *
   * @param self::TRANSFER_LOCK_STATE_* $transferLockState
   */
  public function setTransferLockState($transferLockState)
  {
    $this->transferLockState = $transferLockState;
  }
  /**
   * @return self::TRANSFER_LOCK_STATE_*
   */
  public function getTransferLockState()
  {
    return $this->transferLockState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ManagementSettings::class, 'Google_Service_CloudDomains_ManagementSettings');
