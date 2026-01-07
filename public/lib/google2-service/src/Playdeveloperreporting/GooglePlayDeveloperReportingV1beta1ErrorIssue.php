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

class GooglePlayDeveloperReportingV1beta1ErrorIssue extends \Google\Collection
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
  protected $collection_key = 'sampleErrorReports';
  protected $annotationsType = GooglePlayDeveloperReportingV1beta1IssueAnnotation::class;
  protected $annotationsDataType = 'array';
  /**
   * Cause of the issue. Depending on the type this can be either: *
   * APPLICATION_NOT_RESPONDING: the type of ANR that occurred, e.g., 'Input
   * dispatching timed out'. * CRASH: for Java unhandled exception errors, the
   * type of the innermost exception that was thrown, e.g.,
   * IllegalArgumentException. For signals in native code, the signal that was
   * raised, e.g. SIGSEGV.
   *
   * @var string
   */
  public $cause;
  /**
   * An estimate of the number of unique users who have experienced this issue
   * (only considering occurrences matching the filters and within the requested
   * time period).
   *
   * @var string
   */
  public $distinctUsers;
  protected $distinctUsersPercentType = GoogleTypeDecimal::class;
  protected $distinctUsersPercentDataType = '';
  /**
   * The total number of error reports in this issue (only considering
   * occurrences matching the filters and within the requested time period).
   *
   * @var string
   */
  public $errorReportCount;
  protected $firstAppVersionType = GooglePlayDeveloperReportingV1beta1AppVersion::class;
  protected $firstAppVersionDataType = '';
  protected $firstOsVersionType = GooglePlayDeveloperReportingV1beta1OsVersion::class;
  protected $firstOsVersionDataType = '';
  /**
   * Link to the issue in Android vitals in the Play Console.
   *
   * @var string
   */
  public $issueUri;
  protected $lastAppVersionType = GooglePlayDeveloperReportingV1beta1AppVersion::class;
  protected $lastAppVersionDataType = '';
  /**
   * Start of the hour during which the last error report in this issue
   * occurred.
   *
   * @var string
   */
  public $lastErrorReportTime;
  protected $lastOsVersionType = GooglePlayDeveloperReportingV1beta1OsVersion::class;
  protected $lastOsVersionDataType = '';
  /**
   * Location where the issue happened. Depending on the type this can be
   * either: * APPLICATION_NOT_RESPONDING: the name of the activity or service
   * that stopped responding. * CRASH: the likely method name that caused the
   * error.
   *
   * @var string
   */
  public $location;
  /**
   * Identifier. The resource name of the issue. Format: apps/{app}/{issue}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Sample error reports which belong to this ErrorIssue. *Note:*
   * currently a maximum of 1 per ErrorIssue is supported. Format:
   * "apps/{app}/{report}"
   *
   * @var string[]
   */
  public $sampleErrorReports;
  /**
   * Type of the errors grouped in this issue.
   *
   * @var string
   */
  public $type;

  /**
   * List of annotations for an issue. Annotations provide additional
   * information that may help in diagnosing and fixing the issue.
   *
   * @param GooglePlayDeveloperReportingV1beta1IssueAnnotation[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return GooglePlayDeveloperReportingV1beta1IssueAnnotation[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Cause of the issue. Depending on the type this can be either: *
   * APPLICATION_NOT_RESPONDING: the type of ANR that occurred, e.g., 'Input
   * dispatching timed out'. * CRASH: for Java unhandled exception errors, the
   * type of the innermost exception that was thrown, e.g.,
   * IllegalArgumentException. For signals in native code, the signal that was
   * raised, e.g. SIGSEGV.
   *
   * @param string $cause
   */
  public function setCause($cause)
  {
    $this->cause = $cause;
  }
  /**
   * @return string
   */
  public function getCause()
  {
    return $this->cause;
  }
  /**
   * An estimate of the number of unique users who have experienced this issue
   * (only considering occurrences matching the filters and within the requested
   * time period).
   *
   * @param string $distinctUsers
   */
  public function setDistinctUsers($distinctUsers)
  {
    $this->distinctUsers = $distinctUsers;
  }
  /**
   * @return string
   */
  public function getDistinctUsers()
  {
    return $this->distinctUsers;
  }
  /**
   * An estimated percentage of users affected by any issue that are affected by
   * this issue (only considering occurrences matching the filters and within
   * the requested time period).
   *
   * @param GoogleTypeDecimal $distinctUsersPercent
   */
  public function setDistinctUsersPercent(GoogleTypeDecimal $distinctUsersPercent)
  {
    $this->distinctUsersPercent = $distinctUsersPercent;
  }
  /**
   * @return GoogleTypeDecimal
   */
  public function getDistinctUsersPercent()
  {
    return $this->distinctUsersPercent;
  }
  /**
   * The total number of error reports in this issue (only considering
   * occurrences matching the filters and within the requested time period).
   *
   * @param string $errorReportCount
   */
  public function setErrorReportCount($errorReportCount)
  {
    $this->errorReportCount = $errorReportCount;
  }
  /**
   * @return string
   */
  public function getErrorReportCount()
  {
    return $this->errorReportCount;
  }
  /**
   * The earliest (inclusive) app version appearing in this ErrorIssue in the
   * requested time period (only considering occurrences matching the filters).
   *
   * @param GooglePlayDeveloperReportingV1beta1AppVersion $firstAppVersion
   */
  public function setFirstAppVersion(GooglePlayDeveloperReportingV1beta1AppVersion $firstAppVersion)
  {
    $this->firstAppVersion = $firstAppVersion;
  }
  /**
   * @return GooglePlayDeveloperReportingV1beta1AppVersion
   */
  public function getFirstAppVersion()
  {
    return $this->firstAppVersion;
  }
  /**
   * The smallest OS version in which this error cluster has occurred in the
   * requested time period (only considering occurrences matching the filters
   * and within the requested time period).
   *
   * @param GooglePlayDeveloperReportingV1beta1OsVersion $firstOsVersion
   */
  public function setFirstOsVersion(GooglePlayDeveloperReportingV1beta1OsVersion $firstOsVersion)
  {
    $this->firstOsVersion = $firstOsVersion;
  }
  /**
   * @return GooglePlayDeveloperReportingV1beta1OsVersion
   */
  public function getFirstOsVersion()
  {
    return $this->firstOsVersion;
  }
  /**
   * Link to the issue in Android vitals in the Play Console.
   *
   * @param string $issueUri
   */
  public function setIssueUri($issueUri)
  {
    $this->issueUri = $issueUri;
  }
  /**
   * @return string
   */
  public function getIssueUri()
  {
    return $this->issueUri;
  }
  /**
   * The latest (inclusive) app version appearing in this ErrorIssue in the
   * requested time period (only considering occurrences matching the filters).
   *
   * @param GooglePlayDeveloperReportingV1beta1AppVersion $lastAppVersion
   */
  public function setLastAppVersion(GooglePlayDeveloperReportingV1beta1AppVersion $lastAppVersion)
  {
    $this->lastAppVersion = $lastAppVersion;
  }
  /**
   * @return GooglePlayDeveloperReportingV1beta1AppVersion
   */
  public function getLastAppVersion()
  {
    return $this->lastAppVersion;
  }
  /**
   * Start of the hour during which the last error report in this issue
   * occurred.
   *
   * @param string $lastErrorReportTime
   */
  public function setLastErrorReportTime($lastErrorReportTime)
  {
    $this->lastErrorReportTime = $lastErrorReportTime;
  }
  /**
   * @return string
   */
  public function getLastErrorReportTime()
  {
    return $this->lastErrorReportTime;
  }
  /**
   * The latest OS version in which this error cluster has occurred in the
   * requested time period (only considering occurrences matching the filters
   * and within the requested time period).
   *
   * @param GooglePlayDeveloperReportingV1beta1OsVersion $lastOsVersion
   */
  public function setLastOsVersion(GooglePlayDeveloperReportingV1beta1OsVersion $lastOsVersion)
  {
    $this->lastOsVersion = $lastOsVersion;
  }
  /**
   * @return GooglePlayDeveloperReportingV1beta1OsVersion
   */
  public function getLastOsVersion()
  {
    return $this->lastOsVersion;
  }
  /**
   * Location where the issue happened. Depending on the type this can be
   * either: * APPLICATION_NOT_RESPONDING: the name of the activity or service
   * that stopped responding. * CRASH: the likely method name that caused the
   * error.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Identifier. The resource name of the issue. Format: apps/{app}/{issue}
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
   * Output only. Sample error reports which belong to this ErrorIssue. *Note:*
   * currently a maximum of 1 per ErrorIssue is supported. Format:
   * "apps/{app}/{report}"
   *
   * @param string[] $sampleErrorReports
   */
  public function setSampleErrorReports($sampleErrorReports)
  {
    $this->sampleErrorReports = $sampleErrorReports;
  }
  /**
   * @return string[]
   */
  public function getSampleErrorReports()
  {
    return $this->sampleErrorReports;
  }
  /**
   * Type of the errors grouped in this issue.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePlayDeveloperReportingV1beta1ErrorIssue::class, 'Google_Service_Playdeveloperreporting_GooglePlayDeveloperReportingV1beta1ErrorIssue');
