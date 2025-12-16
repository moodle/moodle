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

class CrossProfilePolicies extends \Google\Model
{
  /**
   * Unspecified. If appFunctions is set to APP_FUNCTIONS_ALLOWED, defaults to
   * CROSS_PROFILE_APP_FUNCTIONS_ALLOWED. If appFunctions is set to
   * APP_FUNCTIONS_DISALLOWED, defaults to
   * CROSS_PROFILE_APP_FUNCTIONS_DISALLOWED.
   */
  public const CROSS_PROFILE_APP_FUNCTIONS_CROSS_PROFILE_APP_FUNCTIONS_UNSPECIFIED = 'CROSS_PROFILE_APP_FUNCTIONS_UNSPECIFIED';
  /**
   * Personal profile apps are not allowed to invoke app functions exposed by
   * apps in the work profile.
   */
  public const CROSS_PROFILE_APP_FUNCTIONS_CROSS_PROFILE_APP_FUNCTIONS_DISALLOWED = 'CROSS_PROFILE_APP_FUNCTIONS_DISALLOWED';
  /**
   * Personal profile apps can invoke app functions exposed by apps in the work
   * profile. If this is set, appFunctions must not be set to
   * APP_FUNCTIONS_DISALLOWED, otherwise the policy will be rejected.
   */
  public const CROSS_PROFILE_APP_FUNCTIONS_CROSS_PROFILE_APP_FUNCTIONS_ALLOWED = 'CROSS_PROFILE_APP_FUNCTIONS_ALLOWED';
  /**
   * Unspecified. Defaults to COPY_FROM_WORK_TO_PERSONAL_DISALLOWED
   */
  public const CROSS_PROFILE_COPY_PASTE_CROSS_PROFILE_COPY_PASTE_UNSPECIFIED = 'CROSS_PROFILE_COPY_PASTE_UNSPECIFIED';
  /**
   * Default. Prevents users from pasting into the personal profile text copied
   * from the work profile. Text copied from the personal profile can be pasted
   * into the work profile, and text copied from the work profile can be pasted
   * into the work profile.
   */
  public const CROSS_PROFILE_COPY_PASTE_COPY_FROM_WORK_TO_PERSONAL_DISALLOWED = 'COPY_FROM_WORK_TO_PERSONAL_DISALLOWED';
  /**
   * Text copied in either profile can be pasted in the other profile.
   */
  public const CROSS_PROFILE_COPY_PASTE_CROSS_PROFILE_COPY_PASTE_ALLOWED = 'CROSS_PROFILE_COPY_PASTE_ALLOWED';
  /**
   * Unspecified. Defaults to DATA_SHARING_FROM_WORK_TO_PERSONAL_DISALLOWED.
   */
  public const CROSS_PROFILE_DATA_SHARING_CROSS_PROFILE_DATA_SHARING_UNSPECIFIED = 'CROSS_PROFILE_DATA_SHARING_UNSPECIFIED';
  /**
   * Prevents data from being shared from both the personal profile to the work
   * profile and the work profile to the personal profile.
   */
  public const CROSS_PROFILE_DATA_SHARING_CROSS_PROFILE_DATA_SHARING_DISALLOWED = 'CROSS_PROFILE_DATA_SHARING_DISALLOWED';
  /**
   * Default. Prevents users from sharing data from the work profile to apps in
   * the personal profile. Personal data can be shared with work apps.
   */
  public const CROSS_PROFILE_DATA_SHARING_DATA_SHARING_FROM_WORK_TO_PERSONAL_DISALLOWED = 'DATA_SHARING_FROM_WORK_TO_PERSONAL_DISALLOWED';
  /**
   * Data from either profile can be shared with the other profile.
   */
  public const CROSS_PROFILE_DATA_SHARING_CROSS_PROFILE_DATA_SHARING_ALLOWED = 'CROSS_PROFILE_DATA_SHARING_ALLOWED';
  /**
   * Unspecified. Defaults to
   * SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_ALLOWED.When this is set,
   * exemptions_to_show_work_contacts_in_personal_profile must not be set.
   */
  public const SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_UNSPECIFIED = 'SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_UNSPECIFIED';
  /**
   * Prevents personal apps from accessing work profile contacts and looking up
   * work contacts.When this is set, personal apps specified in
   * exemptions_to_show_work_contacts_in_personal_profile are allowlisted and
   * can access work profile contacts directly.Supported on Android 7.0 and
   * above. A NonComplianceDetail with API_LEVEL is reported if the Android
   * version is less than 7.0.
   */
  public const SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_DISALLOWED = 'SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_DISALLOWED';
  /**
   * Default. Allows apps in the personal profile to access work profile
   * contacts including contact searches and incoming calls.When this is set,
   * personal apps specified in
   * exemptions_to_show_work_contacts_in_personal_profile are blocklisted and
   * can not access work profile contacts directly.Supported on Android 7.0 and
   * above. A NonComplianceDetail with API_LEVEL is reported if the Android
   * version is less than 7.0.
   */
  public const SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_ALLOWED = 'SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_ALLOWED';
  /**
   * Prevents most personal apps from accessing work profile contacts including
   * contact searches and incoming calls, except for the OEM default Dialer,
   * Messages, and Contacts apps. Neither user-configured Dialer, Messages, and
   * Contacts apps, nor any other system or play installed apps, will be able to
   * query work contacts directly.When this is set, personal apps specified in
   * exemptions_to_show_work_contacts_in_personal_profile are allowlisted and
   * can access work profile contacts.Supported on Android 14 and above. If this
   * is set on a device with Android version less than 14, the behaviour falls
   * back to SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_DISALLOWED and a
   * NonComplianceDetail with API_LEVEL is reported.
   */
  public const SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_DISALLOWED_EXCEPT_SYSTEM = 'SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_DISALLOWED_EXCEPT_SYSTEM';
  /**
   * Unspecified. Defaults to WORK_PROFILE_WIDGETS_DEFAULT_DISALLOWED.
   */
  public const WORK_PROFILE_WIDGETS_DEFAULT_WORK_PROFILE_WIDGETS_DEFAULT_UNSPECIFIED = 'WORK_PROFILE_WIDGETS_DEFAULT_UNSPECIFIED';
  /**
   * Work profile widgets are allowed by default. This means that if the policy
   * does not specify work_profile_widgets as WORK_PROFILE_WIDGETS_DISALLOWED
   * for the application, it will be able to add widgets to the home screen.
   */
  public const WORK_PROFILE_WIDGETS_DEFAULT_WORK_PROFILE_WIDGETS_DEFAULT_ALLOWED = 'WORK_PROFILE_WIDGETS_DEFAULT_ALLOWED';
  /**
   * Work profile widgets are disallowed by default. This means that if the
   * policy does not specify work_profile_widgets as
   * WORK_PROFILE_WIDGETS_ALLOWED for the application, it will be unable to add
   * widgets to the home screen.
   */
  public const WORK_PROFILE_WIDGETS_DEFAULT_WORK_PROFILE_WIDGETS_DEFAULT_DISALLOWED = 'WORK_PROFILE_WIDGETS_DEFAULT_DISALLOWED';
  /**
   * Optional. Controls whether personal profile apps can invoke app functions
   * exposed by apps in the work profile.
   *
   * @var string
   */
  public $crossProfileAppFunctions;
  /**
   * Whether text copied from one profile (personal or work) can be pasted in
   * the other profile.
   *
   * @var string
   */
  public $crossProfileCopyPaste;
  /**
   * Whether data from one profile (personal or work) can be shared with apps in
   * the other profile. Specifically controls simple data sharing via intents.
   * Management of other cross-profile communication channels, such as contact
   * search, copy/paste, or connected work & personal apps, are configured
   * separately.
   *
   * @var string
   */
  public $crossProfileDataSharing;
  protected $exemptionsToShowWorkContactsInPersonalProfileType = PackageNameList::class;
  protected $exemptionsToShowWorkContactsInPersonalProfileDataType = '';
  /**
   * Whether personal apps can access contacts stored in the work profile.See
   * also exemptions_to_show_work_contacts_in_personal_profile.
   *
   * @var string
   */
  public $showWorkContactsInPersonalProfile;
  /**
   * Specifies the default behaviour for work profile widgets. If the policy
   * does not specify work_profile_widgets for a specific application, it will
   * behave according to the value specified here.
   *
   * @var string
   */
  public $workProfileWidgetsDefault;

  /**
   * Optional. Controls whether personal profile apps can invoke app functions
   * exposed by apps in the work profile.
   *
   * Accepted values: CROSS_PROFILE_APP_FUNCTIONS_UNSPECIFIED,
   * CROSS_PROFILE_APP_FUNCTIONS_DISALLOWED, CROSS_PROFILE_APP_FUNCTIONS_ALLOWED
   *
   * @param self::CROSS_PROFILE_APP_FUNCTIONS_* $crossProfileAppFunctions
   */
  public function setCrossProfileAppFunctions($crossProfileAppFunctions)
  {
    $this->crossProfileAppFunctions = $crossProfileAppFunctions;
  }
  /**
   * @return self::CROSS_PROFILE_APP_FUNCTIONS_*
   */
  public function getCrossProfileAppFunctions()
  {
    return $this->crossProfileAppFunctions;
  }
  /**
   * Whether text copied from one profile (personal or work) can be pasted in
   * the other profile.
   *
   * Accepted values: CROSS_PROFILE_COPY_PASTE_UNSPECIFIED,
   * COPY_FROM_WORK_TO_PERSONAL_DISALLOWED, CROSS_PROFILE_COPY_PASTE_ALLOWED
   *
   * @param self::CROSS_PROFILE_COPY_PASTE_* $crossProfileCopyPaste
   */
  public function setCrossProfileCopyPaste($crossProfileCopyPaste)
  {
    $this->crossProfileCopyPaste = $crossProfileCopyPaste;
  }
  /**
   * @return self::CROSS_PROFILE_COPY_PASTE_*
   */
  public function getCrossProfileCopyPaste()
  {
    return $this->crossProfileCopyPaste;
  }
  /**
   * Whether data from one profile (personal or work) can be shared with apps in
   * the other profile. Specifically controls simple data sharing via intents.
   * Management of other cross-profile communication channels, such as contact
   * search, copy/paste, or connected work & personal apps, are configured
   * separately.
   *
   * Accepted values: CROSS_PROFILE_DATA_SHARING_UNSPECIFIED,
   * CROSS_PROFILE_DATA_SHARING_DISALLOWED,
   * DATA_SHARING_FROM_WORK_TO_PERSONAL_DISALLOWED,
   * CROSS_PROFILE_DATA_SHARING_ALLOWED
   *
   * @param self::CROSS_PROFILE_DATA_SHARING_* $crossProfileDataSharing
   */
  public function setCrossProfileDataSharing($crossProfileDataSharing)
  {
    $this->crossProfileDataSharing = $crossProfileDataSharing;
  }
  /**
   * @return self::CROSS_PROFILE_DATA_SHARING_*
   */
  public function getCrossProfileDataSharing()
  {
    return $this->crossProfileDataSharing;
  }
  /**
   * List of apps which are excluded from the ShowWorkContactsInPersonalProfile
   * setting. For this to be set, ShowWorkContactsInPersonalProfile must be set
   * to one of the following values:
   * SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_ALLOWED. In this case, these
   * exemptions act as a blocklist.
   * SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_DISALLOWED. In this case, these
   * exemptions act as an allowlist.
   * SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_DISALLOWED_EXCEPT_SYSTEM. In this
   * case, these exemptions act as an allowlist, in addition to the already
   * allowlisted system apps.Supported on Android 14 and above. A
   * NonComplianceDetail with API_LEVEL is reported if the Android version is
   * less than 14.
   *
   * @param PackageNameList $exemptionsToShowWorkContactsInPersonalProfile
   */
  public function setExemptionsToShowWorkContactsInPersonalProfile(PackageNameList $exemptionsToShowWorkContactsInPersonalProfile)
  {
    $this->exemptionsToShowWorkContactsInPersonalProfile = $exemptionsToShowWorkContactsInPersonalProfile;
  }
  /**
   * @return PackageNameList
   */
  public function getExemptionsToShowWorkContactsInPersonalProfile()
  {
    return $this->exemptionsToShowWorkContactsInPersonalProfile;
  }
  /**
   * Whether personal apps can access contacts stored in the work profile.See
   * also exemptions_to_show_work_contacts_in_personal_profile.
   *
   * Accepted values: SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_UNSPECIFIED,
   * SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_DISALLOWED,
   * SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_ALLOWED,
   * SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_DISALLOWED_EXCEPT_SYSTEM
   *
   * @param self::SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_* $showWorkContactsInPersonalProfile
   */
  public function setShowWorkContactsInPersonalProfile($showWorkContactsInPersonalProfile)
  {
    $this->showWorkContactsInPersonalProfile = $showWorkContactsInPersonalProfile;
  }
  /**
   * @return self::SHOW_WORK_CONTACTS_IN_PERSONAL_PROFILE_*
   */
  public function getShowWorkContactsInPersonalProfile()
  {
    return $this->showWorkContactsInPersonalProfile;
  }
  /**
   * Specifies the default behaviour for work profile widgets. If the policy
   * does not specify work_profile_widgets for a specific application, it will
   * behave according to the value specified here.
   *
   * Accepted values: WORK_PROFILE_WIDGETS_DEFAULT_UNSPECIFIED,
   * WORK_PROFILE_WIDGETS_DEFAULT_ALLOWED,
   * WORK_PROFILE_WIDGETS_DEFAULT_DISALLOWED
   *
   * @param self::WORK_PROFILE_WIDGETS_DEFAULT_* $workProfileWidgetsDefault
   */
  public function setWorkProfileWidgetsDefault($workProfileWidgetsDefault)
  {
    $this->workProfileWidgetsDefault = $workProfileWidgetsDefault;
  }
  /**
   * @return self::WORK_PROFILE_WIDGETS_DEFAULT_*
   */
  public function getWorkProfileWidgetsDefault()
  {
    return $this->workProfileWidgetsDefault;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CrossProfilePolicies::class, 'Google_Service_AndroidManagement_CrossProfilePolicies');
