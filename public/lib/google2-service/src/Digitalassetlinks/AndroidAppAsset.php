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

namespace Google\Service\Digitalassetlinks;

class AndroidAppAsset extends \Google\Model
{
  protected $certificateType = CertificateInfo::class;
  protected $certificateDataType = '';
  /**
   * Android App assets are naturally identified by their Java package name. For
   * example, the Google Maps app uses the package name
   * `com.google.android.apps.maps`. REQUIRED
   *
   * @var string
   */
  public $packageName;

  /**
   * Because there is no global enforcement of package name uniqueness, we also
   * require a signing certificate, which in combination with the package name
   * uniquely identifies an app. Some apps' signing keys are rotated, so they
   * may be signed by different keys over time. We treat these as distinct
   * assets, since we use (package name, cert) as the unique ID. This should not
   * normally pose any problems as both versions of the app will make the same
   * or similar statements. Other assets making statements about the app will
   * have to be updated when a key is rotated, however. (Note that the syntaxes
   * for publishing and querying for statements contain syntactic sugar to
   * easily let you specify apps that are known by multiple certificates.)
   * REQUIRED
   *
   * @param CertificateInfo $certificate
   */
  public function setCertificate(CertificateInfo $certificate)
  {
    $this->certificate = $certificate;
  }
  /**
   * @return CertificateInfo
   */
  public function getCertificate()
  {
    return $this->certificate;
  }
  /**
   * Android App assets are naturally identified by their Java package name. For
   * example, the Google Maps app uses the package name
   * `com.google.android.apps.maps`. REQUIRED
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AndroidAppAsset::class, 'Google_Service_Digitalassetlinks_AndroidAppAsset');
