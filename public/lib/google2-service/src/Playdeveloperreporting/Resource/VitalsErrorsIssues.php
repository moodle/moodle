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

namespace Google\Service\Playdeveloperreporting\Resource;

use Google\Service\Playdeveloperreporting\GooglePlayDeveloperReportingV1beta1SearchErrorIssuesResponse;

/**
 * The "issues" collection of methods.
 * Typical usage is:
 *  <code>
 *   $playdeveloperreportingService = new Google\Service\Playdeveloperreporting(...);
 *   $issues = $playdeveloperreportingService->vitals_errors_issues;
 *  </code>
 */
class VitalsErrorsIssues extends \Google\Service\Resource
{
  /**
   * Searches all error issues in which reports have been grouped. (issues.search)
   *
   * @param string $parent Required. Parent resource of the error issues,
   * indicating the application for which they were received. Format: apps/{app}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A selection predicate to retrieve only a subset of
   * the issues. Counts in the returned error issues will only reflect occurrences
   * that matched the filter. For filtering basics, please check
   * [AIP-160](https://google.aip.dev/160). ** Supported field names:** *
   * `apiLevel`: Matches error issues that occurred in the requested Android
   * versions (specified as the numeric API level) only. Example: `apiLevel = 28
   * OR apiLevel = 29`. * `versionCode`: Matches error issues that occurred in the
   * requested app version codes only. Example: `versionCode = 123 OR versionCode
   * = 456`. * `deviceModel`: Matches error issues that occurred in the requested
   * devices. Example: `deviceModel = "google/walleye" OR deviceModel =
   * "google/marlin"`. * `deviceBrand`: Matches error issues that occurred in the
   * requested device brands. Example: `deviceBrand = "Google". * `deviceType`:
   * Matches error issues that occurred in the requested device types. Example:
   * `deviceType = "PHONE"`. * `errorIssueType`: Matches error issues of the
   * requested types only. Valid candidates: `CRASH`, `ANR`, `NON_FATAL`. Example:
   * `errorIssueType = CRASH OR errorIssueType = ANR`. * `appProcessState`:
   * Matches error issues on the process state of an app, indicating whether an
   * app runs in the foreground (user-visible) or background. Valid candidates:
   * `FOREGROUND`, `BACKGROUND`. Example: `appProcessState = FOREGROUND`. *
   * `isUserPerceived`: Matches error issues that are user-perceived. It is not
   * accompanied by any operators. Example: `isUserPerceived`. ** Supported
   * operators:** * Comparison operators: The only supported comparison operator
   * is equality. The filtered field must appear on the left hand side of the
   * comparison. * Logical Operators: Logical operators `AND` and `OR` can be used
   * to build complex filters following a conjunctive normal form (CNF), i.e.,
   * conjunctions of disjunctions. The `OR` operator takes precedence over `AND`
   * so the use of parenthesis is not necessary when building CNF. The `OR`
   * operator is only supported to build disjunctions that apply to the same
   * field, e.g., `versionCode = 123 OR errorIssueType = ANR` is not a valid
   * filter. ** Examples ** Some valid filtering expressions: * `versionCode = 123
   * AND errorIssueType = ANR` * `versionCode = 123 AND errorIssueType = OR
   * errorIssueType = CRASH` * `versionCode = 123 AND (errorIssueType = OR
   * errorIssueType = CRASH)`
   * @opt_param int interval.endTime.day Optional. Day of month. Must be from 1 to
   * 31 and valid for the year and month, or 0 if specifying a datetime without a
   * day.
   * @opt_param int interval.endTime.hours Optional. Hours of day in 24 hour
   * format. Should be from 0 to 23, defaults to 0 (midnight). An API may choose
   * to allow the value "24:00:00" for scenarios like business closing time.
   * @opt_param int interval.endTime.minutes Optional. Minutes of hour of day.
   * Must be from 0 to 59, defaults to 0.
   * @opt_param int interval.endTime.month Optional. Month of year. Must be from 1
   * to 12, or 0 if specifying a datetime without a month.
   * @opt_param int interval.endTime.nanos Optional. Fractions of seconds in
   * nanoseconds. Must be from 0 to 999,999,999, defaults to 0.
   * @opt_param int interval.endTime.seconds Optional. Seconds of minutes of the
   * time. Must normally be from 0 to 59, defaults to 0. An API may allow the
   * value 60 if it allows leap-seconds.
   * @opt_param string interval.endTime.timeZone.id IANA Time Zone Database time
   * zone. For example "America/New_York".
   * @opt_param string interval.endTime.timeZone.version Optional. IANA Time Zone
   * Database version number. For example "2019a".
   * @opt_param string interval.endTime.utcOffset UTC offset. Must be whole
   * seconds, between -18 hours and +18 hours. For example, a UTC offset of -4:00
   * would be represented as { seconds: -14400 }.
   * @opt_param int interval.endTime.year Optional. Year of date. Must be from 1
   * to 9999, or 0 if specifying a datetime without a year.
   * @opt_param int interval.startTime.day Optional. Day of month. Must be from 1
   * to 31 and valid for the year and month, or 0 if specifying a datetime without
   * a day.
   * @opt_param int interval.startTime.hours Optional. Hours of day in 24 hour
   * format. Should be from 0 to 23, defaults to 0 (midnight). An API may choose
   * to allow the value "24:00:00" for scenarios like business closing time.
   * @opt_param int interval.startTime.minutes Optional. Minutes of hour of day.
   * Must be from 0 to 59, defaults to 0.
   * @opt_param int interval.startTime.month Optional. Month of year. Must be from
   * 1 to 12, or 0 if specifying a datetime without a month.
   * @opt_param int interval.startTime.nanos Optional. Fractions of seconds in
   * nanoseconds. Must be from 0 to 999,999,999, defaults to 0.
   * @opt_param int interval.startTime.seconds Optional. Seconds of minutes of the
   * time. Must normally be from 0 to 59, defaults to 0. An API may allow the
   * value 60 if it allows leap-seconds.
   * @opt_param string interval.startTime.timeZone.id IANA Time Zone Database time
   * zone. For example "America/New_York".
   * @opt_param string interval.startTime.timeZone.version Optional. IANA Time
   * Zone Database version number. For example "2019a".
   * @opt_param string interval.startTime.utcOffset UTC offset. Must be whole
   * seconds, between -18 hours and +18 hours. For example, a UTC offset of -4:00
   * would be represented as { seconds: -14400 }.
   * @opt_param int interval.startTime.year Optional. Year of date. Must be from 1
   * to 9999, or 0 if specifying a datetime without a year.
   * @opt_param string orderBy Specifies a field that will be used to order the
   * results. ** Supported dimensions:** * `errorReportCount`: Orders issues by
   * number of error reports. * `distinctUsers`: Orders issues by number of unique
   * affected users. ** Supported operations:** * `asc` for ascending order. *
   * `desc` for descending order. Format: A field and an operation, e.g.,
   * `errorReportCount desc` *Note:* currently only one field is supported at a
   * time.
   * @opt_param int pageSize The maximum number of error issues to return. The
   * service may return fewer than this value. If unspecified, at most 50 error
   * issues will be returned. The maximum value is 1000; values above 1000 will be
   * coerced to 1000.
   * @opt_param string pageToken A page token, received from a previous call.
   * Provide this to retrieve the subsequent page. When paginating, all other
   * parameters provided to the request must match the call that provided the page
   * token.
   * @opt_param int sampleErrorReportLimit Optional. Number of sample error
   * reports to return per ErrorIssue. If unspecified, 0 will be used. *Note:*
   * currently only 0 and 1 are supported.
   * @return GooglePlayDeveloperReportingV1beta1SearchErrorIssuesResponse
   * @throws \Google\Service\Exception
   */
  public function search($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('search', [$params], GooglePlayDeveloperReportingV1beta1SearchErrorIssuesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VitalsErrorsIssues::class, 'Google_Service_Playdeveloperreporting_Resource_VitalsErrorsIssues');
