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

namespace Google\Service\Fcmdata;

class GoogleFirebaseFcmDataV1beta1ProxyNotificationInsightPercents extends \Google\Model
{
  /**
   * The percentage of accepted notifications that failed to be proxied. This is
   * usually caused by exceptions that occurred while calling [notifyAsPackage](
   * https://developer.android.com/reference/android/app/NotificationManager#not
   * ifyAsPackage%28java.lang.String,%20java.lang.String,%20int,%20android.app.N
   * otification%29).
   *
   * @var float
   */
  public $failed;
  /**
   * The percentage of accepted notifications that were successfully proxied by
   * [Google Play
   * services](https://developers.google.com/android/guides/overview).
   *
   * @var float
   */
  public $proxied;
  /**
   * The percentage of accepted notifications that were skipped because the
   * messages were not throttled.
   *
   * @var float
   */
  public $skippedNotThrottled;
  /**
   * The percentage of accepted notifications that were skipped because the app
   * disallowed these messages to be proxied.
   *
   * @var float
   */
  public $skippedOptedOut;
  /**
   * The percentage of accepted notifications that were skipped because
   * configurations required for notifications to be proxied were missing.
   *
   * @var float
   */
  public $skippedUnconfigured;
  /**
   * The percentage of accepted notifications that were skipped because proxy
   * notification is unsupported for the recipient.
   *
   * @var float
   */
  public $skippedUnsupported;

  /**
   * The percentage of accepted notifications that failed to be proxied. This is
   * usually caused by exceptions that occurred while calling [notifyAsPackage](
   * https://developer.android.com/reference/android/app/NotificationManager#not
   * ifyAsPackage%28java.lang.String,%20java.lang.String,%20int,%20android.app.N
   * otification%29).
   *
   * @param float $failed
   */
  public function setFailed($failed)
  {
    $this->failed = $failed;
  }
  /**
   * @return float
   */
  public function getFailed()
  {
    return $this->failed;
  }
  /**
   * The percentage of accepted notifications that were successfully proxied by
   * [Google Play
   * services](https://developers.google.com/android/guides/overview).
   *
   * @param float $proxied
   */
  public function setProxied($proxied)
  {
    $this->proxied = $proxied;
  }
  /**
   * @return float
   */
  public function getProxied()
  {
    return $this->proxied;
  }
  /**
   * The percentage of accepted notifications that were skipped because the
   * messages were not throttled.
   *
   * @param float $skippedNotThrottled
   */
  public function setSkippedNotThrottled($skippedNotThrottled)
  {
    $this->skippedNotThrottled = $skippedNotThrottled;
  }
  /**
   * @return float
   */
  public function getSkippedNotThrottled()
  {
    return $this->skippedNotThrottled;
  }
  /**
   * The percentage of accepted notifications that were skipped because the app
   * disallowed these messages to be proxied.
   *
   * @param float $skippedOptedOut
   */
  public function setSkippedOptedOut($skippedOptedOut)
  {
    $this->skippedOptedOut = $skippedOptedOut;
  }
  /**
   * @return float
   */
  public function getSkippedOptedOut()
  {
    return $this->skippedOptedOut;
  }
  /**
   * The percentage of accepted notifications that were skipped because
   * configurations required for notifications to be proxied were missing.
   *
   * @param float $skippedUnconfigured
   */
  public function setSkippedUnconfigured($skippedUnconfigured)
  {
    $this->skippedUnconfigured = $skippedUnconfigured;
  }
  /**
   * @return float
   */
  public function getSkippedUnconfigured()
  {
    return $this->skippedUnconfigured;
  }
  /**
   * The percentage of accepted notifications that were skipped because proxy
   * notification is unsupported for the recipient.
   *
   * @param float $skippedUnsupported
   */
  public function setSkippedUnsupported($skippedUnsupported)
  {
    $this->skippedUnsupported = $skippedUnsupported;
  }
  /**
   * @return float
   */
  public function getSkippedUnsupported()
  {
    return $this->skippedUnsupported;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseFcmDataV1beta1ProxyNotificationInsightPercents::class, 'Google_Service_Fcmdata_GoogleFirebaseFcmDataV1beta1ProxyNotificationInsightPercents');
