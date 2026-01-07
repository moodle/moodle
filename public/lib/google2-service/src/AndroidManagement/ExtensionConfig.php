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

namespace Google\Service\AndroidManagement;

class ExtensionConfig extends \Google\Collection
{
  protected $collection_key = 'signingKeyFingerprintsSha256';
  /**
   * Fully qualified class name of the receiver service class for Android Device
   * Policy to notify the extension app of any local command status updates. The
   * service must be exported in the extension app's AndroidManifest.xml and
   * extend NotificationReceiverService (https://developers.google.com/android/m
   * anagement/reference/amapi/com/google/android/managementapi/notification/Not
   * ificationReceiverService) (see Integrate with the AMAPI SDK
   * (https://developers.google.com/android/management/sdk-integration) guide
   * for more details).
   *
   * @deprecated
   * @var string
   */
  public $notificationReceiver;
  /**
   * Hex-encoded SHA-256 hashes of the signing key certificates of the extension
   * app. Only hexadecimal string representations of 64 characters are valid.The
   * signing key certificate fingerprints are always obtained from the Play
   * Store and this field is used to provide additional signing key certificate
   * fingerprints. However, if the application is not available on the Play
   * Store, this field needs to be set. A NonComplianceDetail with INVALID_VALUE
   * is reported if this field is not set when the application is not available
   * on the Play Store.The signing key certificate fingerprint of the extension
   * app on the device must match one of the signing key certificate
   * fingerprints obtained from the Play Store or the ones provided in this
   * field for the app to be able to communicate with Android Device Policy.In
   * production use cases, it is recommended to leave this empty.
   *
   * @deprecated
   * @var string[]
   */
  public $signingKeyFingerprintsSha256;

  /**
   * Fully qualified class name of the receiver service class for Android Device
   * Policy to notify the extension app of any local command status updates. The
   * service must be exported in the extension app's AndroidManifest.xml and
   * extend NotificationReceiverService (https://developers.google.com/android/m
   * anagement/reference/amapi/com/google/android/managementapi/notification/Not
   * ificationReceiverService) (see Integrate with the AMAPI SDK
   * (https://developers.google.com/android/management/sdk-integration) guide
   * for more details).
   *
   * @deprecated
   * @param string $notificationReceiver
   */
  public function setNotificationReceiver($notificationReceiver)
  {
    $this->notificationReceiver = $notificationReceiver;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getNotificationReceiver()
  {
    return $this->notificationReceiver;
  }
  /**
   * Hex-encoded SHA-256 hashes of the signing key certificates of the extension
   * app. Only hexadecimal string representations of 64 characters are valid.The
   * signing key certificate fingerprints are always obtained from the Play
   * Store and this field is used to provide additional signing key certificate
   * fingerprints. However, if the application is not available on the Play
   * Store, this field needs to be set. A NonComplianceDetail with INVALID_VALUE
   * is reported if this field is not set when the application is not available
   * on the Play Store.The signing key certificate fingerprint of the extension
   * app on the device must match one of the signing key certificate
   * fingerprints obtained from the Play Store or the ones provided in this
   * field for the app to be able to communicate with Android Device Policy.In
   * production use cases, it is recommended to leave this empty.
   *
   * @deprecated
   * @param string[] $signingKeyFingerprintsSha256
   */
  public function setSigningKeyFingerprintsSha256($signingKeyFingerprintsSha256)
  {
    $this->signingKeyFingerprintsSha256 = $signingKeyFingerprintsSha256;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getSigningKeyFingerprintsSha256()
  {
    return $this->signingKeyFingerprintsSha256;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExtensionConfig::class, 'Google_Service_AndroidManagement_ExtensionConfig');
