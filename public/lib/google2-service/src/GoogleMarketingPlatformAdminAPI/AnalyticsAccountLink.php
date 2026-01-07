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

namespace Google\Service\GoogleMarketingPlatformAdminAPI;

class AnalyticsAccountLink extends \Google\Model
{
  /**
   * The link state is unknown.
   */
  public const LINK_VERIFICATION_STATE_LINK_VERIFICATION_STATE_UNSPECIFIED = 'LINK_VERIFICATION_STATE_UNSPECIFIED';
  /**
   * The link is established.
   */
  public const LINK_VERIFICATION_STATE_LINK_VERIFICATION_STATE_VERIFIED = 'LINK_VERIFICATION_STATE_VERIFIED';
  /**
   * The link is requested, but hasn't been approved by the product account
   * admin.
   */
  public const LINK_VERIFICATION_STATE_LINK_VERIFICATION_STATE_NOT_VERIFIED = 'LINK_VERIFICATION_STATE_NOT_VERIFIED';
  /**
   * Required. Immutable. The resource name of the AnalyticsAdmin API account.
   * The account ID will be used as the ID of this AnalyticsAccountLink
   * resource, which will become the final component of the resource name.
   * Format: analyticsadmin.googleapis.com/accounts/{account_id}
   *
   * @var string
   */
  public $analyticsAccount;
  /**
   * Output only. The human-readable name for the Analytics account.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The verification state of the link between the Analytics
   * account and the parent organization.
   *
   * @var string
   */
  public $linkVerificationState;
  /**
   * Identifier. Resource name of this AnalyticsAccountLink. Note the resource
   * ID is the same as the ID of the Analtyics account. Format:
   * organizations/{org_id}/analyticsAccountLinks/{analytics_account_link_id}
   * Example: "organizations/xyz/analyticsAccountLinks/1234"
   *
   * @var string
   */
  public $name;

  /**
   * Required. Immutable. The resource name of the AnalyticsAdmin API account.
   * The account ID will be used as the ID of this AnalyticsAccountLink
   * resource, which will become the final component of the resource name.
   * Format: analyticsadmin.googleapis.com/accounts/{account_id}
   *
   * @param string $analyticsAccount
   */
  public function setAnalyticsAccount($analyticsAccount)
  {
    $this->analyticsAccount = $analyticsAccount;
  }
  /**
   * @return string
   */
  public function getAnalyticsAccount()
  {
    return $this->analyticsAccount;
  }
  /**
   * Output only. The human-readable name for the Analytics account.
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
   * Output only. The verification state of the link between the Analytics
   * account and the parent organization.
   *
   * Accepted values: LINK_VERIFICATION_STATE_UNSPECIFIED,
   * LINK_VERIFICATION_STATE_VERIFIED, LINK_VERIFICATION_STATE_NOT_VERIFIED
   *
   * @param self::LINK_VERIFICATION_STATE_* $linkVerificationState
   */
  public function setLinkVerificationState($linkVerificationState)
  {
    $this->linkVerificationState = $linkVerificationState;
  }
  /**
   * @return self::LINK_VERIFICATION_STATE_*
   */
  public function getLinkVerificationState()
  {
    return $this->linkVerificationState;
  }
  /**
   * Identifier. Resource name of this AnalyticsAccountLink. Note the resource
   * ID is the same as the ID of the Analtyics account. Format:
   * organizations/{org_id}/analyticsAccountLinks/{analytics_account_link_id}
   * Example: "organizations/xyz/analyticsAccountLinks/1234"
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnalyticsAccountLink::class, 'Google_Service_GoogleMarketingPlatformAdminAPI_AnalyticsAccountLink');
