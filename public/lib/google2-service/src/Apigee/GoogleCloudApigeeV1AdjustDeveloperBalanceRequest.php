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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1AdjustDeveloperBalanceRequest extends \Google\Model
{
  protected $adjustmentType = GoogleTypeMoney::class;
  protected $adjustmentDataType = '';

  /**
   * * A positive value of `adjustment` means that that the API provider wants
   * to adjust the balance for an under-charged developer i.e. the balance of
   * the developer will decrease. * A negative value of `adjustment` means that
   * that the API provider wants to adjust the balance for an over-charged
   * developer i.e. the balance of the developer will increase. NOTE: An
   * adjustment cannot increase the balance of the developer beyond the balance
   * as of the most recent credit. For example, if a developer's balance is
   * updated to be $100, and they spend $10, a negative adjustment can only
   * increase the balance of the developer to $100.
   *
   * @param GoogleTypeMoney $adjustment
   */
  public function setAdjustment(GoogleTypeMoney $adjustment)
  {
    $this->adjustment = $adjustment;
  }
  /**
   * @return GoogleTypeMoney
   */
  public function getAdjustment()
  {
    return $this->adjustment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1AdjustDeveloperBalanceRequest::class, 'Google_Service_Apigee_GoogleCloudApigeeV1AdjustDeveloperBalanceRequest');
