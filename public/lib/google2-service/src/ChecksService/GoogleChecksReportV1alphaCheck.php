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

class GoogleChecksReportV1alphaCheck extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const SEVERITY_CHECK_SEVERITY_UNSPECIFIED = 'CHECK_SEVERITY_UNSPECIFIED';
  /**
   * Important privacy issue.
   */
  public const SEVERITY_PRIORITY = 'PRIORITY';
  /**
   * Potential privacy issue.
   */
  public const SEVERITY_POTENTIAL = 'POTENTIAL';
  /**
   * Opportunity to improve privacy coverage.
   */
  public const SEVERITY_OPPORTUNITY = 'OPPORTUNITY';
  /**
   * Not specified.
   */
  public const STATE_CHECK_STATE_UNSPECIFIED = 'CHECK_STATE_UNSPECIFIED';
  /**
   * The check passed.
   */
  public const STATE_PASSED = 'PASSED';
  /**
   * The check failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The check was not run.
   */
  public const STATE_UNCHECKED = 'UNCHECKED';
  /**
   * Not specified.
   */
  public const TYPE_CHECK_TYPE_UNSPECIFIED = 'CHECK_TYPE_UNSPECIFIED';
  /**
   * Checks that your store listing includes a working link to your privacy
   * policy.
   */
  public const TYPE_STORE_LISTING_PRIVACY_POLICY_LINK_PRESENT = 'STORE_LISTING_PRIVACY_POLICY_LINK_PRESENT';
  /**
   * Checks that your privacy policy has been updated recently.
   */
  public const TYPE_PRIVACY_POLICY_UPDATE_DATE_RECENT = 'PRIVACY_POLICY_UPDATE_DATE_RECENT';
  /**
   * Checks if your privacy policy references rights under GDPR for users in the
   * EU.
   */
  public const TYPE_PRIVACY_POLICY_GDPR_GENERAL_RULES = 'PRIVACY_POLICY_GDPR_GENERAL_RULES';
  /**
   * Checks if your privacy policy references rights under the CCPA.
   */
  public const TYPE_PRIVACY_POLICY_CCPA_GENERAL_RULES = 'PRIVACY_POLICY_CCPA_GENERAL_RULES';
  /**
   * Checks if your privacy policy mentions the categories of personal data that
   * are collected.
   */
  public const TYPE_PRIVACY_POLICY_COLLECTION_CATEGORIES_DATA_NOTICE = 'PRIVACY_POLICY_COLLECTION_CATEGORIES_DATA_NOTICE';
  /**
   * Checks if your privacy policy explains why personal data is processed.
   */
  public const TYPE_PRIVACY_POLICY_PROCESSING_PURPOSE_DATA_NOTICE = 'PRIVACY_POLICY_PROCESSING_PURPOSE_DATA_NOTICE';
  /**
   * Checks if your privacy policy includes information about third-party
   * sharing of personal data.
   */
  public const TYPE_PRIVACY_POLICY_SHARING_CATEGORIES_DATA_NOTICE = 'PRIVACY_POLICY_SHARING_CATEGORIES_DATA_NOTICE';
  /**
   * Checks if your privacy policy describes your data retention practices.
   */
  public const TYPE_PRIVACY_POLICY_DATA_RETENTION_NOTICE = 'PRIVACY_POLICY_DATA_RETENTION_NOTICE';
  /**
   * Checks if contact information is included in your privacy policy.
   */
  public const TYPE_PRIVACY_POLICY_CONTACT_DETAILS_NOTICE = 'PRIVACY_POLICY_CONTACT_DETAILS_NOTICE';
  /**
   * Checks if information about requirements related to children is included in
   * your privacy policy.
   */
  public const TYPE_PRIVACY_POLICY_CHILDREN_GENERAL_RULES = 'PRIVACY_POLICY_CHILDREN_GENERAL_RULES';
  /**
   * Checks if the Phone Number data type declaration in your privacy policy
   * matches usage.
   */
  public const TYPE_PRIVACY_POLICY_DATA_TYPE_PHONE_NUMBER = 'PRIVACY_POLICY_DATA_TYPE_PHONE_NUMBER';
  /**
   * Checks if the User Account Info data type declaration in your privacy
   * policy matches usage.
   */
  public const TYPE_PRIVACY_POLICY_DATA_TYPE_USER_ACCOUNT_INFO = 'PRIVACY_POLICY_DATA_TYPE_USER_ACCOUNT_INFO';
  /**
   * Checks if the Precise Location data type declaration in your privacy policy
   * matches usage.
   */
  public const TYPE_PRIVACY_POLICY_DATA_TYPE_PRECISE_LOCATION = 'PRIVACY_POLICY_DATA_TYPE_PRECISE_LOCATION';
  /**
   * Checks if the Device ID data type declaration in your privacy policy
   * matches usage.
   */
  public const TYPE_PRIVACY_POLICY_DATA_TYPE_DEVICE_ID = 'PRIVACY_POLICY_DATA_TYPE_DEVICE_ID';
  /**
   * Checks if the Apps on Device data type declaration in your privacy policy
   * matches usage.
   */
  public const TYPE_PRIVACY_POLICY_DATA_TYPE_APPS_ON_DEVICE = 'PRIVACY_POLICY_DATA_TYPE_APPS_ON_DEVICE';
  /**
   * Checks if the Contacts data type declaration in your privacy policy matches
   * usage.
   */
  public const TYPE_PRIVACY_POLICY_DATA_TYPE_CONTACTS = 'PRIVACY_POLICY_DATA_TYPE_CONTACTS';
  /**
   * Checks if the Text Messages data type declaration in your privacy policy
   * matches usage.
   */
  public const TYPE_PRIVACY_POLICY_DATA_TYPE_TEXT_MESSAGES = 'PRIVACY_POLICY_DATA_TYPE_TEXT_MESSAGES';
  /**
   * Checks if the PII data type declaration in your privacy policy matches
   * usage.
   */
  public const TYPE_PRIVACY_POLICY_DATA_TYPE_PII = 'PRIVACY_POLICY_DATA_TYPE_PII';
  /**
   * Checks if the PII Categories data type declaration in your privacy policy
   * matches usage.
   */
  public const TYPE_PRIVACY_POLICY_DATA_TYPE_PII_CATEGORIES = 'PRIVACY_POLICY_DATA_TYPE_PII_CATEGORIES';
  /**
   * Checks if the Health and Biometric data type declaration in your privacy
   * policy matches usage.
   */
  public const TYPE_PRIVACY_POLICY_DATA_TYPE_HEALTH_AND_BIOMETRIC = 'PRIVACY_POLICY_DATA_TYPE_HEALTH_AND_BIOMETRIC';
  /**
   * Checks if your privacy policy references rights under LGPD for users in
   * Brazil.
   */
  public const TYPE_PRIVACY_POLICY_BRAZIL_LGPD_GENERAL_RULES = 'PRIVACY_POLICY_BRAZIL_LGPD_GENERAL_RULES';
  /**
   * Checks if your privacy policy references rights under VCDPA for users in
   * Virginia.
   */
  public const TYPE_PRIVACY_POLICY_VIRGINIA_VCDPA_GENERAL_RULES = 'PRIVACY_POLICY_VIRGINIA_VCDPA_GENERAL_RULES';
  /**
   * Checks if your privacy policy identifies your company or app name(s).
   */
  public const TYPE_PRIVACY_POLICY_AFFILIATION_MENTION = 'PRIVACY_POLICY_AFFILIATION_MENTION';
  /**
   * Checks if your privacy policy mentions your users' right to delete their
   * data.
   */
  public const TYPE_PRIVACY_POLICY_RIGHT_TO_DELETE_NOTICE = 'PRIVACY_POLICY_RIGHT_TO_DELETE_NOTICE';
  /**
   * Checks if your privacy policy mentions your users' right to access the data
   * held about them.
   */
  public const TYPE_PRIVACY_POLICY_RIGHT_TO_ACCESS_NOTICE = 'PRIVACY_POLICY_RIGHT_TO_ACCESS_NOTICE';
  /**
   * Checks if your privacy policy mentions your users' right to correct
   * inaccuracies within their data.
   */
  public const TYPE_PRIVACY_POLICY_RIGHT_TO_RECTIFICATION_NOTICE = 'PRIVACY_POLICY_RIGHT_TO_RECTIFICATION_NOTICE';
  /**
   * Checks if your privacy policy mentions your users' right to know about
   * information selling.
   */
  public const TYPE_PRIVACY_POLICY_RIGHT_TO_KNOW_ABOUT_SELLING_NOTICE = 'PRIVACY_POLICY_RIGHT_TO_KNOW_ABOUT_SELLING_NOTICE';
  /**
   * Checks if your privacy policy mentions your users' right to know about
   * information sharing.
   */
  public const TYPE_PRIVACY_POLICY_RIGHT_TO_KNOW_ABOUT_SHARING_NOTICE = 'PRIVACY_POLICY_RIGHT_TO_KNOW_ABOUT_SHARING_NOTICE';
  /**
   * Checks if your privacy policy mentions your users' right to opt out from
   * information selling.
   */
  public const TYPE_PRIVACY_POLICY_RIGHT_TO_OPT_OUT_FROM_SELLING_NOTICE = 'PRIVACY_POLICY_RIGHT_TO_OPT_OUT_FROM_SELLING_NOTICE';
  /**
   * Checks if your privacy policy explains how your users opt out from the
   * selling or sharing of their data.
   */
  public const TYPE_PRIVACY_POLICY_METHOD_TO_OPT_OUT_FROM_SELLING_OR_SHARING_NOTICE = 'PRIVACY_POLICY_METHOD_TO_OPT_OUT_FROM_SELLING_OR_SHARING_NOTICE';
  /**
   * Checks if your privacy policy provides the name and contact information for
   * your data controller.
   */
  public const TYPE_PRIVACY_POLICY_DATA_CONTROLLER_IDENTITY = 'PRIVACY_POLICY_DATA_CONTROLLER_IDENTITY';
  /**
   * Checks if your privacy policy provides the name and contact information for
   * your Data Protection Officer.
   */
  public const TYPE_PRIVACY_POLICY_DPO_CONTACT_DETAILS = 'PRIVACY_POLICY_DPO_CONTACT_DETAILS';
  /**
   * Checks if your privacy policy mentions your users' right to lodge a
   * complaint with a supervisory authority.
   */
  public const TYPE_PRIVACY_POLICY_RIGHT_TO_LODGE_A_COMPLAINT = 'PRIVACY_POLICY_RIGHT_TO_LODGE_A_COMPLAINT';
  /**
   * Checks if your privacy policy mentions the legal basis you rely on for
   * processing your users' data.
   */
  public const TYPE_PRIVACY_POLICY_LEGAL_BASIS = 'PRIVACY_POLICY_LEGAL_BASIS';
  /**
   * Checks if your privacy policy mentions what personal information is
   * collected from children.
   */
  public const TYPE_PRIVACY_POLICY_CHILDREN_INFO_COLLECTION = 'PRIVACY_POLICY_CHILDREN_INFO_COLLECTION';
  /**
   * Checks if your privacy policy mentions why you collect personal information
   * from children.
   */
  public const TYPE_PRIVACY_POLICY_CHILDREN_INFO_USAGE_PURPOSES = 'PRIVACY_POLICY_CHILDREN_INFO_USAGE_PURPOSES';
  /**
   * Checks if your privacy policy mentions what personal information from
   * children is shared with third parties.
   */
  public const TYPE_PRIVACY_POLICY_CHILDREN_INFO_DISCLOSURE_PRACTICES = 'PRIVACY_POLICY_CHILDREN_INFO_DISCLOSURE_PRACTICES';
  /**
   * Checks if your privacy policy mentions whether your app allows children to
   * make their personal information publicly available.
   */
  public const TYPE_PRIVACY_POLICY_CHILDREN_INFO_PUBLICITY = 'PRIVACY_POLICY_CHILDREN_INFO_PUBLICITY';
  /**
   * Checks if your privacy policy mentions how parents/caregivers/guardians can
   * request the deletion of their child's personal information.
   */
  public const TYPE_PRIVACY_POLICY_PARENTS_METHOD_OF_INFO_DELETION = 'PRIVACY_POLICY_PARENTS_METHOD_OF_INFO_DELETION';
  /**
   * Checks if your privacy policy mentions how parents/caregivers/guardians can
   * review their child's personal information.
   */
  public const TYPE_PRIVACY_POLICY_PARENTS_METHOD_TO_INFO_REVIEW = 'PRIVACY_POLICY_PARENTS_METHOD_TO_INFO_REVIEW';
  /**
   * Checks if your privacy policy explains how a parent/caregiver/guardian can
   * stop the collection/use from their child's personal information.
   */
  public const TYPE_PRIVACY_POLICY_PARENTS_METHOD_TO_STOP_FURTHER_INFO_COLLECTION_USE = 'PRIVACY_POLICY_PARENTS_METHOD_TO_STOP_FURTHER_INFO_COLLECTION_USE';
  /**
   * Checks if your privacy policy mentions the right of a
   * parent/caregiver/guardian to request the deletion of their child's personal
   * information.
   */
  public const TYPE_PRIVACY_POLICY_PARENTS_RIGHT_TO_INFO_DELETION = 'PRIVACY_POLICY_PARENTS_RIGHT_TO_INFO_DELETION';
  /**
   * Checks if your privacy policy mentions the right of a
   * parent/caregiver/guardian to review their child's personal information.
   */
  public const TYPE_PRIVACY_POLICY_PARENTS_RIGHT_TO_INFO_REVIEW = 'PRIVACY_POLICY_PARENTS_RIGHT_TO_INFO_REVIEW';
  /**
   * Checks if your privacy policy mentions the right of a
   * parent/caregiver/guardian to stop collection/use from their child's
   * personal information.
   */
  public const TYPE_PRIVACY_POLICY_PARENTS_RIGHT_TO_STOP_FURTHER_INFO_COLLECTION_USE = 'PRIVACY_POLICY_PARENTS_RIGHT_TO_STOP_FURTHER_INFO_COLLECTION_USE';
  /**
   * Checks if your privacy policy mentions collection of your users'
   * approximate location if this data type is declared in your Play Data Safety
   * Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_APPROXIMATE_LOCATION = 'PRIVACY_POLICY_PSL_APPROXIMATE_LOCATION';
  /**
   * Checks if your privacy policy mentions collection of your users' precise
   * location if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_PRECISE_LOCATION = 'PRIVACY_POLICY_PSL_PRECISE_LOCATION';
  /**
   * Checks if your privacy policy mentions collection of your users' personal
   * names if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_NAME = 'PRIVACY_POLICY_PSL_NAME';
  /**
   * Checks if your privacy policy mentions collection of your users' email
   * addresses if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_EMAIL_ADDRESS = 'PRIVACY_POLICY_PSL_EMAIL_ADDRESS';
  /**
   * Checks if your privacy policy mentions collection of your users' user IDs
   * if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_USER_IDENTIFIERS = 'PRIVACY_POLICY_PSL_USER_IDENTIFIERS';
  /**
   * Checks if your privacy policy mentions collection of your users' physical
   * addresses if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_ADDRESS = 'PRIVACY_POLICY_PSL_ADDRESS';
  /**
   * Checks if your privacy policy mentions collection of your users' phone
   * numbers if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_PHONE_NUMBER = 'PRIVACY_POLICY_PSL_PHONE_NUMBER';
  /**
   * Checks if your privacy policy mentions collection of your users' race or
   * ethnicity if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_RACE_AND_ETHNICITY = 'PRIVACY_POLICY_PSL_RACE_AND_ETHNICITY';
  /**
   * Checks if your privacy policy mentions collection of your users' credit
   * score if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_CREDIT_SCORE = 'PRIVACY_POLICY_PSL_CREDIT_SCORE';
  /**
   * Checks if your privacy policy mentions collection of your users' purchase
   * history if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_PURCHASE_HISTORY = 'PRIVACY_POLICY_PSL_PURCHASE_HISTORY';
  /**
   * Checks if your privacy policy mentions collection of your users' health
   * info if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_HEALTH_INFO = 'PRIVACY_POLICY_PSL_HEALTH_INFO';
  /**
   * Checks if your privacy policy mentions collection of your users' fitness
   * info if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_FITNESS_INFO = 'PRIVACY_POLICY_PSL_FITNESS_INFO';
  /**
   * Checks if your privacy policy mentions collection of your users' emails if
   * this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_EMAIL_MESSAGES = 'PRIVACY_POLICY_PSL_EMAIL_MESSAGES';
  /**
   * Checks if your privacy policy mentions collection of your users' text
   * messages if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_TEXT_MESSAGES = 'PRIVACY_POLICY_PSL_TEXT_MESSAGES';
  /**
   * Checks if your privacy policy mentions collection of your users' photos if
   * this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_PHOTOS = 'PRIVACY_POLICY_PSL_PHOTOS';
  /**
   * Checks if your privacy policy mentions collection of your users' videos if
   * this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_VIDEOS = 'PRIVACY_POLICY_PSL_VIDEOS';
  /**
   * Checks if your privacy policy mentions collection of your users' music
   * files if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_MUSIC_FILES = 'PRIVACY_POLICY_PSL_MUSIC_FILES';
  /**
   * Checks if your privacy policy mentions collection of your users' voice or
   * sound recordings if this data type is declared in your Play Data Safety
   * Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_VOICE_OR_SOUND_RECORDINGS = 'PRIVACY_POLICY_PSL_VOICE_OR_SOUND_RECORDINGS';
  /**
   * Checks if your privacy policy mentions collection of your users' files or
   * documents if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_FILES_AND_DOCS = 'PRIVACY_POLICY_PSL_FILES_AND_DOCS';
  /**
   * Checks if your privacy policy mentions collection of your users' calendar
   * events if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_CALENDAR_EVENTS = 'PRIVACY_POLICY_PSL_CALENDAR_EVENTS';
  /**
   * Checks if your privacy policy mentions collection of your users' contacts
   * if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_CONTACTS = 'PRIVACY_POLICY_PSL_CONTACTS';
  /**
   * Checks if your privacy policy mentions collection of your users' app
   * interactions if this data type is declared in your Play Data Safety
   * Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_APP_INTERACTIONS = 'PRIVACY_POLICY_PSL_APP_INTERACTIONS';
  /**
   * Checks if your privacy policy mentions collection of your users' in-app
   * search history if this data type is declared in your Play Data Safety
   * Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_IN_APP_SEARCH_HISTORY = 'PRIVACY_POLICY_PSL_IN_APP_SEARCH_HISTORY';
  /**
   * Checks if your privacy policy mentions collection of your users' web
   * browsing history if this data type is declared in your Play Data Safety
   * Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_WEB_BROWSING_HISTORY = 'PRIVACY_POLICY_PSL_WEB_BROWSING_HISTORY';
  /**
   * Checks if your privacy policy mentions collection of your users' installed
   * apps if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_INSTALLED_APPS = 'PRIVACY_POLICY_PSL_INSTALLED_APPS';
  /**
   * Checks if your privacy policy mentions collection of your users' crash logs
   * if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_CRASH_LOGS = 'PRIVACY_POLICY_PSL_CRASH_LOGS';
  /**
   * Checks if your privacy policy mentions collection of your users'
   * performance diagnostics if this data type is declared in your Play Data
   * Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_DIAGNOSTICS = 'PRIVACY_POLICY_PSL_DIAGNOSTICS';
  /**
   * Checks if your privacy policy mentions collection of your users' device or
   * other IDs if this data type is declared in your Play Data Safety Section.
   */
  public const TYPE_PRIVACY_POLICY_PSL_DEVICE_OR_OTHER_IDS = 'PRIVACY_POLICY_PSL_DEVICE_OR_OTHER_IDS';
  /**
   * Checks if there is a new endpoint we've recently detected. Because this
   * check accounts for flakiness, it may fail for several weeks even if the
   * endpoint is not detected in the current report.
   */
  public const TYPE_DATA_MONITORING_NEW_ENDPOINT = 'DATA_MONITORING_NEW_ENDPOINT';
  /**
   * Checks if there is a new permission we've recently detected. Because this
   * check accounts for flakiness, it may fail for several weeks even if the
   * permission is not detected in the current report.
   */
  public const TYPE_DATA_MONITORING_NEW_PERMISSION = 'DATA_MONITORING_NEW_PERMISSION';
  /**
   * Checks if there is a new data type we've recently detected. Because this
   * check accounts for flakiness, it may fail for several weeks even if the
   * data type is not detected in the current report.
   */
  public const TYPE_DATA_MONITORING_NEW_DATA_TYPE = 'DATA_MONITORING_NEW_DATA_TYPE';
  /**
   * Checks if there is a new SDK we've recently detected. Because this check
   * accounts for flakiness, it may fail for several weeks even if the SDK is
   * not detected in the current report.
   */
  public const TYPE_DATA_MONITORING_NEW_SDK = 'DATA_MONITORING_NEW_SDK';
  /**
   * Checks if there is any endpoint contacted using HTTP protocol instead of
   * HTTPS. If no protocol is found in the URL, the endpoint is not considered
   * for analysis.
   */
  public const TYPE_DATA_MONITORING_ENCRYPTION = 'DATA_MONITORING_ENCRYPTION';
  /**
   * Checks if new data types have been detected since a specific app version.
   */
  public const TYPE_DATA_MONITORING_NEW_DATA_TYPE_VERSION_DIFF = 'DATA_MONITORING_NEW_DATA_TYPE_VERSION_DIFF';
  /**
   * Checks if new endpoints have been detected since a specific app version.
   */
  public const TYPE_DATA_MONITORING_NEW_ENDPOINT_VERSION_DIFF = 'DATA_MONITORING_NEW_ENDPOINT_VERSION_DIFF';
  /**
   * Checks if new permissions have been detected since a specific app version.
   */
  public const TYPE_DATA_MONITORING_NEW_PERMISSION_VERSION_DIFF = 'DATA_MONITORING_NEW_PERMISSION_VERSION_DIFF';
  /**
   * Checks if new SDKs have been detected since a specific app version.
   */
  public const TYPE_DATA_MONITORING_NEW_SDK_VERSION_DIFF = 'DATA_MONITORING_NEW_SDK_VERSION_DIFF';
  /**
   * Checks if any SDKs were detected that are specified in the denylist.
   */
  public const TYPE_DATA_MONITORING_SDKS_DENYLIST_VIOLATION = 'DATA_MONITORING_SDKS_DENYLIST_VIOLATION';
  /**
   * Checks if any permissions were detected that are specified in the denylist.
   */
  public const TYPE_DATA_MONITORING_PERMISSIONS_DENYLIST_VIOLATION = 'DATA_MONITORING_PERMISSIONS_DENYLIST_VIOLATION';
  /**
   * Checks if any endpoints were detected that are specified in the denylist.
   */
  public const TYPE_DATA_MONITORING_ENDPOINTS_DENYLIST_VIOLATION = 'DATA_MONITORING_ENDPOINTS_DENYLIST_VIOLATION';
  /**
   * Checks if there are any outdated SDKs.
   */
  public const TYPE_DATA_MONITORING_OUTDATED_SDK_VERSION = 'DATA_MONITORING_OUTDATED_SDK_VERSION';
  /**
   * Checks if there are any SDKs with critical issues.
   */
  public const TYPE_DATA_MONITORING_CRITICAL_SDK_ISSUE = 'DATA_MONITORING_CRITICAL_SDK_ISSUE';
  /**
   * Checks if the Sensitive Information data type declaration matches usage.
   */
  public const TYPE_PRIVACY_POLICY_DATA_TYPE_SENSITIVE_INFO = 'PRIVACY_POLICY_DATA_TYPE_SENSITIVE_INFO';
  /**
   * Checks if there were any PII leaked to device logs.
   */
  public const TYPE_DATA_MONITORING_PII_LOGCAT_LEAK = 'DATA_MONITORING_PII_LOGCAT_LEAK';
  /**
   * Checks if there are media (photo and video) permissions that are considered
   * sensitive and should be minimized for Android.
   */
  public const TYPE_DATA_MONITORING_MINIMIZE_PERMISSION_MEDIA = 'DATA_MONITORING_MINIMIZE_PERMISSION_MEDIA';
  /**
   * Checks if there are camera use permissions that are considered sensitive
   * and should be minimized for Android.
   */
  public const TYPE_DATA_MONITORING_MINIMIZE_PERMISSION_CAMERA = 'DATA_MONITORING_MINIMIZE_PERMISSION_CAMERA';
  /**
   * Checks if there are documents and file permissions that are considered
   * sensitive and should be minimized for Android.
   */
  public const TYPE_DATA_MONITORING_MINIMIZE_PERMISSION_DOCUMENTS = 'DATA_MONITORING_MINIMIZE_PERMISSION_DOCUMENTS';
  protected $collection_key = 'regionCodes';
  protected $citationsType = GoogleChecksReportV1alphaCheckCitation::class;
  protected $citationsDataType = 'array';
  protected $evidenceType = GoogleChecksReportV1alphaCheckEvidence::class;
  protected $evidenceDataType = '';
  /**
   * Regions that are impacted by the check. For more info, see
   * https://google.aip.dev/143#countries-and-regions.
   *
   * @var string[]
   */
  public $regionCodes;
  /**
   * The urgency or risk level of the check.
   *
   * @var string
   */
  public $severity;
  /**
   * The result after running the check.
   *
   * @var string
   */
  public $state;
  protected $stateMetadataType = GoogleChecksReportV1alphaCheckStateMetadata::class;
  protected $stateMetadataDataType = '';
  /**
   * The type of check that was run. A type will only appear once in a report's
   * list of checks.
   *
   * @var string
   */
  public $type;

  /**
   * Regulations and policies that serve as the legal basis for the check.
   *
   * @param GoogleChecksReportV1alphaCheckCitation[] $citations
   */
  public function setCitations($citations)
  {
    $this->citations = $citations;
  }
  /**
   * @return GoogleChecksReportV1alphaCheckCitation[]
   */
  public function getCitations()
  {
    return $this->citations;
  }
  /**
   * Evidence that substantiates the check result.
   *
   * @param GoogleChecksReportV1alphaCheckEvidence $evidence
   */
  public function setEvidence(GoogleChecksReportV1alphaCheckEvidence $evidence)
  {
    $this->evidence = $evidence;
  }
  /**
   * @return GoogleChecksReportV1alphaCheckEvidence
   */
  public function getEvidence()
  {
    return $this->evidence;
  }
  /**
   * Regions that are impacted by the check. For more info, see
   * https://google.aip.dev/143#countries-and-regions.
   *
   * @param string[] $regionCodes
   */
  public function setRegionCodes($regionCodes)
  {
    $this->regionCodes = $regionCodes;
  }
  /**
   * @return string[]
   */
  public function getRegionCodes()
  {
    return $this->regionCodes;
  }
  /**
   * The urgency or risk level of the check.
   *
   * Accepted values: CHECK_SEVERITY_UNSPECIFIED, PRIORITY, POTENTIAL,
   * OPPORTUNITY
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * The result after running the check.
   *
   * Accepted values: CHECK_STATE_UNSPECIFIED, PASSED, FAILED, UNCHECKED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Additional information about the check state in relation to past reports.
   *
   * @param GoogleChecksReportV1alphaCheckStateMetadata $stateMetadata
   */
  public function setStateMetadata(GoogleChecksReportV1alphaCheckStateMetadata $stateMetadata)
  {
    $this->stateMetadata = $stateMetadata;
  }
  /**
   * @return GoogleChecksReportV1alphaCheckStateMetadata
   */
  public function getStateMetadata()
  {
    return $this->stateMetadata;
  }
  /**
   * The type of check that was run. A type will only appear once in a report's
   * list of checks.
   *
   * Accepted values: CHECK_TYPE_UNSPECIFIED,
   * STORE_LISTING_PRIVACY_POLICY_LINK_PRESENT,
   * PRIVACY_POLICY_UPDATE_DATE_RECENT, PRIVACY_POLICY_GDPR_GENERAL_RULES,
   * PRIVACY_POLICY_CCPA_GENERAL_RULES,
   * PRIVACY_POLICY_COLLECTION_CATEGORIES_DATA_NOTICE,
   * PRIVACY_POLICY_PROCESSING_PURPOSE_DATA_NOTICE,
   * PRIVACY_POLICY_SHARING_CATEGORIES_DATA_NOTICE,
   * PRIVACY_POLICY_DATA_RETENTION_NOTICE,
   * PRIVACY_POLICY_CONTACT_DETAILS_NOTICE,
   * PRIVACY_POLICY_CHILDREN_GENERAL_RULES,
   * PRIVACY_POLICY_DATA_TYPE_PHONE_NUMBER,
   * PRIVACY_POLICY_DATA_TYPE_USER_ACCOUNT_INFO,
   * PRIVACY_POLICY_DATA_TYPE_PRECISE_LOCATION,
   * PRIVACY_POLICY_DATA_TYPE_DEVICE_ID,
   * PRIVACY_POLICY_DATA_TYPE_APPS_ON_DEVICE, PRIVACY_POLICY_DATA_TYPE_CONTACTS,
   * PRIVACY_POLICY_DATA_TYPE_TEXT_MESSAGES, PRIVACY_POLICY_DATA_TYPE_PII,
   * PRIVACY_POLICY_DATA_TYPE_PII_CATEGORIES,
   * PRIVACY_POLICY_DATA_TYPE_HEALTH_AND_BIOMETRIC,
   * PRIVACY_POLICY_BRAZIL_LGPD_GENERAL_RULES,
   * PRIVACY_POLICY_VIRGINIA_VCDPA_GENERAL_RULES,
   * PRIVACY_POLICY_AFFILIATION_MENTION, PRIVACY_POLICY_RIGHT_TO_DELETE_NOTICE,
   * PRIVACY_POLICY_RIGHT_TO_ACCESS_NOTICE,
   * PRIVACY_POLICY_RIGHT_TO_RECTIFICATION_NOTICE,
   * PRIVACY_POLICY_RIGHT_TO_KNOW_ABOUT_SELLING_NOTICE,
   * PRIVACY_POLICY_RIGHT_TO_KNOW_ABOUT_SHARING_NOTICE,
   * PRIVACY_POLICY_RIGHT_TO_OPT_OUT_FROM_SELLING_NOTICE,
   * PRIVACY_POLICY_METHOD_TO_OPT_OUT_FROM_SELLING_OR_SHARING_NOTICE,
   * PRIVACY_POLICY_DATA_CONTROLLER_IDENTITY,
   * PRIVACY_POLICY_DPO_CONTACT_DETAILS,
   * PRIVACY_POLICY_RIGHT_TO_LODGE_A_COMPLAINT, PRIVACY_POLICY_LEGAL_BASIS,
   * PRIVACY_POLICY_CHILDREN_INFO_COLLECTION,
   * PRIVACY_POLICY_CHILDREN_INFO_USAGE_PURPOSES,
   * PRIVACY_POLICY_CHILDREN_INFO_DISCLOSURE_PRACTICES,
   * PRIVACY_POLICY_CHILDREN_INFO_PUBLICITY,
   * PRIVACY_POLICY_PARENTS_METHOD_OF_INFO_DELETION,
   * PRIVACY_POLICY_PARENTS_METHOD_TO_INFO_REVIEW,
   * PRIVACY_POLICY_PARENTS_METHOD_TO_STOP_FURTHER_INFO_COLLECTION_USE,
   * PRIVACY_POLICY_PARENTS_RIGHT_TO_INFO_DELETION,
   * PRIVACY_POLICY_PARENTS_RIGHT_TO_INFO_REVIEW,
   * PRIVACY_POLICY_PARENTS_RIGHT_TO_STOP_FURTHER_INFO_COLLECTION_USE,
   * PRIVACY_POLICY_PSL_APPROXIMATE_LOCATION,
   * PRIVACY_POLICY_PSL_PRECISE_LOCATION, PRIVACY_POLICY_PSL_NAME,
   * PRIVACY_POLICY_PSL_EMAIL_ADDRESS, PRIVACY_POLICY_PSL_USER_IDENTIFIERS,
   * PRIVACY_POLICY_PSL_ADDRESS, PRIVACY_POLICY_PSL_PHONE_NUMBER,
   * PRIVACY_POLICY_PSL_RACE_AND_ETHNICITY, PRIVACY_POLICY_PSL_CREDIT_SCORE,
   * PRIVACY_POLICY_PSL_PURCHASE_HISTORY, PRIVACY_POLICY_PSL_HEALTH_INFO,
   * PRIVACY_POLICY_PSL_FITNESS_INFO, PRIVACY_POLICY_PSL_EMAIL_MESSAGES,
   * PRIVACY_POLICY_PSL_TEXT_MESSAGES, PRIVACY_POLICY_PSL_PHOTOS,
   * PRIVACY_POLICY_PSL_VIDEOS, PRIVACY_POLICY_PSL_MUSIC_FILES,
   * PRIVACY_POLICY_PSL_VOICE_OR_SOUND_RECORDINGS,
   * PRIVACY_POLICY_PSL_FILES_AND_DOCS, PRIVACY_POLICY_PSL_CALENDAR_EVENTS,
   * PRIVACY_POLICY_PSL_CONTACTS, PRIVACY_POLICY_PSL_APP_INTERACTIONS,
   * PRIVACY_POLICY_PSL_IN_APP_SEARCH_HISTORY,
   * PRIVACY_POLICY_PSL_WEB_BROWSING_HISTORY, PRIVACY_POLICY_PSL_INSTALLED_APPS,
   * PRIVACY_POLICY_PSL_CRASH_LOGS, PRIVACY_POLICY_PSL_DIAGNOSTICS,
   * PRIVACY_POLICY_PSL_DEVICE_OR_OTHER_IDS, DATA_MONITORING_NEW_ENDPOINT,
   * DATA_MONITORING_NEW_PERMISSION, DATA_MONITORING_NEW_DATA_TYPE,
   * DATA_MONITORING_NEW_SDK, DATA_MONITORING_ENCRYPTION,
   * DATA_MONITORING_NEW_DATA_TYPE_VERSION_DIFF,
   * DATA_MONITORING_NEW_ENDPOINT_VERSION_DIFF,
   * DATA_MONITORING_NEW_PERMISSION_VERSION_DIFF,
   * DATA_MONITORING_NEW_SDK_VERSION_DIFF,
   * DATA_MONITORING_SDKS_DENYLIST_VIOLATION,
   * DATA_MONITORING_PERMISSIONS_DENYLIST_VIOLATION,
   * DATA_MONITORING_ENDPOINTS_DENYLIST_VIOLATION,
   * DATA_MONITORING_OUTDATED_SDK_VERSION, DATA_MONITORING_CRITICAL_SDK_ISSUE,
   * PRIVACY_POLICY_DATA_TYPE_SENSITIVE_INFO, DATA_MONITORING_PII_LOGCAT_LEAK,
   * DATA_MONITORING_MINIMIZE_PERMISSION_MEDIA,
   * DATA_MONITORING_MINIMIZE_PERMISSION_CAMERA,
   * DATA_MONITORING_MINIMIZE_PERMISSION_DOCUMENTS
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksReportV1alphaCheck::class, 'Google_Service_ChecksService_GoogleChecksReportV1alphaCheck');
