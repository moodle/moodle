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

namespace Google\Service\AlertCenter;

class AccountSuspensionWarning extends \Google\Collection
{
  /**
   * State is unspecified.
   */
  public const STATE_ACCOUNT_SUSPENSION_WARNING_STATE_UNSPECIFIED = 'ACCOUNT_SUSPENSION_WARNING_STATE_UNSPECIFIED';
  /**
   * Customer is receiving a warning about imminent suspension.
   */
  public const STATE_WARNING = 'WARNING';
  /**
   * Customer is being notified that their account has been suspended.
   */
  public const STATE_SUSPENDED = 'SUSPENDED';
  /**
   * Customer is being notified that their suspension appeal was approved.
   */
  public const STATE_APPEAL_APPROVED = 'APPEAL_APPROVED';
  /**
   * Customer has submitted their appeal, which is pending review.
   */
  public const STATE_APPEAL_SUBMITTED = 'APPEAL_SUBMITTED';
  protected $collection_key = 'suspensionDetails';
  /**
   * The amount of time remaining to appeal an imminent suspension. After this
   * window has elapsed, the account will be suspended. Only populated if the
   * account suspension is in WARNING state.
   *
   * @var string
   */
  public $appealWindow;
  /**
   * Account suspension warning state.
   *
   * @var string
   */
  public $state;
  protected $suspensionDetailsType = AccountSuspensionDetails::class;
  protected $suspensionDetailsDataType = 'array';

  /**
   * The amount of time remaining to appeal an imminent suspension. After this
   * window has elapsed, the account will be suspended. Only populated if the
   * account suspension is in WARNING state.
   *
   * @param string $appealWindow
   */
  public function setAppealWindow($appealWindow)
  {
    $this->appealWindow = $appealWindow;
  }
  /**
   * @return string
   */
  public function getAppealWindow()
  {
    return $this->appealWindow;
  }
  /**
   * Account suspension warning state.
   *
   * Accepted values: ACCOUNT_SUSPENSION_WARNING_STATE_UNSPECIFIED, WARNING,
   * SUSPENDED, APPEAL_APPROVED, APPEAL_SUBMITTED
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
   * Details about why an account is being suspended.
   *
   * @param AccountSuspensionDetails[] $suspensionDetails
   */
  public function setSuspensionDetails($suspensionDetails)
  {
    $this->suspensionDetails = $suspensionDetails;
  }
  /**
   * @return AccountSuspensionDetails[]
   */
  public function getSuspensionDetails()
  {
    return $this->suspensionDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountSuspensionWarning::class, 'Google_Service_AlertCenter_AccountSuspensionWarning');
