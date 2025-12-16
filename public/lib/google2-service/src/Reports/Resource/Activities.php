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

namespace Google\Service\Reports\Resource;

use Google\Service\Reports\Activities as ActivitiesModel;
use Google\Service\Reports\Channel;

/**
 * The "activities" collection of methods.
 * Typical usage is:
 *  <code>
 *   $adminService = new Google\Service\Reports(...);
 *   $activities = $adminService->activities;
 *  </code>
 */
class Activities extends \Google\Service\Resource
{
  /**
   * Retrieves a list of activities for a specific customer's account and
   * application such as the Admin console application or the Google Drive
   * application. For more information, see the guides for administrator and
   * Google Drive activity reports. For more information about the activity
   * report's parameters, see the activity parameters reference guides.
   * (activities.listActivities)
   *
   * @param string $userKey Represents the profile ID or the user email for which
   * the data should be filtered. Can be `all` for all information, or `userKey`
   * for a user's unique Google Workspace profile ID or their primary email
   * address. Must not be a deleted user. For a deleted user, call `users.list` in
   * Directory API with `showDeleted=true`, then use the returned `ID` as the
   * `userKey`.
   * @param string $applicationName Application name for which the events are to
   * be retrieved.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string actorIpAddress The Internet Protocol (IP) Address of host
   * where the event was performed. This is an additional way to filter a report's
   * summary using the IP address of the user whose activity is being reported.
   * This IP address may or may not reflect the user's physical location. For
   * example, the IP address can be the user's proxy server's address or a virtual
   * private network (VPN) address. This parameter supports both IPv4 and IPv6
   * address versions.
   * @opt_param string customerId The unique ID of the customer to retrieve data
   * for.
   * @opt_param string endTime Sets the end of the range of time shown in the
   * report. The date is in the RFC 3339 format, for example
   * 2010-10-28T10:26:35.000Z. The default value is the approximate time of the
   * API request. An API report has three basic time concepts: - *Date of the
   * API's request for a report*: When the API created and retrieved the report. -
   * *Report's start time*: The beginning of the timespan shown in the report. The
   * `startTime` must be before the `endTime` (if specified) and the current time
   * when the request is made, or the API returns an error. - *Report's end time*:
   * The end of the timespan shown in the report. For example, the timespan of
   * events summarized in a report can start in April and end in May. The report
   * itself can be requested in August. If the `endTime` is not specified, the
   * report returns all activities from the `startTime` until the current time or
   * the most recent 180 days if the `startTime` is more than 180 days in the
   * past. For Gmail requests, `startTime` and `endTime` must be provided and the
   * difference must not be greater than 30 days.
   * @opt_param string eventName The name of the event being queried by the API.
   * Each `eventName` is related to a specific Google Workspace service or feature
   * which the API organizes into types of events. An example is the Google
   * Calendar events in the Admin console application's reports. The Calendar
   * Settings `type` structure has all of the Calendar `eventName` activities
   * reported by the API. When an administrator changes a Calendar setting, the
   * API reports this activity in the Calendar Settings `type` and `eventName`
   * parameters. For more information about `eventName` query strings and
   * parameters, see the list of event names for various applications above in
   * `applicationName`.
   * @opt_param string filters The `filters` query string is a comma-separated
   * list composed of event parameters manipulated by relational operators. Event
   * parameters are in the form `{parameter1 name}{relational operator}{parameter1
   * value},{parameter2 name}{relational operator}{parameter2 value},...` These
   * event parameters are associated with a specific `eventName`. An empty report
   * is returned if the request's parameter doesn't belong to the `eventName`. For
   * more information about the available `eventName` fields for each application
   * and their associated parameters, go to the
   * [ApplicationName](#applicationname) table, then click through to the Activity
   * Events page in the Appendix for the application you want. In the following
   * Drive activity examples, the returned list consists of all `edit` events
   * where the `doc_id` parameter value matches the conditions defined by the
   * relational operator. In the first example, the request returns all edited
   * documents with a `doc_id` value equal to `12345`. In the second example, the
   * report returns any edited documents where the `doc_id` value is not equal to
   * `98765`. The `<>` operator is URL-encoded in the request's query string
   * (`%3C%3E`): ``` GET...&eventName=edit&filters=doc_id==12345
   * GET...&eventName=edit&filters=doc_id%3C%3E98765 ``` A `filters` query
   * supports these relational operators: * `==`—'equal to'. * `<>`—'not equal
   * to'. Must be URL-encoded (%3C%3E). * `<`—'less than'. Must be URL-encoded
   * (%3C). * `<=`—'less than or equal to'. Must be URL-encoded (%3C=). *
   * `>`—'greater than'. Must be URL-encoded (%3E). * `>=`—'greater than or equal
   * to'. Must be URL-encoded (%3E=). **Note:** The API doesn't accept multiple
   * values of the same parameter. If a parameter is supplied more than once in
   * the API request, the API only accepts the last value of that parameter. In
   * addition, if an invalid parameter is supplied in the API request, the API
   * ignores that parameter and returns the response corresponding to the
   * remaining valid parameters. If no parameters are requested, all parameters
   * are returned.
   * @opt_param string groupIdFilter Comma separated group ids (obfuscated) on
   * which user activities are filtered, i.e. the response will contain activities
   * for only those users that are a part of at least one of the group ids
   * mentioned here. Format: "id:abc123,id:xyz456" *Important:* To filter by
   * groups, you must explicitly add the groups to your filtering groups
   * allowlist. For more information about adding groups to filtering groups
   * allowlist, see [Filter results by Google
   * Group](https://support.google.com/a/answer/11482175)
   * @opt_param int maxResults Determines how many activity records are shown on
   * each response page. For example, if the request sets `maxResults=1` and the
   * report has two activities, the report has two pages. The response's
   * `nextPageToken` property has the token to the second page. The `maxResults`
   * query string is optional in the request. The default value is 1000.
   * @opt_param string orgUnitID ID of the organizational unit to report on.
   * Activity records will be shown only for users who belong to the specified
   * organizational unit. Data before Dec 17, 2018 doesn't appear in the filtered
   * results.
   * @opt_param string pageToken The token to specify next page. A report with
   * multiple pages has a `nextPageToken` property in the response. In your
   * follow-on request getting the next page of the report, enter the
   * `nextPageToken` value in the `pageToken` query string.
   * @opt_param string resourceDetailsFilter Optional. The `resourceDetailsFilter`
   * query string is an AND separated list composed of [Resource
   * Details](#resourcedetails) fields manipulated by relational operators.
   * Resource Details Filters are in the form `{resourceDetails.field1}{relational
   * operator}{field1 value} AND {resourceDetails.field2}{relational
   * operator}{field2 value}...` All the inner fields are traversed using the `.`
   * operator, as shown in the following example: ``` resourceDetails.id =
   * "resourceId" AND resourceDetails.appliedLabels.id = "appliedLabelId" AND
   * resourceDetails.appliedLabels.fieldValue.id = "fieldValueId" ```
   * `resourceDetailsFilter` query supports these relational operators: *
   * `=`—'equal to'. * `!=`—'not equal to'. * `:`—'exists'. This is used for
   * filtering on repeated fields. [`FieldValue`](#fieldvalue) types that are
   * repeated in nature uses `exists` operator for filtering. The following
   * [`FieldValue`](#fieldvalue) types are repeated: *
   * [`TextListValue`](#textlistvalue) *
   * [`SelectionListValue`](#selectionlistvalue) *
   * [`UserListValue`](#userlistvalue) For example, in the following filter,
   * [`SelectionListValue`](#selectionlistvalue), is a repeated field. The filter
   * checks whether [`SelectionListValue`](#selectionlistvalue) contains
   * `selection_id`: ``` resourceDetails.id = "resourceId" AND
   * resourceDetails.appliedLabels.id = "appliedLabelId" AND
   * resourceDetails.appliedLabels.fieldValue.id = "fieldValueId" AND
   * resourceDetails.appliedLabels.fieldValue.type = "SELECTION_LIST_VALUE" AND
   * resourceDetails.appliedLabels.fieldValue.selectionListValue.id: "id" ```
   * **Usage** ``` GET...&resourceDetailsFilter=resourceDetails.id = "resourceId"
   * AND resourceDetails.appliedLabels.id = "appliedLabelId" GET...&resourceDetail
   * sFilter=resourceDetails.id=%22resourceId%22%20AND%20resourceDetails.appliedLa
   * bels.id=%22appliedLabelId%22 ``` **Note the following**: * You must URL
   * encode the query string before sending the request. * The API supports a
   * maximum of 5 fields separated by the AND operator. - When filtering on deeper
   * levels (e.g., [`AppliedLabel`](#appliedlabel), [`FieldValue`](#fieldvalue)),
   * the IDs of all preceding levels in the hierarchy must be included in the
   * filter. For example: Filtering on [`FieldValue`](#fieldvalue) requires
   * [`AppliedLabel`](#appliedlabel) ID and resourceDetails ID to be present.
   * *Sample Query*: ``` resourceDetails.id = "resourceId" AND
   * resourceDetails.appliedLabels.id = "appliedLabelId" AND
   * resourceDetails.appliedLabels.fieldValue.id = "fieldValueId" ``` * Filtering
   * on inner [`FieldValue`](#fieldvalue) types like `longTextValue` and
   * `textValue` requires `resourceDetails.appliedLabels.fieldValue.type` to be
   * present. * Only Filtering on a single [`AppliedLabel`](#appliedlabel) id and
   * [`FieldValue`](#fieldvalue) id is supported.
   * @opt_param string startTime Sets the beginning of the range of time shown in
   * the report. The date is in the RFC 3339 format, for example
   * 2010-10-28T10:26:35.000Z. The report returns all activities from `startTime`
   * until `endTime`. The `startTime` must be before the `endTime` (if specified)
   * and the current time when the request is made, or the API returns an error.
   * For Gmail requests, `startTime` and `endTime` must be provided and the
   * difference must not be greater than 30 days.
   * @return ActivitiesModel
   * @throws \Google\Service\Exception
   */
  public function listActivities($userKey, $applicationName, $optParams = [])
  {
    $params = ['userKey' => $userKey, 'applicationName' => $applicationName];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ActivitiesModel::class);
  }
  /**
   * Start receiving notifications for account activities. For more information,
   * see Receiving Push Notifications. (activities.watch)
   *
   * @param string $userKey Represents the profile ID or the user email for which
   * the data should be filtered. Can be `all` for all information, or `userKey`
   * for a user's unique Google Workspace profile ID or their primary email
   * address. Must not be a deleted user. For a deleted user, call `users.list` in
   * Directory API with `showDeleted=true`, then use the returned `ID` as the
   * `userKey`.
   * @param string $applicationName Application name for which the events are to
   * be retrieved.
   * @param Channel $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string actorIpAddress The Internet Protocol (IP) Address of host
   * where the event was performed. This is an additional way to filter a report's
   * summary using the IP address of the user whose activity is being reported.
   * This IP address may or may not reflect the user's physical location. For
   * example, the IP address can be the user's proxy server's address or a virtual
   * private network (VPN) address. This parameter supports both IPv4 and IPv6
   * address versions.
   * @opt_param string customerId The unique ID of the customer to retrieve data
   * for.
   * @opt_param string endTime Sets the end of the range of time shown in the
   * report. The date is in the RFC 3339 format, for example
   * 2010-10-28T10:26:35.000Z. The default value is the approximate time of the
   * API request. An API report has three basic time concepts: - *Date of the
   * API's request for a report*: When the API created and retrieved the report. -
   * *Report's start time*: The beginning of the timespan shown in the report. The
   * `startTime` must be before the `endTime` (if specified) and the current time
   * when the request is made, or the API returns an error. - *Report's end time*:
   * The end of the timespan shown in the report. For example, the timespan of
   * events summarized in a report can start in April and end in May. The report
   * itself can be requested in August. If the `endTime` is not specified, the
   * report returns all activities from the `startTime` until the current time or
   * the most recent 180 days if the `startTime` is more than 180 days in the
   * past.
   * @opt_param string eventName The name of the event being queried by the API.
   * Each `eventName` is related to a specific Google Workspace service or feature
   * which the API organizes into types of events. An example is the Google
   * Calendar events in the Admin console application's reports. The Calendar
   * Settings `type` structure has all of the Calendar `eventName` activities
   * reported by the API. When an administrator changes a Calendar setting, the
   * API reports this activity in the Calendar Settings `type` and `eventName`
   * parameters. For more information about `eventName` query strings and
   * parameters, see the list of event names for various applications above in
   * `applicationName`.
   * @opt_param string filters The `filters` query string is a comma-separated
   * list composed of event parameters manipulated by relational operators. Event
   * parameters are in the form `{parameter1 name}{relational operator}{parameter1
   * value},{parameter2 name}{relational operator}{parameter2 value},...` These
   * event parameters are associated with a specific `eventName`. An empty report
   * is returned if the request's parameter doesn't belong to the `eventName`. For
   * more information about the available `eventName` fields for each application
   * and their associated parameters, go to the
   * [ApplicationName](#applicationname) table, then click through to the Activity
   * Events page in the Appendix for the application you want. In the following
   * Drive activity examples, the returned list consists of all `edit` events
   * where the `doc_id` parameter value matches the conditions defined by the
   * relational operator. In the first example, the request returns all edited
   * documents with a `doc_id` value equal to `12345`. In the second example, the
   * report returns any edited documents where the `doc_id` value is not equal to
   * `98765`. The `<>` operator is URL-encoded in the request's query string
   * (`%3C%3E`): ``` GET...&eventName=edit&filters=doc_id==12345
   * GET...&eventName=edit&filters=doc_id%3C%3E98765 ``` A `filters` query
   * supports these relational operators: * `==`—'equal to'. * `<>`—'not equal
   * to'. Must be URL-encoded (%3C%3E). * `<`—'less than'. Must be URL-encoded
   * (%3C). * `<=`—'less than or equal to'. Must be URL-encoded (%3C=). *
   * `>`—'greater than'. Must be URL-encoded (%3E). * `>=`—'greater than or equal
   * to'. Must be URL-encoded (%3E=). **Note:** The API doesn't accept multiple
   * values of the same parameter. If a parameter is supplied more than once in
   * the API request, the API only accepts the last value of that parameter. In
   * addition, if an invalid parameter is supplied in the API request, the API
   * ignores that parameter and returns the response corresponding to the
   * remaining valid parameters. If no parameters are requested, all parameters
   * are returned.
   * @opt_param string groupIdFilter `Deprecated`. This field is deprecated and is
   * no longer supported. Comma separated group ids (obfuscated) on which user
   * activities are filtered, i.e. the response will contain activities for only
   * those users that are a part of at least one of the group ids mentioned here.
   * Format: "id:abc123,id:xyz456" *Important:* To filter by groups, you must
   * explicitly add the groups to your filtering groups allowlist. For more
   * information about adding groups to filtering groups allowlist, see [Filter
   * results by Google Group](https://support.google.com/a/answer/11482175)
   * @opt_param int maxResults Determines how many activity records are shown on
   * each response page. For example, if the request sets `maxResults=1` and the
   * report has two activities, the report has two pages. The response's
   * `nextPageToken` property has the token to the second page. The `maxResults`
   * query string is optional in the request. The default value is 1000.
   * @opt_param string orgUnitID `Deprecated`. This field is deprecated and is no
   * longer supported. ID of the organizational unit to report on. Activity
   * records will be shown only for users who belong to the specified
   * organizational unit. Data before Dec 17, 2018 doesn't appear in the filtered
   * results.
   * @opt_param string pageToken The token to specify next page. A report with
   * multiple pages has a `nextPageToken` property in the response. In your
   * follow-on request getting the next page of the report, enter the
   * `nextPageToken` value in the `pageToken` query string.
   * @opt_param string startTime Sets the beginning of the range of time shown in
   * the report. The date is in the RFC 3339 format, for example
   * 2010-10-28T10:26:35.000Z. The report returns all activities from `startTime`
   * until `endTime`. The `startTime` must be before the `endTime` (if specified)
   * and the current time when the request is made, or the API returns an error.
   * @return Channel
   * @throws \Google\Service\Exception
   */
  public function watch($userKey, $applicationName, Channel $postBody, $optParams = [])
  {
    $params = ['userKey' => $userKey, 'applicationName' => $applicationName, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('watch', [$params], Channel::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Activities::class, 'Google_Service_Reports_Resource_Activities');
