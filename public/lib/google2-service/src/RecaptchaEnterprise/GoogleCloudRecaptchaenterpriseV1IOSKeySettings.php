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

class GoogleCloudRecaptchaenterpriseV1IOSKeySettings extends \Google\Collection
{
  protected $collection_key = 'allowedBundleIds';
  /**
   * Optional. If set to true, allowed_bundle_ids are not enforced.
   *
   * @var bool
   */
  public $allowAllBundleIds;
  /**
   * Optional. iOS bundle IDs of apps allowed to use the key. Example:
   * 'com.companyname.productname.appname' Each key supports a maximum of 250
   * bundle IDs. To use a key on more apps, set `allow_all_bundle_ids` to true.
   * When this is set, you are responsible for validating the bundle id by
   * checking the `token_properties.ios_bundle_id` field in each assessment
   * response against your list of allowed bundle IDs.
   *
   * @var string[]
   */
  public $allowedBundleIds;
  protected $appleDeveloperIdType = GoogleCloudRecaptchaenterpriseV1AppleDeveloperId::class;
  protected $appleDeveloperIdDataType = '';

  /**
   * Optional. If set to true, allowed_bundle_ids are not enforced.
   *
   * @param bool $allowAllBundleIds
   */
  public function setAllowAllBundleIds($allowAllBundleIds)
  {
    $this->allowAllBundleIds = $allowAllBundleIds;
  }
  /**
   * @return bool
   */
  public function getAllowAllBundleIds()
  {
    return $this->allowAllBundleIds;
  }
  /**
   * Optional. iOS bundle IDs of apps allowed to use the key. Example:
   * 'com.companyname.productname.appname' Each key supports a maximum of 250
   * bundle IDs. To use a key on more apps, set `allow_all_bundle_ids` to true.
   * When this is set, you are responsible for validating the bundle id by
   * checking the `token_properties.ios_bundle_id` field in each assessment
   * response against your list of allowed bundle IDs.
   *
   * @param string[] $allowedBundleIds
   */
  public function setAllowedBundleIds($allowedBundleIds)
  {
    $this->allowedBundleIds = $allowedBundleIds;
  }
  /**
   * @return string[]
   */
  public function getAllowedBundleIds()
  {
    return $this->allowedBundleIds;
  }
  /**
   * Optional. Apple Developer account details for the app that is protected by
   * the reCAPTCHA Key. reCAPTCHA leverages platform-specific checks like Apple
   * App Attest and Apple DeviceCheck to protect your app from abuse. Providing
   * these fields allows reCAPTCHA to get a better assessment of the integrity
   * of your app.
   *
   * @param GoogleCloudRecaptchaenterpriseV1AppleDeveloperId $appleDeveloperId
   */
  public function setAppleDeveloperId(GoogleCloudRecaptchaenterpriseV1AppleDeveloperId $appleDeveloperId)
  {
    $this->appleDeveloperId = $appleDeveloperId;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1AppleDeveloperId
   */
  public function getAppleDeveloperId()
  {
    return $this->appleDeveloperId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1IOSKeySettings::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1IOSKeySettings');
