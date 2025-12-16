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

namespace Google\Service\ChecksService;

class GoogleChecksReportV1alphaAnalyzeUploadRequest extends \Google\Model
{
  /**
   * Not specified.
   */
  public const APP_BINARY_FILE_TYPE_APP_BINARY_FILE_TYPE_UNSPECIFIED = 'APP_BINARY_FILE_TYPE_UNSPECIFIED';
  /**
   * .apk file type.
   */
  public const APP_BINARY_FILE_TYPE_ANDROID_APK = 'ANDROID_APK';
  /**
   * .aab (app bundle) file type.
   */
  public const APP_BINARY_FILE_TYPE_ANDROID_AAB = 'ANDROID_AAB';
  /**
   * .ipa file type.
   */
  public const APP_BINARY_FILE_TYPE_IOS_IPA = 'IOS_IPA';
  /**
   * Optional. The type of the uploaded app binary. If not provided, the server
   * assumes APK file for Android and IPA file for iOS.
   *
   * @var string
   */
  public $appBinaryFileType;
  /**
   * Optional. Git commit hash or changelist number associated with the upload.
   *
   * @var string
   */
  public $codeReferenceId;

  /**
   * Optional. The type of the uploaded app binary. If not provided, the server
   * assumes APK file for Android and IPA file for iOS.
   *
   * Accepted values: APP_BINARY_FILE_TYPE_UNSPECIFIED, ANDROID_APK,
   * ANDROID_AAB, IOS_IPA
   *
   * @param self::APP_BINARY_FILE_TYPE_* $appBinaryFileType
   */
  public function setAppBinaryFileType($appBinaryFileType)
  {
    $this->appBinaryFileType = $appBinaryFileType;
  }
  /**
   * @return self::APP_BINARY_FILE_TYPE_*
   */
  public function getAppBinaryFileType()
  {
    return $this->appBinaryFileType;
  }
  /**
   * Optional. Git commit hash or changelist number associated with the upload.
   *
   * @param string $codeReferenceId
   */
  public function setCodeReferenceId($codeReferenceId)
  {
    $this->codeReferenceId = $codeReferenceId;
  }
  /**
   * @return string
   */
  public function getCodeReferenceId()
  {
    return $this->codeReferenceId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksReportV1alphaAnalyzeUploadRequest::class, 'Google_Service_ChecksService_GoogleChecksReportV1alphaAnalyzeUploadRequest');
