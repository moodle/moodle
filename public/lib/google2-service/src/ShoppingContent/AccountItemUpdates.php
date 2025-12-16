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

namespace Google\Service\ShoppingContent;

class AccountItemUpdates extends \Google\Model
{
  protected $accountItemUpdatesSettingsType = AccountItemUpdatesSettings::class;
  protected $accountItemUpdatesSettingsDataType = '';
  /**
   * Output only. The effective value of allow_availability_updates. If
   * account_item_updates_settings is present, then this value is the same.
   * Otherwise, it represents the inherited value of the parent account. Read-
   * only.
   *
   * @var bool
   */
  public $effectiveAllowAvailabilityUpdates;
  /**
   * Output only. The effective value of allow_condition_updates. If
   * account_item_updates_settings is present, then this value is the same.
   * Otherwise, it represents the inherited value of the parent account. Read-
   * only.
   *
   * @var bool
   */
  public $effectiveAllowConditionUpdates;
  /**
   * Output only. The effective value of allow_price_updates. If
   * account_item_updates_settings is present, then this value is the same.
   * Otherwise, it represents the inherited value of the parent account. Read-
   * only.
   *
   * @var bool
   */
  public $effectiveAllowPriceUpdates;
  /**
   * Output only. The effective value of allow_strict_availability_updates. If
   * account_item_updates_settings is present, then this value is the same.
   * Otherwise, it represents the inherited value of the parent account. Read-
   * only.
   *
   * @var bool
   */
  public $effectiveAllowStrictAvailabilityUpdates;

  /**
   * Determines which attributes of the items should be automatically updated.
   * If this field is not present, then the settings will be deleted. If there
   * are no settings for subaccount, they are inherited from aggregator.
   *
   * @param AccountItemUpdatesSettings $accountItemUpdatesSettings
   */
  public function setAccountItemUpdatesSettings(AccountItemUpdatesSettings $accountItemUpdatesSettings)
  {
    $this->accountItemUpdatesSettings = $accountItemUpdatesSettings;
  }
  /**
   * @return AccountItemUpdatesSettings
   */
  public function getAccountItemUpdatesSettings()
  {
    return $this->accountItemUpdatesSettings;
  }
  /**
   * Output only. The effective value of allow_availability_updates. If
   * account_item_updates_settings is present, then this value is the same.
   * Otherwise, it represents the inherited value of the parent account. Read-
   * only.
   *
   * @param bool $effectiveAllowAvailabilityUpdates
   */
  public function setEffectiveAllowAvailabilityUpdates($effectiveAllowAvailabilityUpdates)
  {
    $this->effectiveAllowAvailabilityUpdates = $effectiveAllowAvailabilityUpdates;
  }
  /**
   * @return bool
   */
  public function getEffectiveAllowAvailabilityUpdates()
  {
    return $this->effectiveAllowAvailabilityUpdates;
  }
  /**
   * Output only. The effective value of allow_condition_updates. If
   * account_item_updates_settings is present, then this value is the same.
   * Otherwise, it represents the inherited value of the parent account. Read-
   * only.
   *
   * @param bool $effectiveAllowConditionUpdates
   */
  public function setEffectiveAllowConditionUpdates($effectiveAllowConditionUpdates)
  {
    $this->effectiveAllowConditionUpdates = $effectiveAllowConditionUpdates;
  }
  /**
   * @return bool
   */
  public function getEffectiveAllowConditionUpdates()
  {
    return $this->effectiveAllowConditionUpdates;
  }
  /**
   * Output only. The effective value of allow_price_updates. If
   * account_item_updates_settings is present, then this value is the same.
   * Otherwise, it represents the inherited value of the parent account. Read-
   * only.
   *
   * @param bool $effectiveAllowPriceUpdates
   */
  public function setEffectiveAllowPriceUpdates($effectiveAllowPriceUpdates)
  {
    $this->effectiveAllowPriceUpdates = $effectiveAllowPriceUpdates;
  }
  /**
   * @return bool
   */
  public function getEffectiveAllowPriceUpdates()
  {
    return $this->effectiveAllowPriceUpdates;
  }
  /**
   * Output only. The effective value of allow_strict_availability_updates. If
   * account_item_updates_settings is present, then this value is the same.
   * Otherwise, it represents the inherited value of the parent account. Read-
   * only.
   *
   * @param bool $effectiveAllowStrictAvailabilityUpdates
   */
  public function setEffectiveAllowStrictAvailabilityUpdates($effectiveAllowStrictAvailabilityUpdates)
  {
    $this->effectiveAllowStrictAvailabilityUpdates = $effectiveAllowStrictAvailabilityUpdates;
  }
  /**
   * @return bool
   */
  public function getEffectiveAllowStrictAvailabilityUpdates()
  {
    return $this->effectiveAllowStrictAvailabilityUpdates;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountItemUpdates::class, 'Google_Service_ShoppingContent_AccountItemUpdates');
