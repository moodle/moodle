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

namespace Google\Service\PaymentsResellerSubscription;

class IntentPayloadIntentOptions extends \Google\Model
{
  /**
   * Optional. If true, Google may use a different product and promotion id from
   * the ones in the `create_intent` based on the user's eligibility. Only
   * applicable for certain YouTube free trial offers.
   *
   * @var bool
   */
  public $enableOfferOverride;

  /**
   * Optional. If true, Google may use a different product and promotion id from
   * the ones in the `create_intent` based on the user's eligibility. Only
   * applicable for certain YouTube free trial offers.
   *
   * @param bool $enableOfferOverride
   */
  public function setEnableOfferOverride($enableOfferOverride)
  {
    $this->enableOfferOverride = $enableOfferOverride;
  }
  /**
   * @return bool
   */
  public function getEnableOfferOverride()
  {
    return $this->enableOfferOverride;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IntentPayloadIntentOptions::class, 'Google_Service_PaymentsResellerSubscription_IntentPayloadIntentOptions');
