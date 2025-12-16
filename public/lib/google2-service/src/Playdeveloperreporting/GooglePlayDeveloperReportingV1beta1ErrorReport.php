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

namespace Google\Service\Playdeveloperreporting;

class GooglePlayDeveloperReportingV1beta1ErrorReport extends \Google\Model
{
  /**
   * Unspecified error type.
   */
  public const TYPE_ERROR_TYPE_UNSPECIFIED = 'ERROR_TYPE_UNSPECIFIED';
  /**
   * Application Not Responding (ANR) error. To learn more about this type of
   * errors visit the corresponding Android Developers documentation.
   */
  public const TYPE_APPLICATION_NOT_RESPONDING = 'APPLICATION_NOT_RESPONDING';
  /**
   * Crash caused by an unhandled exception in Java (or Kotlin or any other JVM
   * language) or a signal in native code such as SIGSEGV.
   */
  public const TYPE_CRASH = 'CRASH';
  /**
   * Non-fatal caused by events that do not immediately cause crashes, but is
   * likely to lead to one.
   */
  public const TYPE_NON_FATAL = 'NON_FATAL';
  protected $appVersionType = GooglePlayDeveloperReportingV1beta1AppVersion::class;
  protected $appVersionDataType = '';
  protected $deviceModelType = GooglePlayDeveloperReportingV1beta1DeviceModelSummary::class;
  protected $deviceModelDataType = '';
  /**
   * Start of the hour during which the latest event in this error report
   * occurred.
   *
   * @var string
   */
  public $eventTime;
  /**
   * The issue this report was associated with. **Please note:** this resource
   * is currently in Alpha. There could be changes to the issue grouping that
   * would result in similar but more recent error reports being assigned to a
   * different issue.
   *
   * @var string
   */
  public $issue;
  /**
   * The resource name of the report. Format: apps/{app}/{report}
   *
   * @var string
   */
  public $name;
  protected $osVersionType = GooglePlayDeveloperReportingV1beta1OsVersion::class;
  protected $osVersionDataType = '';
  /**
   * Textual representation of the error report. These textual reports are
   * produced by the platform. The reports are then sanitized and filtered to
   * remove any potentially sensitive information. Although their format is
   * fairly stable, they are not entirely meant for machine consumption and we
   * cannot guarantee that there won't be subtle changes to the formatting that
   * may break systems trying to parse information out of the reports.
   *
   * @var string
   */
  public $reportText;
  /**
   * Type of the error for which this report was generated.
   *
   * @var string
   */
  public $type;
  /**
   * Version control system information from BUNDLE-METADATA/version-control-
   * info.textproto or META-INF/version-control-info.textproto of the app bundle
   * or APK, respectively.
   *
   * @var string
   */
  public $vcsInformation;

  /**
   * The app version on which an event in this error report occurred on.
   *
   * @param GooglePlayDeveloperReportingV1beta1AppVersion $appVersion
   */
  public function setAppVersion(GooglePlayDeveloperReportingV1beta1AppVersion $appVersion)
  {
    $this->appVersion = $appVersion;
  }
  /**
   * @return GooglePlayDeveloperReportingV1beta1AppVersion
   */
  public function getAppVersion()
  {
    return $this->appVersion;
  }
  /**
   * A device model on which an event in this error report occurred on.
   *
   * @param GooglePlayDeveloperReportingV1beta1DeviceModelSummary $deviceModel
   */
  public function setDeviceModel(GooglePlayDeveloperReportingV1beta1DeviceModelSummary $deviceModel)
  {
    $this->deviceModel = $deviceModel;
  }
  /**
   * @return GooglePlayDeveloperReportingV1beta1DeviceModelSummary
   */
  public function getDeviceModel()
  {
    return $this->deviceModel;
  }
  /**
   * Start of the hour during which the latest event in this error report
   * occurred.
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * The issue this report was associated with. **Please note:** this resource
   * is currently in Alpha. There could be changes to the issue grouping that
   * would result in similar but more recent error reports being assigned to a
   * different issue.
   *
   * @param string $issue
   */
  public function setIssue($issue)
  {
    $this->issue = $issue;
  }
  /**
   * @return string
   */
  public function getIssue()
  {
    return $this->issue;
  }
  /**
   * The resource name of the report. Format: apps/{app}/{report}
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
   * The OS version on which an event in this error report occurred on.
   *
   * @param GooglePlayDeveloperReportingV1beta1OsVersion $osVersion
   */
  public function setOsVersion(GooglePlayDeveloperReportingV1beta1OsVersion $osVersion)
  {
    $this->osVersion = $osVersion;
  }
  /**
   * @return GooglePlayDeveloperReportingV1beta1OsVersion
   */
  public function getOsVersion()
  {
    return $this->osVersion;
  }
  /**
   * Textual representation of the error report. These textual reports are
   * produced by the platform. The reports are then sanitized and filtered to
   * remove any potentially sensitive information. Although their format is
   * fairly stable, they are not entirely meant for machine consumption and we
   * cannot guarantee that there won't be subtle changes to the formatting that
   * may break systems trying to parse information out of the reports.
   *
   * @param string $reportText
   */
  public function setReportText($reportText)
  {
    $this->reportText = $reportText;
  }
  /**
   * @return string
   */
  public function getReportText()
  {
    return $this->reportText;
  }
  /**
   * Type of the error for which this report was generated.
   *
   * Accepted values: ERROR_TYPE_UNSPECIFIED, APPLICATION_NOT_RESPONDING, CRASH,
   * NON_FATAL
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
  /**
   * Version control system information from BUNDLE-METADATA/version-control-
   * info.textproto or META-INF/version-control-info.textproto of the app bundle
   * or APK, respectively.
   *
   * @param string $vcsInformation
   */
  public function setVcsInformation($vcsInformation)
  {
    $this->vcsInformation = $vcsInformation;
  }
  /**
   * @return string
   */
  public function getVcsInformation()
  {
    return $this->vcsInformation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePlayDeveloperReportingV1beta1ErrorReport::class, 'Google_Service_Playdeveloperreporting_GooglePlayDeveloperReportingV1beta1ErrorReport');
