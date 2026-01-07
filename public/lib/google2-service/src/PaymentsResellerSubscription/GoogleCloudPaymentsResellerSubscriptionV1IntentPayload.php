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

class GoogleCloudPaymentsResellerSubscriptionV1IntentPayload extends \Google\Model
{
  protected $createIntentType = GoogleCloudPaymentsResellerSubscriptionV1CreateSubscriptionIntent::class;
  protected $createIntentDataType = '';
  protected $entitleIntentType = GoogleCloudPaymentsResellerSubscriptionV1EntitleSubscriptionIntent::class;
  protected $entitleIntentDataType = '';

  /**
   * @param GoogleCloudPaymentsResellerSubscriptionV1CreateSubscriptionIntent
   */
  public function setCreateIntent(GoogleCloudPaymentsResellerSubscriptionV1CreateSubscriptionIntent $createIntent)
  {
    $this->createIntent = $createIntent;
  }
  /**
   * @return GoogleCloudPaymentsResellerSubscriptionV1CreateSubscriptionIntent
   */
  public function getCreateIntent()
  {
    return $this->createIntent;
  }
  /**
   * @param GoogleCloudPaymentsResellerSubscriptionV1EntitleSubscriptionIntent
   */
  public function setEntitleIntent(GoogleCloudPaymentsResellerSubscriptionV1EntitleSubscriptionIntent $entitleIntent)
  {
    $this->entitleIntent = $entitleIntent;
  }
  /**
   * @return GoogleCloudPaymentsResellerSubscriptionV1EntitleSubscriptionIntent
   */
  public function getEntitleIntent()
  {
    return $this->entitleIntent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPaymentsResellerSubscriptionV1IntentPayload::class, 'Google_Service_PaymentsResellerSubscription_GoogleCloudPaymentsResellerSubscriptionV1IntentPayload');
