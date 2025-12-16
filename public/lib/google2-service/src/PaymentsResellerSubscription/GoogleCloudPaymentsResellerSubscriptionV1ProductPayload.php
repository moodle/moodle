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

class GoogleCloudPaymentsResellerSubscriptionV1ProductPayload extends \Google\Model
{
  protected $googleHomePayloadType = GoogleCloudPaymentsResellerSubscriptionV1GoogleHomePayload::class;
  protected $googleHomePayloadDataType = '';
  protected $googleOnePayloadType = GoogleCloudPaymentsResellerSubscriptionV1GoogleOnePayload::class;
  protected $googleOnePayloadDataType = '';
  protected $youtubePayloadType = GoogleCloudPaymentsResellerSubscriptionV1YoutubePayload::class;
  protected $youtubePayloadDataType = '';

  /**
   * @param GoogleCloudPaymentsResellerSubscriptionV1GoogleHomePayload
   */
  public function setGoogleHomePayload(GoogleCloudPaymentsResellerSubscriptionV1GoogleHomePayload $googleHomePayload)
  {
    $this->googleHomePayload = $googleHomePayload;
  }
  /**
   * @return GoogleCloudPaymentsResellerSubscriptionV1GoogleHomePayload
   */
  public function getGoogleHomePayload()
  {
    return $this->googleHomePayload;
  }
  /**
   * @param GoogleCloudPaymentsResellerSubscriptionV1GoogleOnePayload
   */
  public function setGoogleOnePayload(GoogleCloudPaymentsResellerSubscriptionV1GoogleOnePayload $googleOnePayload)
  {
    $this->googleOnePayload = $googleOnePayload;
  }
  /**
   * @return GoogleCloudPaymentsResellerSubscriptionV1GoogleOnePayload
   */
  public function getGoogleOnePayload()
  {
    return $this->googleOnePayload;
  }
  /**
   * @param GoogleCloudPaymentsResellerSubscriptionV1YoutubePayload
   */
  public function setYoutubePayload(GoogleCloudPaymentsResellerSubscriptionV1YoutubePayload $youtubePayload)
  {
    $this->youtubePayload = $youtubePayload;
  }
  /**
   * @return GoogleCloudPaymentsResellerSubscriptionV1YoutubePayload
   */
  public function getYoutubePayload()
  {
    return $this->youtubePayload;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPaymentsResellerSubscriptionV1ProductPayload::class, 'Google_Service_PaymentsResellerSubscription_GoogleCloudPaymentsResellerSubscriptionV1ProductPayload');
