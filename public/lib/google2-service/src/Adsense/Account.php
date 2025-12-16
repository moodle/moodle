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

namespace Google\Service\Adsense;

class Account extends \Google\Collection
{
  /**
   * State unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The account is open and ready to serve ads.
   */
  public const STATE_READY = 'READY';
  /**
   * There are some issues with this account. Publishers should visit AdSense in
   * order to fix the account.
   */
  public const STATE_NEEDS_ATTENTION = 'NEEDS_ATTENTION';
  /**
   * The account is closed and can't serve ads.
   */
  public const STATE_CLOSED = 'CLOSED';
  protected $collection_key = 'pendingTasks';
  /**
   * Output only. Creation time of the account.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Display name of this account.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Resource name of the account. Format: accounts/pub-[0-9]+
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Outstanding tasks that need to be completed as part of the
   * sign-up process for a new account. e.g. "billing-profile-creation", "phone-
   * pin-verification".
   *
   * @var string[]
   */
  public $pendingTasks;
  /**
   * Output only. Whether this account is premium. Premium accounts have access
   * to additional spam-related metrics.
   *
   * @var bool
   */
  public $premium;
  /**
   * Output only. State of the account.
   *
   * @var string
   */
  public $state;
  protected $timeZoneType = TimeZone::class;
  protected $timeZoneDataType = '';

  /**
   * Output only. Creation time of the account.
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
   * Output only. Display name of this account.
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
   * Output only. Resource name of the account. Format: accounts/pub-[0-9]+
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
   * Output only. Outstanding tasks that need to be completed as part of the
   * sign-up process for a new account. e.g. "billing-profile-creation", "phone-
   * pin-verification".
   *
   * @param string[] $pendingTasks
   */
  public function setPendingTasks($pendingTasks)
  {
    $this->pendingTasks = $pendingTasks;
  }
  /**
   * @return string[]
   */
  public function getPendingTasks()
  {
    return $this->pendingTasks;
  }
  /**
   * Output only. Whether this account is premium. Premium accounts have access
   * to additional spam-related metrics.
   *
   * @param bool $premium
   */
  public function setPremium($premium)
  {
    $this->premium = $premium;
  }
  /**
   * @return bool
   */
  public function getPremium()
  {
    return $this->premium;
  }
  /**
   * Output only. State of the account.
   *
   * Accepted values: STATE_UNSPECIFIED, READY, NEEDS_ATTENTION, CLOSED
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
   * The account time zone, as used by reporting. For more information, see
   * [changing the time zone of your
   * reports](https://support.google.com/adsense/answer/9830725).
   *
   * @param TimeZone $timeZone
   */
  public function setTimeZone(TimeZone $timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return TimeZone
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Account::class, 'Google_Service_Adsense_Account');
