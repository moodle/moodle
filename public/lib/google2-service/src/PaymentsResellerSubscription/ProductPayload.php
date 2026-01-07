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

class ProductPayload extends \Google\Model
{
  protected $googleHomePayloadType = GoogleHomePayload::class;
  protected $googleHomePayloadDataType = '';
  protected $googleOnePayloadType = GoogleOnePayload::class;
  protected $googleOnePayloadDataType = '';
  protected $youtubePayloadType = YoutubePayload::class;
  protected $youtubePayloadDataType = '';

  /**
   * Payload specific to Google Home products.
   *
   * @param GoogleHomePayload $googleHomePayload
   */
  public function setGoogleHomePayload(GoogleHomePayload $googleHomePayload)
  {
    $this->googleHomePayload = $googleHomePayload;
  }
  /**
   * @return GoogleHomePayload
   */
  public function getGoogleHomePayload()
  {
    return $this->googleHomePayload;
  }
  /**
   * Product-specific payloads. Payload specific to Google One products.
   *
   * @param GoogleOnePayload $googleOnePayload
   */
  public function setGoogleOnePayload(GoogleOnePayload $googleOnePayload)
  {
    $this->googleOnePayload = $googleOnePayload;
  }
  /**
   * @return GoogleOnePayload
   */
  public function getGoogleOnePayload()
  {
    return $this->googleOnePayload;
  }
  /**
   * Payload specific to Youtube products.
   *
   * @param YoutubePayload $youtubePayload
   */
  public function setYoutubePayload(YoutubePayload $youtubePayload)
  {
    $this->youtubePayload = $youtubePayload;
  }
  /**
   * @return YoutubePayload
   */
  public function getYoutubePayload()
  {
    return $this->youtubePayload;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductPayload::class, 'Google_Service_PaymentsResellerSubscription_ProductPayload');
