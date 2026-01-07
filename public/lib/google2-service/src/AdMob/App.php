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

namespace Google\Service\AdMob;

class App extends \Google\Model
{
  /**
   * Default value for an unset field. Do not use.
   */
  public const APP_APPROVAL_STATE_APP_APPROVAL_STATE_UNSPECIFIED = 'APP_APPROVAL_STATE_UNSPECIFIED';
  /**
   * The app requires additional user action to be approved. Please refer to
   * https://support.google.com/admob/answer/10564477 for details and next
   * steps.
   */
  public const APP_APPROVAL_STATE_ACTION_REQUIRED = 'ACTION_REQUIRED';
  /**
   * The app is pending review.
   */
  public const APP_APPROVAL_STATE_IN_REVIEW = 'IN_REVIEW';
  /**
   * The app is approved and can serve ads.
   */
  public const APP_APPROVAL_STATE_APPROVED = 'APPROVED';
  /**
   * Output only. The approval state for the app. The field is read-only.
   *
   * @var string
   */
  public $appApprovalState;
  /**
   * The externally visible ID of the app which can be used to integrate with
   * the AdMob SDK. This is a read only property. Example: ca-app-
   * pub-9876543210987654~0123456789
   *
   * @var string
   */
  public $appId;
  protected $linkedAppInfoType = AppLinkedAppInfo::class;
  protected $linkedAppInfoDataType = '';
  protected $manualAppInfoType = AppManualAppInfo::class;
  protected $manualAppInfoDataType = '';
  /**
   * Resource name for this app. Format is
   * accounts/{publisher_id}/apps/{app_id_fragment} Example:
   * accounts/pub-9876543210987654/apps/0123456789
   *
   * @var string
   */
  public $name;
  /**
   * Describes the platform of the app. Limited to "IOS" and "ANDROID".
   *
   * @var string
   */
  public $platform;

  /**
   * Output only. The approval state for the app. The field is read-only.
   *
   * Accepted values: APP_APPROVAL_STATE_UNSPECIFIED, ACTION_REQUIRED,
   * IN_REVIEW, APPROVED
   *
   * @param self::APP_APPROVAL_STATE_* $appApprovalState
   */
  public function setAppApprovalState($appApprovalState)
  {
    $this->appApprovalState = $appApprovalState;
  }
  /**
   * @return self::APP_APPROVAL_STATE_*
   */
  public function getAppApprovalState()
  {
    return $this->appApprovalState;
  }
  /**
   * The externally visible ID of the app which can be used to integrate with
   * the AdMob SDK. This is a read only property. Example: ca-app-
   * pub-9876543210987654~0123456789
   *
   * @param string $appId
   */
  public function setAppId($appId)
  {
    $this->appId = $appId;
  }
  /**
   * @return string
   */
  public function getAppId()
  {
    return $this->appId;
  }
  /**
   * Immutable. The information for an app that is linked to an app store. This
   * field is present if and only if the app is linked to an app store.
   *
   * @param AppLinkedAppInfo $linkedAppInfo
   */
  public function setLinkedAppInfo(AppLinkedAppInfo $linkedAppInfo)
  {
    $this->linkedAppInfo = $linkedAppInfo;
  }
  /**
   * @return AppLinkedAppInfo
   */
  public function getLinkedAppInfo()
  {
    return $this->linkedAppInfo;
  }
  /**
   * The information for an app that is not linked to any app store. After an
   * app is linked, this information is still retrivable. If no name is provided
   * for the app upon creation, a placeholder name will be used.
   *
   * @param AppManualAppInfo $manualAppInfo
   */
  public function setManualAppInfo(AppManualAppInfo $manualAppInfo)
  {
    $this->manualAppInfo = $manualAppInfo;
  }
  /**
   * @return AppManualAppInfo
   */
  public function getManualAppInfo()
  {
    return $this->manualAppInfo;
  }
  /**
   * Resource name for this app. Format is
   * accounts/{publisher_id}/apps/{app_id_fragment} Example:
   * accounts/pub-9876543210987654/apps/0123456789
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
   * Describes the platform of the app. Limited to "IOS" and "ANDROID".
   *
   * @param string $platform
   */
  public function setPlatform($platform)
  {
    $this->platform = $platform;
  }
  /**
   * @return string
   */
  public function getPlatform()
  {
    return $this->platform;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(App::class, 'Google_Service_AdMob_App');
