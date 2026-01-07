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

namespace Google\Service\FirebaseManagement;

class ShaCertificate extends \Google\Model
{
  /**
   * Unknown state. This is only used for distinguishing unset values.
   */
  public const CERT_TYPE_SHA_CERTIFICATE_TYPE_UNSPECIFIED = 'SHA_CERTIFICATE_TYPE_UNSPECIFIED';
  /**
   * Certificate is a SHA-1 type certificate.
   */
  public const CERT_TYPE_SHA_1 = 'SHA_1';
  /**
   * Certificate is a SHA-256 type certificate.
   */
  public const CERT_TYPE_SHA_256 = 'SHA_256';
  /**
   * The type of SHA certificate encoded in the hash.
   *
   * @var string
   */
  public $certType;
  /**
   * The resource name of the ShaCertificate for the AndroidApp, in the format:
   * projects/PROJECT_IDENTIFIER/androidApps/APP_ID/sha/SHA_HASH *
   * PROJECT_IDENTIFIER: the parent Project's
   * [`ProjectNumber`](../projects#FirebaseProject.FIELDS.project_number)
   * ***(recommended)*** or its
   * [`ProjectId`](../projects#FirebaseProject.FIELDS.project_id). Learn more
   * about using project identifiers in Google's [AIP 2510
   * standard](https://google.aip.dev/cloud/2510). Note that the value for
   * PROJECT_IDENTIFIER in any response body will be the `ProjectId`. * APP_ID:
   * the globally unique, Firebase-assigned identifier for the App (see
   * [`appId`](../projects.androidApps#AndroidApp.FIELDS.app_id)). * SHA_HASH:
   * the certificate hash for the App (see
   * [`shaHash`](../projects.androidApps.sha#ShaCertificate.FIELDS.sha_hash)).
   *
   * @var string
   */
  public $name;
  /**
   * The certificate hash for the `AndroidApp`.
   *
   * @var string
   */
  public $shaHash;

  /**
   * The type of SHA certificate encoded in the hash.
   *
   * Accepted values: SHA_CERTIFICATE_TYPE_UNSPECIFIED, SHA_1, SHA_256
   *
   * @param self::CERT_TYPE_* $certType
   */
  public function setCertType($certType)
  {
    $this->certType = $certType;
  }
  /**
   * @return self::CERT_TYPE_*
   */
  public function getCertType()
  {
    return $this->certType;
  }
  /**
   * The resource name of the ShaCertificate for the AndroidApp, in the format:
   * projects/PROJECT_IDENTIFIER/androidApps/APP_ID/sha/SHA_HASH *
   * PROJECT_IDENTIFIER: the parent Project's
   * [`ProjectNumber`](../projects#FirebaseProject.FIELDS.project_number)
   * ***(recommended)*** or its
   * [`ProjectId`](../projects#FirebaseProject.FIELDS.project_id). Learn more
   * about using project identifiers in Google's [AIP 2510
   * standard](https://google.aip.dev/cloud/2510). Note that the value for
   * PROJECT_IDENTIFIER in any response body will be the `ProjectId`. * APP_ID:
   * the globally unique, Firebase-assigned identifier for the App (see
   * [`appId`](../projects.androidApps#AndroidApp.FIELDS.app_id)). * SHA_HASH:
   * the certificate hash for the App (see
   * [`shaHash`](../projects.androidApps.sha#ShaCertificate.FIELDS.sha_hash)).
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The certificate hash for the `AndroidApp`.
   *
   * @param string $shaHash
   */
  public function setShaHash($shaHash)
  {
    $this->shaHash = $shaHash;
  }
  /**
   * @return string
   */
  public function getShaHash()
  {
    return $this->shaHash;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ShaCertificate::class, 'Google_Service_FirebaseManagement_ShaCertificate');
