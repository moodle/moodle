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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1AndroidKeySettings extends \Google\Collection
{
  protected $collection_key = 'allowedPackageNames';
  /**
   * Optional. If set to true, allowed_package_names are not enforced.
   *
   * @var bool
   */
  public $allowAllPackageNames;
  /**
   * Optional. Android package names of apps allowed to use the key. Example:
   * 'com.companyname.appname' Each key supports a maximum of 250 package names.
   * To use a key on more apps, set `allow_all_package_names` to true. When this
   * is set, you are responsible for validating the package name by checking the
   * `token_properties.android_package_name` field in each assessment response
   * against your list of allowed package names.
   *
   * @var string[]
   */
  public $allowedPackageNames;
  /**
   * Optional. Set to true for keys that are used in an Android application that
   * is available for download in app stores in addition to the Google Play
   * Store.
   *
   * @var bool
   */
  public $supportNonGoogleAppStoreDistribution;

  /**
   * Optional. If set to true, allowed_package_names are not enforced.
   *
   * @param bool $allowAllPackageNames
   */
  public function setAllowAllPackageNames($allowAllPackageNames)
  {
    $this->allowAllPackageNames = $allowAllPackageNames;
  }
  /**
   * @return bool
   */
  public function getAllowAllPackageNames()
  {
    return $this->allowAllPackageNames;
  }
  /**
   * Optional. Android package names of apps allowed to use the key. Example:
   * 'com.companyname.appname' Each key supports a maximum of 250 package names.
   * To use a key on more apps, set `allow_all_package_names` to true. When this
   * is set, you are responsible for validating the package name by checking the
   * `token_properties.android_package_name` field in each assessment response
   * against your list of allowed package names.
   *
   * @param string[] $allowedPackageNames
   */
  public function setAllowedPackageNames($allowedPackageNames)
  {
    $this->allowedPackageNames = $allowedPackageNames;
  }
  /**
   * @return string[]
   */
  public function getAllowedPackageNames()
  {
    return $this->allowedPackageNames;
  }
  /**
   * Optional. Set to true for keys that are used in an Android application that
   * is available for download in app stores in addition to the Google Play
   * Store.
   *
   * @param bool $supportNonGoogleAppStoreDistribution
   */
  public function setSupportNonGoogleAppStoreDistribution($supportNonGoogleAppStoreDistribution)
  {
    $this->supportNonGoogleAppStoreDistribution = $supportNonGoogleAppStoreDistribution;
  }
  /**
   * @return bool
   */
  public function getSupportNonGoogleAppStoreDistribution()
  {
    return $this->supportNonGoogleAppStoreDistribution;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1AndroidKeySettings::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1AndroidKeySettings');
