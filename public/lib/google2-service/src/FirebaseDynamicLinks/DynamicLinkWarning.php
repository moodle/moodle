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

namespace Google\Service\FirebaseDynamicLinks;

class DynamicLinkWarning extends \Google\Model
{
  /**
   * Unknown code.
   */
  public const WARNING_CODE_CODE_UNSPECIFIED = 'CODE_UNSPECIFIED';
  /**
   * The Android package does not match any in developer's DevConsole project.
   */
  public const WARNING_CODE_NOT_IN_PROJECT_ANDROID_PACKAGE_NAME = 'NOT_IN_PROJECT_ANDROID_PACKAGE_NAME';
  /**
   * The Android minimum version code has to be a valid integer.
   */
  public const WARNING_CODE_NOT_INTEGER_ANDROID_PACKAGE_MIN_VERSION = 'NOT_INTEGER_ANDROID_PACKAGE_MIN_VERSION';
  /**
   * Android package min version param is not needed, e.g. when 'apn' is
   * missing.
   */
  public const WARNING_CODE_UNNECESSARY_ANDROID_PACKAGE_MIN_VERSION = 'UNNECESSARY_ANDROID_PACKAGE_MIN_VERSION';
  /**
   * Android link is not a valid URI.
   */
  public const WARNING_CODE_NOT_URI_ANDROID_LINK = 'NOT_URI_ANDROID_LINK';
  /**
   * Android link param is not needed, e.g. when param 'al' and 'link' have the
   * same value..
   */
  public const WARNING_CODE_UNNECESSARY_ANDROID_LINK = 'UNNECESSARY_ANDROID_LINK';
  /**
   * Android fallback link is not a valid URI.
   */
  public const WARNING_CODE_NOT_URI_ANDROID_FALLBACK_LINK = 'NOT_URI_ANDROID_FALLBACK_LINK';
  /**
   * Android fallback link has an invalid (non http/https) URI scheme.
   */
  public const WARNING_CODE_BAD_URI_SCHEME_ANDROID_FALLBACK_LINK = 'BAD_URI_SCHEME_ANDROID_FALLBACK_LINK';
  /**
   * The iOS bundle ID does not match any in developer's DevConsole project.
   */
  public const WARNING_CODE_NOT_IN_PROJECT_IOS_BUNDLE_ID = 'NOT_IN_PROJECT_IOS_BUNDLE_ID';
  /**
   * The iPad bundle ID does not match any in developer's DevConsole project.
   */
  public const WARNING_CODE_NOT_IN_PROJECT_IPAD_BUNDLE_ID = 'NOT_IN_PROJECT_IPAD_BUNDLE_ID';
  /**
   * iOS URL scheme is not needed, e.g. when 'ibi' are 'ipbi' are all missing.
   */
  public const WARNING_CODE_UNNECESSARY_IOS_URL_SCHEME = 'UNNECESSARY_IOS_URL_SCHEME';
  /**
   * iOS app store ID format is incorrect, e.g. not numeric.
   */
  public const WARNING_CODE_NOT_NUMERIC_IOS_APP_STORE_ID = 'NOT_NUMERIC_IOS_APP_STORE_ID';
  /**
   * iOS app store ID is not needed.
   */
  public const WARNING_CODE_UNNECESSARY_IOS_APP_STORE_ID = 'UNNECESSARY_IOS_APP_STORE_ID';
  /**
   * iOS fallback link is not a valid URI.
   */
  public const WARNING_CODE_NOT_URI_IOS_FALLBACK_LINK = 'NOT_URI_IOS_FALLBACK_LINK';
  /**
   * iOS fallback link has an invalid (non http/https) URI scheme.
   */
  public const WARNING_CODE_BAD_URI_SCHEME_IOS_FALLBACK_LINK = 'BAD_URI_SCHEME_IOS_FALLBACK_LINK';
  /**
   * iPad fallback link is not a valid URI.
   */
  public const WARNING_CODE_NOT_URI_IPAD_FALLBACK_LINK = 'NOT_URI_IPAD_FALLBACK_LINK';
  /**
   * iPad fallback link has an invalid (non http/https) URI scheme.
   */
  public const WARNING_CODE_BAD_URI_SCHEME_IPAD_FALLBACK_LINK = 'BAD_URI_SCHEME_IPAD_FALLBACK_LINK';
  /**
   * Debug param format is incorrect.
   */
  public const WARNING_CODE_BAD_DEBUG_PARAM = 'BAD_DEBUG_PARAM';
  /**
   * isAd param format is incorrect.
   */
  public const WARNING_CODE_BAD_AD_PARAM = 'BAD_AD_PARAM';
  /**
   * Indicates a certain param is deprecated.
   */
  public const WARNING_CODE_DEPRECATED_PARAM = 'DEPRECATED_PARAM';
  /**
   * Indicates certain parameter is not recognized.
   */
  public const WARNING_CODE_UNRECOGNIZED_PARAM = 'UNRECOGNIZED_PARAM';
  /**
   * Indicates certain parameter is too long.
   */
  public const WARNING_CODE_TOO_LONG_PARAM = 'TOO_LONG_PARAM';
  /**
   * Social meta tag image link is not a valid URI.
   */
  public const WARNING_CODE_NOT_URI_SOCIAL_IMAGE_LINK = 'NOT_URI_SOCIAL_IMAGE_LINK';
  /**
   * Social meta tag image link has an invalid (non http/https) URI scheme.
   */
  public const WARNING_CODE_BAD_URI_SCHEME_SOCIAL_IMAGE_LINK = 'BAD_URI_SCHEME_SOCIAL_IMAGE_LINK';
  public const WARNING_CODE_NOT_URI_SOCIAL_URL = 'NOT_URI_SOCIAL_URL';
  public const WARNING_CODE_BAD_URI_SCHEME_SOCIAL_URL = 'BAD_URI_SCHEME_SOCIAL_URL';
  /**
   * Dynamic Link URL length is too long.
   */
  public const WARNING_CODE_LINK_LENGTH_TOO_LONG = 'LINK_LENGTH_TOO_LONG';
  /**
   * Dynamic Link URL contains fragments.
   */
  public const WARNING_CODE_LINK_WITH_FRAGMENTS = 'LINK_WITH_FRAGMENTS';
  /**
   * The iOS bundle ID does not match with the given iOS store ID.
   */
  public const WARNING_CODE_NOT_MATCHING_IOS_BUNDLE_ID_AND_STORE_ID = 'NOT_MATCHING_IOS_BUNDLE_ID_AND_STORE_ID';
  /**
   * The API is deprecated.
   */
  public const WARNING_CODE_API_DEPRECATED = 'API_DEPRECATED';
  /**
   * The warning code.
   *
   * @var string
   */
  public $warningCode;
  /**
   * The document describing the warning, and helps resolve.
   *
   * @var string
   */
  public $warningDocumentLink;
  /**
   * The warning message to help developers improve their requests.
   *
   * @var string
   */
  public $warningMessage;

  /**
   * The warning code.
   *
   * Accepted values: CODE_UNSPECIFIED, NOT_IN_PROJECT_ANDROID_PACKAGE_NAME,
   * NOT_INTEGER_ANDROID_PACKAGE_MIN_VERSION,
   * UNNECESSARY_ANDROID_PACKAGE_MIN_VERSION, NOT_URI_ANDROID_LINK,
   * UNNECESSARY_ANDROID_LINK, NOT_URI_ANDROID_FALLBACK_LINK,
   * BAD_URI_SCHEME_ANDROID_FALLBACK_LINK, NOT_IN_PROJECT_IOS_BUNDLE_ID,
   * NOT_IN_PROJECT_IPAD_BUNDLE_ID, UNNECESSARY_IOS_URL_SCHEME,
   * NOT_NUMERIC_IOS_APP_STORE_ID, UNNECESSARY_IOS_APP_STORE_ID,
   * NOT_URI_IOS_FALLBACK_LINK, BAD_URI_SCHEME_IOS_FALLBACK_LINK,
   * NOT_URI_IPAD_FALLBACK_LINK, BAD_URI_SCHEME_IPAD_FALLBACK_LINK,
   * BAD_DEBUG_PARAM, BAD_AD_PARAM, DEPRECATED_PARAM, UNRECOGNIZED_PARAM,
   * TOO_LONG_PARAM, NOT_URI_SOCIAL_IMAGE_LINK,
   * BAD_URI_SCHEME_SOCIAL_IMAGE_LINK, NOT_URI_SOCIAL_URL,
   * BAD_URI_SCHEME_SOCIAL_URL, LINK_LENGTH_TOO_LONG, LINK_WITH_FRAGMENTS,
   * NOT_MATCHING_IOS_BUNDLE_ID_AND_STORE_ID, API_DEPRECATED
   *
   * @param self::WARNING_CODE_* $warningCode
   */
  public function setWarningCode($warningCode)
  {
    $this->warningCode = $warningCode;
  }
  /**
   * @return self::WARNING_CODE_*
   */
  public function getWarningCode()
  {
    return $this->warningCode;
  }
  /**
   * The document describing the warning, and helps resolve.
   *
   * @param string $warningDocumentLink
   */
  public function setWarningDocumentLink($warningDocumentLink)
  {
    $this->warningDocumentLink = $warningDocumentLink;
  }
  /**
   * @return string
   */
  public function getWarningDocumentLink()
  {
    return $this->warningDocumentLink;
  }
  /**
   * The warning message to help developers improve their requests.
   *
   * @param string $warningMessage
   */
  public function setWarningMessage($warningMessage)
  {
    $this->warningMessage = $warningMessage;
  }
  /**
   * @return string
   */
  public function getWarningMessage()
  {
    return $this->warningMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DynamicLinkWarning::class, 'Google_Service_FirebaseDynamicLinks_DynamicLinkWarning');
