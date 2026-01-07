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

class AccountItemUpdatesSettings extends \Google\Model
{
  /**
   * If availability updates are enabled, any previous availability values get
   * overwritten if Google finds an out-of-stock annotation on the offer's page.
   * If additionally `allow_availability_updates` field is set to true, values
   * get overwritten if Google finds an in-stock annotation on the offer’s page.
   *
   * @var bool
   */
  public $allowAvailabilityUpdates;
  /**
   * If condition updates are enabled, Google always updates item condition with
   * the condition detected from the details of your product.
   *
   * @var bool
   */
  public $allowConditionUpdates;
  /**
   * If price updates are enabled, Google always updates the active price with
   * the crawled information.
   *
   * @var bool
   */
  public $allowPriceUpdates;
  /**
   * If allow_availability_updates is enabled, items are automatically updated
   * in all your Shopping target countries. By default, availability updates
   * will only be applied to items that are 'out of stock' on your website but
   * 'in stock' on Shopping. Set this to true to also update items that are 'in
   * stock' on your website, but 'out of stock' on Google Shopping. In order for
   * this field to have an effect, you must also allow availability updates.
   *
   * @var bool
   */
  public $allowStrictAvailabilityUpdates;

  /**
   * If availability updates are enabled, any previous availability values get
   * overwritten if Google finds an out-of-stock annotation on the offer's page.
   * If additionally `allow_availability_updates` field is set to true, values
   * get overwritten if Google finds an in-stock annotation on the offer’s page.
   *
   * @param bool $allowAvailabilityUpdates
   */
  public function setAllowAvailabilityUpdates($allowAvailabilityUpdates)
  {
    $this->allowAvailabilityUpdates = $allowAvailabilityUpdates;
  }
  /**
   * @return bool
   */
  public function getAllowAvailabilityUpdates()
  {
    return $this->allowAvailabilityUpdates;
  }
  /**
   * If condition updates are enabled, Google always updates item condition with
   * the condition detected from the details of your product.
   *
   * @param bool $allowConditionUpdates
   */
  public function setAllowConditionUpdates($allowConditionUpdates)
  {
    $this->allowConditionUpdates = $allowConditionUpdates;
  }
  /**
   * @return bool
   */
  public function getAllowConditionUpdates()
  {
    return $this->allowConditionUpdates;
  }
  /**
   * If price updates are enabled, Google always updates the active price with
   * the crawled information.
   *
   * @param bool $allowPriceUpdates
   */
  public function setAllowPriceUpdates($allowPriceUpdates)
  {
    $this->allowPriceUpdates = $allowPriceUpdates;
  }
  /**
   * @return bool
   */
  public function getAllowPriceUpdates()
  {
    return $this->allowPriceUpdates;
  }
  /**
   * If allow_availability_updates is enabled, items are automatically updated
   * in all your Shopping target countries. By default, availability updates
   * will only be applied to items that are 'out of stock' on your website but
   * 'in stock' on Shopping. Set this to true to also update items that are 'in
   * stock' on your website, but 'out of stock' on Google Shopping. In order for
   * this field to have an effect, you must also allow availability updates.
   *
   * @param bool $allowStrictAvailabilityUpdates
   */
  public function setAllowStrictAvailabilityUpdates($allowStrictAvailabilityUpdates)
  {
    $this->allowStrictAvailabilityUpdates = $allowStrictAvailabilityUpdates;
  }
  /**
   * @return bool
   */
  public function getAllowStrictAvailabilityUpdates()
  {
    return $this->allowStrictAvailabilityUpdates;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountItemUpdatesSettings::class, 'Google_Service_ShoppingContent_AccountItemUpdatesSettings');
