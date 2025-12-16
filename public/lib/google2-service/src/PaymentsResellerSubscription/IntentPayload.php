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

class IntentPayload extends \Google\Model
{
  protected $createIntentType = CreateSubscriptionIntent::class;
  protected $createIntentDataType = '';
  protected $entitleIntentType = EntitleSubscriptionIntent::class;
  protected $entitleIntentDataType = '';
  protected $intentOptionsType = IntentPayloadIntentOptions::class;
  protected $intentOptionsDataType = '';

  /**
   * The request to create a subscription.
   *
   * @param CreateSubscriptionIntent $createIntent
   */
  public function setCreateIntent(CreateSubscriptionIntent $createIntent)
  {
    $this->createIntent = $createIntent;
  }
  /**
   * @return CreateSubscriptionIntent
   */
  public function getCreateIntent()
  {
    return $this->createIntent;
  }
  /**
   * The request to entitle a subscription.
   *
   * @param EntitleSubscriptionIntent $entitleIntent
   */
  public function setEntitleIntent(EntitleSubscriptionIntent $entitleIntent)
  {
    $this->entitleIntent = $entitleIntent;
  }
  /**
   * @return EntitleSubscriptionIntent
   */
  public function getEntitleIntent()
  {
    return $this->entitleIntent;
  }
  /**
   * Optional. The additional features for the intent.
   *
   * @param IntentPayloadIntentOptions $intentOptions
   */
  public function setIntentOptions(IntentPayloadIntentOptions $intentOptions)
  {
    $this->intentOptions = $intentOptions;
  }
  /**
   * @return IntentPayloadIntentOptions
   */
  public function getIntentOptions()
  {
    return $this->intentOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IntentPayload::class, 'Google_Service_PaymentsResellerSubscription_IntentPayload');
