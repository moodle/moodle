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

namespace Google\Service\Walletobjects;

class DiscoverableProgram extends \Google\Model
{
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Visible only to testers that have access to issuer account.
   */
  public const STATE_TRUSTED_TESTERS = 'TRUSTED_TESTERS';
  /**
   * Legacy alias for `TRUSTED_TESTERS`. Deprecated.
   *
   * @deprecated
   */
  public const STATE_trustedTesters = 'trustedTesters';
  /**
   * Visible to all.
   */
  public const STATE_LIVE = 'LIVE';
  /**
   * Legacy alias for `LIVE`. Deprecated.
   *
   * @deprecated
   */
  public const STATE_live = 'live';
  /**
   * Not visible.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * Legacy alias for `DISABLED`. Deprecated.
   *
   * @deprecated
   */
  public const STATE_disabled = 'disabled';
  protected $merchantSigninInfoType = DiscoverableProgramMerchantSigninInfo::class;
  protected $merchantSigninInfoDataType = '';
  protected $merchantSignupInfoType = DiscoverableProgramMerchantSignupInfo::class;
  protected $merchantSignupInfoDataType = '';
  /**
   * Visibility state of the discoverable program.
   *
   * @var string
   */
  public $state;

  /**
   * Information about the ability to signin and add a valuable for this program
   * through a merchant site. Used when MERCHANT_HOSTED_SIGNIN is enabled.
   *
   * @param DiscoverableProgramMerchantSigninInfo $merchantSigninInfo
   */
  public function setMerchantSigninInfo(DiscoverableProgramMerchantSigninInfo $merchantSigninInfo)
  {
    $this->merchantSigninInfo = $merchantSigninInfo;
  }
  /**
   * @return DiscoverableProgramMerchantSigninInfo
   */
  public function getMerchantSigninInfo()
  {
    return $this->merchantSigninInfo;
  }
  /**
   * Information about the ability to signup and add a valuable for this program
   * through a merchant site. Used when MERCHANT_HOSTED_SIGNUP is enabled.
   *
   * @param DiscoverableProgramMerchantSignupInfo $merchantSignupInfo
   */
  public function setMerchantSignupInfo(DiscoverableProgramMerchantSignupInfo $merchantSignupInfo)
  {
    $this->merchantSignupInfo = $merchantSignupInfo;
  }
  /**
   * @return DiscoverableProgramMerchantSignupInfo
   */
  public function getMerchantSignupInfo()
  {
    return $this->merchantSignupInfo;
  }
  /**
   * Visibility state of the discoverable program.
   *
   * Accepted values: STATE_UNSPECIFIED, TRUSTED_TESTERS, trustedTesters, LIVE,
   * live, DISABLED, disabled
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
class_alias(DiscoverableProgram::class, 'Google_Service_Walletobjects_DiscoverableProgram');
