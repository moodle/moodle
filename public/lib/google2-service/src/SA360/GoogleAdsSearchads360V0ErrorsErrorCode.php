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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ErrorsErrorCode extends \Google\Model
{
  /**
   * Enum unspecified.
   */
  public const AUTHENTICATION_ERROR_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received error code is not known in this version.
   */
  public const AUTHENTICATION_ERROR_UNKNOWN = 'UNKNOWN';
  /**
   * Authentication of the request failed.
   */
  public const AUTHENTICATION_ERROR_AUTHENTICATION_ERROR = 'AUTHENTICATION_ERROR';
  /**
   * Client customer ID is not a number.
   */
  public const AUTHENTICATION_ERROR_CLIENT_CUSTOMER_ID_INVALID = 'CLIENT_CUSTOMER_ID_INVALID';
  /**
   * No customer found for the provided customer ID.
   */
  public const AUTHENTICATION_ERROR_CUSTOMER_NOT_FOUND = 'CUSTOMER_NOT_FOUND';
  /**
   * Client's Google account is deleted.
   */
  public const AUTHENTICATION_ERROR_GOOGLE_ACCOUNT_DELETED = 'GOOGLE_ACCOUNT_DELETED';
  /**
   * Account login token in the cookie is invalid.
   */
  public const AUTHENTICATION_ERROR_GOOGLE_ACCOUNT_COOKIE_INVALID = 'GOOGLE_ACCOUNT_COOKIE_INVALID';
  /**
   * A problem occurred during Google account authentication.
   */
  public const AUTHENTICATION_ERROR_GOOGLE_ACCOUNT_AUTHENTICATION_FAILED = 'GOOGLE_ACCOUNT_AUTHENTICATION_FAILED';
  /**
   * The user in the Google account login token does not match the user ID in
   * the cookie.
   */
  public const AUTHENTICATION_ERROR_GOOGLE_ACCOUNT_USER_AND_ADS_USER_MISMATCH = 'GOOGLE_ACCOUNT_USER_AND_ADS_USER_MISMATCH';
  /**
   * Login cookie is required for authentication.
   */
  public const AUTHENTICATION_ERROR_LOGIN_COOKIE_REQUIRED = 'LOGIN_COOKIE_REQUIRED';
  /**
   * The Google account that generated the OAuth access token is not associated
   * with a Search Ads 360 account. Create a new account, or add the Google
   * account to an existing Search Ads 360 account.
   */
  public const AUTHENTICATION_ERROR_NOT_ADS_USER = 'NOT_ADS_USER';
  /**
   * OAuth token in the header is not valid.
   */
  public const AUTHENTICATION_ERROR_OAUTH_TOKEN_INVALID = 'OAUTH_TOKEN_INVALID';
  /**
   * OAuth token in the header has expired.
   */
  public const AUTHENTICATION_ERROR_OAUTH_TOKEN_EXPIRED = 'OAUTH_TOKEN_EXPIRED';
  /**
   * OAuth token in the header has been disabled.
   */
  public const AUTHENTICATION_ERROR_OAUTH_TOKEN_DISABLED = 'OAUTH_TOKEN_DISABLED';
  /**
   * OAuth token in the header has been revoked.
   */
  public const AUTHENTICATION_ERROR_OAUTH_TOKEN_REVOKED = 'OAUTH_TOKEN_REVOKED';
  /**
   * OAuth token HTTP header is malformed.
   */
  public const AUTHENTICATION_ERROR_OAUTH_TOKEN_HEADER_INVALID = 'OAUTH_TOKEN_HEADER_INVALID';
  /**
   * Login cookie is not valid.
   */
  public const AUTHENTICATION_ERROR_LOGIN_COOKIE_INVALID = 'LOGIN_COOKIE_INVALID';
  /**
   * User ID in the header is not a valid ID.
   */
  public const AUTHENTICATION_ERROR_USER_ID_INVALID = 'USER_ID_INVALID';
  /**
   * An account administrator changed this account's authentication settings. To
   * access this account, enable 2-Step Verification in your Google account at
   * https://www.google.com/landing/2step.
   */
  public const AUTHENTICATION_ERROR_TWO_STEP_VERIFICATION_NOT_ENROLLED = 'TWO_STEP_VERIFICATION_NOT_ENROLLED';
  /**
   * An account administrator changed this account's authentication settings. To
   * access this account, enable Advanced Protection in your Google account at
   * https://landing.google.com/advancedprotection.
   */
  public const AUTHENTICATION_ERROR_ADVANCED_PROTECTION_NOT_ENROLLED = 'ADVANCED_PROTECTION_NOT_ENROLLED';
  /**
   * Enum unspecified.
   */
  public const AUTHORIZATION_ERROR_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received error code is not known in this version.
   */
  public const AUTHORIZATION_ERROR_UNKNOWN = 'UNKNOWN';
  /**
   * User doesn't have permission to access customer. Note: If you're accessing
   * a client customer, the manager's customer ID must be set in the `login-
   * customer-id` header. Learn more at https://developers.google.com/search-
   * ads/reporting/concepts/call-structure#login_customer_id_header
   */
  public const AUTHORIZATION_ERROR_USER_PERMISSION_DENIED = 'USER_PERMISSION_DENIED';
  /**
   * The Google Cloud project sent in the request does not have permission to
   * access the api.
   */
  public const AUTHORIZATION_ERROR_PROJECT_DISABLED = 'PROJECT_DISABLED';
  /**
   * Authorization of the client failed.
   */
  public const AUTHORIZATION_ERROR_AUTHORIZATION_ERROR = 'AUTHORIZATION_ERROR';
  /**
   * The user does not have permission to perform this action (for example, ADD,
   * UPDATE, REMOVE) on the resource or call a method.
   */
  public const AUTHORIZATION_ERROR_ACTION_NOT_PERMITTED = 'ACTION_NOT_PERMITTED';
  /**
   * Signup not complete.
   */
  public const AUTHORIZATION_ERROR_INCOMPLETE_SIGNUP = 'INCOMPLETE_SIGNUP';
  /**
   * The customer account can't be accessed because it is not yet enabled or has
   * been deactivated.
   */
  public const AUTHORIZATION_ERROR_CUSTOMER_NOT_ENABLED = 'CUSTOMER_NOT_ENABLED';
  /**
   * The developer must sign the terms of service. They can be found here:
   * https://developers.google.com/terms
   */
  public const AUTHORIZATION_ERROR_MISSING_TOS = 'MISSING_TOS';
  /**
   * The login customer specified does not have access to the account specified,
   * so the request is invalid.
   */
  public const AUTHORIZATION_ERROR_INVALID_LOGIN_CUSTOMER_ID_SERVING_CUSTOMER_ID_COMBINATION = 'INVALID_LOGIN_CUSTOMER_ID_SERVING_CUSTOMER_ID_COMBINATION';
  /**
   * The developer specified does not have access to the service.
   */
  public const AUTHORIZATION_ERROR_SERVICE_ACCESS_DENIED = 'SERVICE_ACCESS_DENIED';
  /**
   * The customer (or login customer) isn't allowed in Search Ads 360 API. It
   * belongs to another ads system.
   */
  public const AUTHORIZATION_ERROR_ACCESS_DENIED_FOR_ACCOUNT_TYPE = 'ACCESS_DENIED_FOR_ACCOUNT_TYPE';
  /**
   * The developer does not have access to the metrics queried.
   */
  public const AUTHORIZATION_ERROR_METRIC_ACCESS_DENIED = 'METRIC_ACCESS_DENIED';
  /**
   * Enum unspecified.
   */
  public const CONVERSION_CUSTOM_VARIABLE_ERROR_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received error code is not known in this version.
   */
  public const CONVERSION_CUSTOM_VARIABLE_ERROR_UNKNOWN = 'UNKNOWN';
  /**
   * A conversion custom variable with the specified name already exists.
   */
  public const CONVERSION_CUSTOM_VARIABLE_ERROR_DUPLICATE_NAME = 'DUPLICATE_NAME';
  /**
   * A conversion custom variable with the specified tag already exists.
   */
  public const CONVERSION_CUSTOM_VARIABLE_ERROR_DUPLICATE_TAG = 'DUPLICATE_TAG';
  /**
   * A conversion custom variable with the specified tag is reserved for other
   * uses.
   */
  public const CONVERSION_CUSTOM_VARIABLE_ERROR_RESERVED_TAG = 'RESERVED_TAG';
  /**
   * The conversion custom variable is not found.
   */
  public const CONVERSION_CUSTOM_VARIABLE_ERROR_NOT_FOUND = 'NOT_FOUND';
  /**
   * The conversion custom variable is not available for use.
   */
  public const CONVERSION_CUSTOM_VARIABLE_ERROR_NOT_AVAILABLE = 'NOT_AVAILABLE';
  /**
   * The conversion custom variable requested is incompatible with the current
   * request.
   */
  public const CONVERSION_CUSTOM_VARIABLE_ERROR_INCOMPATIBLE_TYPE = 'INCOMPATIBLE_TYPE';
  /**
   * The conversion custom variable requested is not of type METRIC.
   */
  public const CONVERSION_CUSTOM_VARIABLE_ERROR_INVALID_METRIC = 'INVALID_METRIC';
  /**
   * The conversion custom variable's cardinality exceeds the segmentation
   * limit.
   */
  public const CONVERSION_CUSTOM_VARIABLE_ERROR_EXCEEDS_CARDINALITY_LIMIT = 'EXCEEDS_CARDINALITY_LIMIT';
  /**
   * The conversion custom variable requested is not of type DIMENSION.
   */
  public const CONVERSION_CUSTOM_VARIABLE_ERROR_INVALID_DIMENSION = 'INVALID_DIMENSION';
  /**
   * The conversion custom variable requested is incompatible with the selected
   * resource.
   */
  public const CONVERSION_CUSTOM_VARIABLE_ERROR_INCOMPATIBLE_WITH_SELECTED_RESOURCE = 'INCOMPATIBLE_WITH_SELECTED_RESOURCE';
  /**
   * Enum unspecified.
   */
  public const CUSTOM_COLUMN_ERROR_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received error code is not known in this version.
   */
  public const CUSTOM_COLUMN_ERROR_UNKNOWN = 'UNKNOWN';
  /**
   * The custom column has not been found.
   */
  public const CUSTOM_COLUMN_ERROR_CUSTOM_COLUMN_NOT_FOUND = 'CUSTOM_COLUMN_NOT_FOUND';
  /**
   * The custom column is not available.
   */
  public const CUSTOM_COLUMN_ERROR_CUSTOM_COLUMN_NOT_AVAILABLE = 'CUSTOM_COLUMN_NOT_AVAILABLE';
  /**
   * Enum unspecified.
   */
  public const DATE_ERROR_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received error code is not known in this version.
   */
  public const DATE_ERROR_UNKNOWN = 'UNKNOWN';
  /**
   * Given field values do not correspond to a valid date.
   */
  public const DATE_ERROR_INVALID_FIELD_VALUES_IN_DATE = 'INVALID_FIELD_VALUES_IN_DATE';
  /**
   * Given field values do not correspond to a valid date time.
   */
  public const DATE_ERROR_INVALID_FIELD_VALUES_IN_DATE_TIME = 'INVALID_FIELD_VALUES_IN_DATE_TIME';
  /**
   * The string date's format should be yyyy-mm-dd.
   */
  public const DATE_ERROR_INVALID_STRING_DATE = 'INVALID_STRING_DATE';
  /**
   * The string date time's format should be yyyy-mm-dd hh:mm:ss.ssssss.
   */
  public const DATE_ERROR_INVALID_STRING_DATE_TIME_MICROS = 'INVALID_STRING_DATE_TIME_MICROS';
  /**
   * The string date time's format should be yyyy-mm-dd hh:mm:ss.
   */
  public const DATE_ERROR_INVALID_STRING_DATE_TIME_SECONDS = 'INVALID_STRING_DATE_TIME_SECONDS';
  /**
   * The string date time's format should be yyyy-mm-dd hh:mm:ss+|-hh:mm.
   */
  public const DATE_ERROR_INVALID_STRING_DATE_TIME_SECONDS_WITH_OFFSET = 'INVALID_STRING_DATE_TIME_SECONDS_WITH_OFFSET';
  /**
   * Date is before allowed minimum.
   */
  public const DATE_ERROR_EARLIER_THAN_MINIMUM_DATE = 'EARLIER_THAN_MINIMUM_DATE';
  /**
   * Date is after allowed maximum.
   */
  public const DATE_ERROR_LATER_THAN_MAXIMUM_DATE = 'LATER_THAN_MAXIMUM_DATE';
  /**
   * Date range bounds are not in order.
   */
  public const DATE_ERROR_DATE_RANGE_MINIMUM_DATE_LATER_THAN_MAXIMUM_DATE = 'DATE_RANGE_MINIMUM_DATE_LATER_THAN_MAXIMUM_DATE';
  /**
   * Both dates in range are null.
   */
  public const DATE_ERROR_DATE_RANGE_MINIMUM_AND_MAXIMUM_DATES_BOTH_NULL = 'DATE_RANGE_MINIMUM_AND_MAXIMUM_DATES_BOTH_NULL';
  /**
   * Enum unspecified.
   */
  public const DATE_RANGE_ERROR_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received error code is not known in this version.
   */
  public const DATE_RANGE_ERROR_UNKNOWN = 'UNKNOWN';
  /**
   * Invalid date.
   */
  public const DATE_RANGE_ERROR_INVALID_DATE = 'INVALID_DATE';
  /**
   * The start date was after the end date.
   */
  public const DATE_RANGE_ERROR_START_DATE_AFTER_END_DATE = 'START_DATE_AFTER_END_DATE';
  /**
   * Cannot set date to past time
   */
  public const DATE_RANGE_ERROR_CANNOT_SET_DATE_TO_PAST = 'CANNOT_SET_DATE_TO_PAST';
  /**
   * A date was used that is past the system "last" date.
   */
  public const DATE_RANGE_ERROR_AFTER_MAXIMUM_ALLOWABLE_DATE = 'AFTER_MAXIMUM_ALLOWABLE_DATE';
  /**
   * Trying to change start date on a resource that has started.
   */
  public const DATE_RANGE_ERROR_CANNOT_MODIFY_START_DATE_IF_ALREADY_STARTED = 'CANNOT_MODIFY_START_DATE_IF_ALREADY_STARTED';
  /**
   * Enum unspecified.
   */
  public const DISTINCT_ERROR_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received error code is not known in this version.
   */
  public const DISTINCT_ERROR_UNKNOWN = 'UNKNOWN';
  /**
   * Duplicate element.
   */
  public const DISTINCT_ERROR_DUPLICATE_ELEMENT = 'DUPLICATE_ELEMENT';
  /**
   * Duplicate type.
   */
  public const DISTINCT_ERROR_DUPLICATE_TYPE = 'DUPLICATE_TYPE';
  /**
   * Enum unspecified.
   */
  public const HEADER_ERROR_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received error code is not known in this version.
   */
  public const HEADER_ERROR_UNKNOWN = 'UNKNOWN';
  /**
   * The user selected customer ID could not be validated.
   */
  public const HEADER_ERROR_INVALID_USER_SELECTED_CUSTOMER_ID = 'INVALID_USER_SELECTED_CUSTOMER_ID';
  /**
   * The login customer ID could not be validated.
   */
  public const HEADER_ERROR_INVALID_LOGIN_CUSTOMER_ID = 'INVALID_LOGIN_CUSTOMER_ID';
  /**
   * Enum unspecified.
   */
  public const INTERNAL_ERROR_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received error code is not known in this version.
   */
  public const INTERNAL_ERROR_UNKNOWN = 'UNKNOWN';
  /**
   * API encountered unexpected internal error.
   */
  public const INTERNAL_ERROR_INTERNAL_ERROR = 'INTERNAL_ERROR';
  /**
   * The intended error code doesn't exist in specified API version. It will be
   * released in a future API version.
   */
  public const INTERNAL_ERROR_ERROR_CODE_NOT_PUBLISHED = 'ERROR_CODE_NOT_PUBLISHED';
  /**
   * API encountered an unexpected transient error. The user should retry their
   * request in these cases.
   */
  public const INTERNAL_ERROR_TRANSIENT_ERROR = 'TRANSIENT_ERROR';
  /**
   * The request took longer than a deadline.
   */
  public const INTERNAL_ERROR_DEADLINE_EXCEEDED = 'DEADLINE_EXCEEDED';
  /**
   * Enum unspecified.
   */
  public const INVALID_PARAMETER_ERROR_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received error code is not known in this version.
   */
  public const INVALID_PARAMETER_ERROR_UNKNOWN = 'UNKNOWN';
  /**
   * The specified currency code is invalid.
   */
  public const INVALID_PARAMETER_ERROR_INVALID_CURRENCY_CODE = 'INVALID_CURRENCY_CODE';
  /**
   * Name unspecified.
   */
  public const QUERY_ERROR_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received error code is not known in this version.
   */
  public const QUERY_ERROR_UNKNOWN = 'UNKNOWN';
  /**
   * Returned if all other query error reasons are not applicable.
   */
  public const QUERY_ERROR_QUERY_ERROR = 'QUERY_ERROR';
  /**
   * A condition used in the query references an invalid enum constant.
   */
  public const QUERY_ERROR_BAD_ENUM_CONSTANT = 'BAD_ENUM_CONSTANT';
  /**
   * Query contains an invalid escape sequence.
   */
  public const QUERY_ERROR_BAD_ESCAPE_SEQUENCE = 'BAD_ESCAPE_SEQUENCE';
  /**
   * Field name is invalid.
   */
  public const QUERY_ERROR_BAD_FIELD_NAME = 'BAD_FIELD_NAME';
  /**
   * Limit value is invalid (for example, not a number)
   */
  public const QUERY_ERROR_BAD_LIMIT_VALUE = 'BAD_LIMIT_VALUE';
  /**
   * Encountered number can not be parsed.
   */
  public const QUERY_ERROR_BAD_NUMBER = 'BAD_NUMBER';
  /**
   * Invalid operator encountered.
   */
  public const QUERY_ERROR_BAD_OPERATOR = 'BAD_OPERATOR';
  /**
   * Parameter unknown or not supported.
   */
  public const QUERY_ERROR_BAD_PARAMETER_NAME = 'BAD_PARAMETER_NAME';
  /**
   * Parameter have invalid value.
   */
  public const QUERY_ERROR_BAD_PARAMETER_VALUE = 'BAD_PARAMETER_VALUE';
  /**
   * Invalid resource type was specified in the FROM clause.
   */
  public const QUERY_ERROR_BAD_RESOURCE_TYPE_IN_FROM_CLAUSE = 'BAD_RESOURCE_TYPE_IN_FROM_CLAUSE';
  /**
   * Non-ASCII symbol encountered outside of strings.
   */
  public const QUERY_ERROR_BAD_SYMBOL = 'BAD_SYMBOL';
  /**
   * Value is invalid.
   */
  public const QUERY_ERROR_BAD_VALUE = 'BAD_VALUE';
  /**
   * Date filters fail to restrict date to a range smaller than 31 days.
   * Applicable if the query is segmented by date.
   */
  public const QUERY_ERROR_DATE_RANGE_TOO_WIDE = 'DATE_RANGE_TOO_WIDE';
  /**
   * Filters on date/week/month/quarter have a start date after end date.
   */
  public const QUERY_ERROR_DATE_RANGE_TOO_NARROW = 'DATE_RANGE_TOO_NARROW';
  /**
   * Expected AND between values with BETWEEN operator.
   */
  public const QUERY_ERROR_EXPECTED_AND = 'EXPECTED_AND';
  /**
   * Expecting ORDER BY to have BY.
   */
  public const QUERY_ERROR_EXPECTED_BY = 'EXPECTED_BY';
  /**
   * There was no dimension field selected.
   */
  public const QUERY_ERROR_EXPECTED_DIMENSION_FIELD_IN_SELECT_CLAUSE = 'EXPECTED_DIMENSION_FIELD_IN_SELECT_CLAUSE';
  /**
   * Missing filters on date related fields.
   */
  public const QUERY_ERROR_EXPECTED_FILTERS_ON_DATE_RANGE = 'EXPECTED_FILTERS_ON_DATE_RANGE';
  /**
   * Missing FROM clause.
   */
  public const QUERY_ERROR_EXPECTED_FROM = 'EXPECTED_FROM';
  /**
   * The operator used in the conditions requires the value to be a list.
   */
  public const QUERY_ERROR_EXPECTED_LIST = 'EXPECTED_LIST';
  /**
   * Fields used in WHERE or ORDER BY clauses are missing from the SELECT
   * clause.
   */
  public const QUERY_ERROR_EXPECTED_REFERENCED_FIELD_IN_SELECT_CLAUSE = 'EXPECTED_REFERENCED_FIELD_IN_SELECT_CLAUSE';
  /**
   * SELECT is missing at the beginning of query.
   */
  public const QUERY_ERROR_EXPECTED_SELECT = 'EXPECTED_SELECT';
  /**
   * A list was passed as a value to a condition whose operator expects a single
   * value.
   */
  public const QUERY_ERROR_EXPECTED_SINGLE_VALUE = 'EXPECTED_SINGLE_VALUE';
  /**
   * Missing one or both values with BETWEEN operator.
   */
  public const QUERY_ERROR_EXPECTED_VALUE_WITH_BETWEEN_OPERATOR = 'EXPECTED_VALUE_WITH_BETWEEN_OPERATOR';
  /**
   * Invalid date format. Expected 'YYYY-MM-DD'.
   */
  public const QUERY_ERROR_INVALID_DATE_FORMAT = 'INVALID_DATE_FORMAT';
  /**
   * Misaligned date value for the filter. The date should be the start of a
   * week/month/quarter if the filtered field is
   * segments.week/segments.month/segments.quarter.
   */
  public const QUERY_ERROR_MISALIGNED_DATE_FOR_FILTER = 'MISALIGNED_DATE_FOR_FILTER';
  /**
   * Value passed was not a string when it should have been. For example, it was
   * a number or unquoted literal.
   */
  public const QUERY_ERROR_INVALID_STRING_VALUE = 'INVALID_STRING_VALUE';
  /**
   * A String value passed to the BETWEEN operator does not parse as a date.
   */
  public const QUERY_ERROR_INVALID_VALUE_WITH_BETWEEN_OPERATOR = 'INVALID_VALUE_WITH_BETWEEN_OPERATOR';
  /**
   * The value passed to the DURING operator is not a Date range literal
   */
  public const QUERY_ERROR_INVALID_VALUE_WITH_DURING_OPERATOR = 'INVALID_VALUE_WITH_DURING_OPERATOR';
  /**
   * A value was passed to the LIKE operator.
   */
  public const QUERY_ERROR_INVALID_VALUE_WITH_LIKE_OPERATOR = 'INVALID_VALUE_WITH_LIKE_OPERATOR';
  /**
   * An operator was provided that is inapplicable to the field being filtered.
   */
  public const QUERY_ERROR_OPERATOR_FIELD_MISMATCH = 'OPERATOR_FIELD_MISMATCH';
  /**
   * A Condition was found with an empty list.
   */
  public const QUERY_ERROR_PROHIBITED_EMPTY_LIST_IN_CONDITION = 'PROHIBITED_EMPTY_LIST_IN_CONDITION';
  /**
   * A condition used in the query references an unsupported enum constant.
   */
  public const QUERY_ERROR_PROHIBITED_ENUM_CONSTANT = 'PROHIBITED_ENUM_CONSTANT';
  /**
   * Fields that are not allowed to be selected together were included in the
   * SELECT clause.
   */
  public const QUERY_ERROR_PROHIBITED_FIELD_COMBINATION_IN_SELECT_CLAUSE = 'PROHIBITED_FIELD_COMBINATION_IN_SELECT_CLAUSE';
  /**
   * A field that is not orderable was included in the ORDER BY clause.
   */
  public const QUERY_ERROR_PROHIBITED_FIELD_IN_ORDER_BY_CLAUSE = 'PROHIBITED_FIELD_IN_ORDER_BY_CLAUSE';
  /**
   * A field that is not selectable was included in the SELECT clause.
   */
  public const QUERY_ERROR_PROHIBITED_FIELD_IN_SELECT_CLAUSE = 'PROHIBITED_FIELD_IN_SELECT_CLAUSE';
  /**
   * A field that is not filterable was included in the WHERE clause.
   */
  public const QUERY_ERROR_PROHIBITED_FIELD_IN_WHERE_CLAUSE = 'PROHIBITED_FIELD_IN_WHERE_CLAUSE';
  /**
   * Resource type specified in the FROM clause is not supported by this
   * service.
   */
  public const QUERY_ERROR_PROHIBITED_RESOURCE_TYPE_IN_FROM_CLAUSE = 'PROHIBITED_RESOURCE_TYPE_IN_FROM_CLAUSE';
  /**
   * A field that comes from an incompatible resource was included in the SELECT
   * clause.
   */
  public const QUERY_ERROR_PROHIBITED_RESOURCE_TYPE_IN_SELECT_CLAUSE = 'PROHIBITED_RESOURCE_TYPE_IN_SELECT_CLAUSE';
  /**
   * A field that comes from an incompatible resource was included in the WHERE
   * clause.
   */
  public const QUERY_ERROR_PROHIBITED_RESOURCE_TYPE_IN_WHERE_CLAUSE = 'PROHIBITED_RESOURCE_TYPE_IN_WHERE_CLAUSE';
  /**
   * A metric incompatible with the main resource or other selected segmenting
   * resources was included in the SELECT or WHERE clause.
   */
  public const QUERY_ERROR_PROHIBITED_METRIC_IN_SELECT_OR_WHERE_CLAUSE = 'PROHIBITED_METRIC_IN_SELECT_OR_WHERE_CLAUSE';
  /**
   * A segment incompatible with the main resource or other selected segmenting
   * resources was included in the SELECT or WHERE clause.
   */
  public const QUERY_ERROR_PROHIBITED_SEGMENT_IN_SELECT_OR_WHERE_CLAUSE = 'PROHIBITED_SEGMENT_IN_SELECT_OR_WHERE_CLAUSE';
  /**
   * A segment in the SELECT clause is incompatible with a metric in the SELECT
   * or WHERE clause.
   */
  public const QUERY_ERROR_PROHIBITED_SEGMENT_WITH_METRIC_IN_SELECT_OR_WHERE_CLAUSE = 'PROHIBITED_SEGMENT_WITH_METRIC_IN_SELECT_OR_WHERE_CLAUSE';
  /**
   * The value passed to the limit clause is too low.
   */
  public const QUERY_ERROR_LIMIT_VALUE_TOO_LOW = 'LIMIT_VALUE_TOO_LOW';
  /**
   * Query has a string containing a newline character.
   */
  public const QUERY_ERROR_PROHIBITED_NEWLINE_IN_STRING = 'PROHIBITED_NEWLINE_IN_STRING';
  /**
   * List contains values of different types.
   */
  public const QUERY_ERROR_PROHIBITED_VALUE_COMBINATION_IN_LIST = 'PROHIBITED_VALUE_COMBINATION_IN_LIST';
  /**
   * The values passed to the BETWEEN operator are not of the same type.
   */
  public const QUERY_ERROR_PROHIBITED_VALUE_COMBINATION_WITH_BETWEEN_OPERATOR = 'PROHIBITED_VALUE_COMBINATION_WITH_BETWEEN_OPERATOR';
  /**
   * Query contains unterminated string.
   */
  public const QUERY_ERROR_STRING_NOT_TERMINATED = 'STRING_NOT_TERMINATED';
  /**
   * Too many segments are specified in SELECT clause.
   */
  public const QUERY_ERROR_TOO_MANY_SEGMENTS = 'TOO_MANY_SEGMENTS';
  /**
   * Query is incomplete and cannot be parsed.
   */
  public const QUERY_ERROR_UNEXPECTED_END_OF_QUERY = 'UNEXPECTED_END_OF_QUERY';
  /**
   * FROM clause cannot be specified in this query.
   */
  public const QUERY_ERROR_UNEXPECTED_FROM_CLAUSE = 'UNEXPECTED_FROM_CLAUSE';
  /**
   * Query contains one or more unrecognized fields.
   */
  public const QUERY_ERROR_UNRECOGNIZED_FIELD = 'UNRECOGNIZED_FIELD';
  /**
   * Query has an unexpected extra part.
   */
  public const QUERY_ERROR_UNEXPECTED_INPUT = 'UNEXPECTED_INPUT';
  /**
   * Metrics cannot be requested for a manager account. To retrieve metrics,
   * issue separate requests against each client account under the manager
   * account.
   */
  public const QUERY_ERROR_REQUESTED_METRICS_FOR_MANAGER = 'REQUESTED_METRICS_FOR_MANAGER';
  /**
   * The number of values (right-hand-side operands) in a filter exceeds the
   * limit.
   */
  public const QUERY_ERROR_FILTER_HAS_TOO_MANY_VALUES = 'FILTER_HAS_TOO_MANY_VALUES';
  /**
   * Enum unspecified.
   */
  public const QUOTA_ERROR_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received error code is not known in this version.
   */
  public const QUOTA_ERROR_UNKNOWN = 'UNKNOWN';
  /**
   * Too many requests.
   */
  public const QUOTA_ERROR_RESOURCE_EXHAUSTED = 'RESOURCE_EXHAUSTED';
  /**
   * Too many requests in a short amount of time.
   */
  public const QUOTA_ERROR_RESOURCE_TEMPORARILY_EXHAUSTED = 'RESOURCE_TEMPORARILY_EXHAUSTED';
  /**
   * Enum unspecified.
   */
  public const REQUEST_ERROR_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received error code is not known in this version.
   */
  public const REQUEST_ERROR_UNKNOWN = 'UNKNOWN';
  /**
   * Resource name is required for this request.
   */
  public const REQUEST_ERROR_RESOURCE_NAME_MISSING = 'RESOURCE_NAME_MISSING';
  /**
   * Resource name provided is malformed.
   */
  public const REQUEST_ERROR_RESOURCE_NAME_MALFORMED = 'RESOURCE_NAME_MALFORMED';
  /**
   * Resource name provided is malformed.
   */
  public const REQUEST_ERROR_BAD_RESOURCE_ID = 'BAD_RESOURCE_ID';
  /**
   * Product name is invalid.
   */
  public const REQUEST_ERROR_INVALID_PRODUCT_NAME = 'INVALID_PRODUCT_NAME';
  /**
   * Customer ID is invalid.
   */
  public const REQUEST_ERROR_INVALID_CUSTOMER_ID = 'INVALID_CUSTOMER_ID';
  /**
   * Mutate operation should have either create, update, or remove specified.
   */
  public const REQUEST_ERROR_OPERATION_REQUIRED = 'OPERATION_REQUIRED';
  /**
   * Requested resource not found.
   */
  public const REQUEST_ERROR_RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
  /**
   * Next page token specified in user request is invalid.
   */
  public const REQUEST_ERROR_INVALID_PAGE_TOKEN = 'INVALID_PAGE_TOKEN';
  /**
   * Next page token specified in user request has expired.
   */
  public const REQUEST_ERROR_EXPIRED_PAGE_TOKEN = 'EXPIRED_PAGE_TOKEN';
  /**
   * Page size specified in user request is invalid.
   */
  public const REQUEST_ERROR_INVALID_PAGE_SIZE = 'INVALID_PAGE_SIZE';
  /**
   * Required field is missing.
   */
  public const REQUEST_ERROR_REQUIRED_FIELD_MISSING = 'REQUIRED_FIELD_MISSING';
  /**
   * The field cannot be modified because it's immutable. It's also possible
   * that the field can be modified using 'create' operation but not 'update'.
   */
  public const REQUEST_ERROR_IMMUTABLE_FIELD = 'IMMUTABLE_FIELD';
  /**
   * Received too many entries in request.
   */
  public const REQUEST_ERROR_TOO_MANY_MUTATE_OPERATIONS = 'TOO_MANY_MUTATE_OPERATIONS';
  /**
   * Request cannot be executed by a manager account.
   */
  public const REQUEST_ERROR_CANNOT_BE_EXECUTED_BY_MANAGER_ACCOUNT = 'CANNOT_BE_EXECUTED_BY_MANAGER_ACCOUNT';
  /**
   * Mutate request was attempting to modify a readonly field. For instance,
   * Budget fields can be requested for Ad Group, but are read-only for
   * adGroups:mutate.
   */
  public const REQUEST_ERROR_CANNOT_MODIFY_FOREIGN_FIELD = 'CANNOT_MODIFY_FOREIGN_FIELD';
  /**
   * Enum value is not permitted.
   */
  public const REQUEST_ERROR_INVALID_ENUM_VALUE = 'INVALID_ENUM_VALUE';
  /**
   * The login-customer-id parameter is required for this request.
   */
  public const REQUEST_ERROR_LOGIN_CUSTOMER_ID_PARAMETER_MISSING = 'LOGIN_CUSTOMER_ID_PARAMETER_MISSING';
  /**
   * Either login-customer-id or linked-customer-id parameter is required for
   * this request.
   */
  public const REQUEST_ERROR_LOGIN_OR_LINKED_CUSTOMER_ID_PARAMETER_REQUIRED = 'LOGIN_OR_LINKED_CUSTOMER_ID_PARAMETER_REQUIRED';
  /**
   * page_token is set in the validate only request
   */
  public const REQUEST_ERROR_VALIDATE_ONLY_REQUEST_HAS_PAGE_TOKEN = 'VALIDATE_ONLY_REQUEST_HAS_PAGE_TOKEN';
  /**
   * return_summary_row cannot be enabled if request did not select any metrics
   * field.
   */
  public const REQUEST_ERROR_CANNOT_RETURN_SUMMARY_ROW_FOR_REQUEST_WITHOUT_METRICS = 'CANNOT_RETURN_SUMMARY_ROW_FOR_REQUEST_WITHOUT_METRICS';
  /**
   * return_summary_row should not be enabled for validate only requests.
   */
  public const REQUEST_ERROR_CANNOT_RETURN_SUMMARY_ROW_FOR_VALIDATE_ONLY_REQUESTS = 'CANNOT_RETURN_SUMMARY_ROW_FOR_VALIDATE_ONLY_REQUESTS';
  /**
   * return_summary_row parameter value should be the same between requests with
   * page_token field set and their original request.
   */
  public const REQUEST_ERROR_INCONSISTENT_RETURN_SUMMARY_ROW_VALUE = 'INCONSISTENT_RETURN_SUMMARY_ROW_VALUE';
  /**
   * The total results count cannot be returned if it was not requested in the
   * original request.
   */
  public const REQUEST_ERROR_TOTAL_RESULTS_COUNT_NOT_ORIGINALLY_REQUESTED = 'TOTAL_RESULTS_COUNT_NOT_ORIGINALLY_REQUESTED';
  /**
   * Deadline specified by the client was too short.
   */
  public const REQUEST_ERROR_RPC_DEADLINE_TOO_SHORT = 'RPC_DEADLINE_TOO_SHORT';
  /**
   * The product associated with the request is not supported for the current
   * request.
   */
  public const REQUEST_ERROR_PRODUCT_NOT_SUPPORTED = 'PRODUCT_NOT_SUPPORTED';
  /**
   * Enum unspecified.
   */
  public const SIZE_LIMIT_ERROR_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received error code is not known in this version.
   */
  public const SIZE_LIMIT_ERROR_UNKNOWN = 'UNKNOWN';
  /**
   * The number of entries in the request exceeds the system limit, or the
   * contents of the operations exceed transaction limits due to their size or
   * complexity. Try reducing the number of entries per request.
   */
  public const SIZE_LIMIT_ERROR_REQUEST_SIZE_LIMIT_EXCEEDED = 'REQUEST_SIZE_LIMIT_EXCEEDED';
  /**
   * The number of entries in the response exceeds the system limit.
   */
  public const SIZE_LIMIT_ERROR_RESPONSE_SIZE_LIMIT_EXCEEDED = 'RESPONSE_SIZE_LIMIT_EXCEEDED';
  /**
   * Indicates failure to properly authenticate user.
   *
   * @var string
   */
  public $authenticationError;
  /**
   * An error encountered when trying to authorize a user.
   *
   * @var string
   */
  public $authorizationError;
  /**
   * The reasons for the conversion custom variable error
   *
   * @var string
   */
  public $conversionCustomVariableError;
  /**
   * The reasons for the custom column error
   *
   * @var string
   */
  public $customColumnError;
  /**
   * The reasons for the date error
   *
   * @var string
   */
  public $dateError;
  /**
   * The reasons for the date range error
   *
   * @var string
   */
  public $dateRangeError;
  /**
   * The reasons for the distinct error
   *
   * @var string
   */
  public $distinctError;
  /**
   * The reasons for the header error.
   *
   * @var string
   */
  public $headerError;
  /**
   * An unexpected server-side error.
   *
   * @var string
   */
  public $internalError;
  /**
   * The reasons for invalid parameter errors.
   *
   * @var string
   */
  public $invalidParameterError;
  /**
   * An error with the query
   *
   * @var string
   */
  public $queryError;
  /**
   * An error with the amount of quota remaining.
   *
   * @var string
   */
  public $quotaError;
  /**
   * An error caused by the request
   *
   * @var string
   */
  public $requestError;
  /**
   * The reasons for the size limit error
   *
   * @var string
   */
  public $sizeLimitError;

  /**
   * Indicates failure to properly authenticate user.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, AUTHENTICATION_ERROR,
   * CLIENT_CUSTOMER_ID_INVALID, CUSTOMER_NOT_FOUND, GOOGLE_ACCOUNT_DELETED,
   * GOOGLE_ACCOUNT_COOKIE_INVALID, GOOGLE_ACCOUNT_AUTHENTICATION_FAILED,
   * GOOGLE_ACCOUNT_USER_AND_ADS_USER_MISMATCH, LOGIN_COOKIE_REQUIRED,
   * NOT_ADS_USER, OAUTH_TOKEN_INVALID, OAUTH_TOKEN_EXPIRED,
   * OAUTH_TOKEN_DISABLED, OAUTH_TOKEN_REVOKED, OAUTH_TOKEN_HEADER_INVALID,
   * LOGIN_COOKIE_INVALID, USER_ID_INVALID, TWO_STEP_VERIFICATION_NOT_ENROLLED,
   * ADVANCED_PROTECTION_NOT_ENROLLED
   *
   * @param self::AUTHENTICATION_ERROR_* $authenticationError
   */
  public function setAuthenticationError($authenticationError)
  {
    $this->authenticationError = $authenticationError;
  }
  /**
   * @return self::AUTHENTICATION_ERROR_*
   */
  public function getAuthenticationError()
  {
    return $this->authenticationError;
  }
  /**
   * An error encountered when trying to authorize a user.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, USER_PERMISSION_DENIED,
   * PROJECT_DISABLED, AUTHORIZATION_ERROR, ACTION_NOT_PERMITTED,
   * INCOMPLETE_SIGNUP, CUSTOMER_NOT_ENABLED, MISSING_TOS,
   * INVALID_LOGIN_CUSTOMER_ID_SERVING_CUSTOMER_ID_COMBINATION,
   * SERVICE_ACCESS_DENIED, ACCESS_DENIED_FOR_ACCOUNT_TYPE, METRIC_ACCESS_DENIED
   *
   * @param self::AUTHORIZATION_ERROR_* $authorizationError
   */
  public function setAuthorizationError($authorizationError)
  {
    $this->authorizationError = $authorizationError;
  }
  /**
   * @return self::AUTHORIZATION_ERROR_*
   */
  public function getAuthorizationError()
  {
    return $this->authorizationError;
  }
  /**
   * The reasons for the conversion custom variable error
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, DUPLICATE_NAME, DUPLICATE_TAG,
   * RESERVED_TAG, NOT_FOUND, NOT_AVAILABLE, INCOMPATIBLE_TYPE, INVALID_METRIC,
   * EXCEEDS_CARDINALITY_LIMIT, INVALID_DIMENSION,
   * INCOMPATIBLE_WITH_SELECTED_RESOURCE
   *
   * @param self::CONVERSION_CUSTOM_VARIABLE_ERROR_* $conversionCustomVariableError
   */
  public function setConversionCustomVariableError($conversionCustomVariableError)
  {
    $this->conversionCustomVariableError = $conversionCustomVariableError;
  }
  /**
   * @return self::CONVERSION_CUSTOM_VARIABLE_ERROR_*
   */
  public function getConversionCustomVariableError()
  {
    return $this->conversionCustomVariableError;
  }
  /**
   * The reasons for the custom column error
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, CUSTOM_COLUMN_NOT_FOUND,
   * CUSTOM_COLUMN_NOT_AVAILABLE
   *
   * @param self::CUSTOM_COLUMN_ERROR_* $customColumnError
   */
  public function setCustomColumnError($customColumnError)
  {
    $this->customColumnError = $customColumnError;
  }
  /**
   * @return self::CUSTOM_COLUMN_ERROR_*
   */
  public function getCustomColumnError()
  {
    return $this->customColumnError;
  }
  /**
   * The reasons for the date error
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, INVALID_FIELD_VALUES_IN_DATE,
   * INVALID_FIELD_VALUES_IN_DATE_TIME, INVALID_STRING_DATE,
   * INVALID_STRING_DATE_TIME_MICROS, INVALID_STRING_DATE_TIME_SECONDS,
   * INVALID_STRING_DATE_TIME_SECONDS_WITH_OFFSET, EARLIER_THAN_MINIMUM_DATE,
   * LATER_THAN_MAXIMUM_DATE, DATE_RANGE_MINIMUM_DATE_LATER_THAN_MAXIMUM_DATE,
   * DATE_RANGE_MINIMUM_AND_MAXIMUM_DATES_BOTH_NULL
   *
   * @param self::DATE_ERROR_* $dateError
   */
  public function setDateError($dateError)
  {
    $this->dateError = $dateError;
  }
  /**
   * @return self::DATE_ERROR_*
   */
  public function getDateError()
  {
    return $this->dateError;
  }
  /**
   * The reasons for the date range error
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, INVALID_DATE,
   * START_DATE_AFTER_END_DATE, CANNOT_SET_DATE_TO_PAST,
   * AFTER_MAXIMUM_ALLOWABLE_DATE, CANNOT_MODIFY_START_DATE_IF_ALREADY_STARTED
   *
   * @param self::DATE_RANGE_ERROR_* $dateRangeError
   */
  public function setDateRangeError($dateRangeError)
  {
    $this->dateRangeError = $dateRangeError;
  }
  /**
   * @return self::DATE_RANGE_ERROR_*
   */
  public function getDateRangeError()
  {
    return $this->dateRangeError;
  }
  /**
   * The reasons for the distinct error
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, DUPLICATE_ELEMENT, DUPLICATE_TYPE
   *
   * @param self::DISTINCT_ERROR_* $distinctError
   */
  public function setDistinctError($distinctError)
  {
    $this->distinctError = $distinctError;
  }
  /**
   * @return self::DISTINCT_ERROR_*
   */
  public function getDistinctError()
  {
    return $this->distinctError;
  }
  /**
   * The reasons for the header error.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, INVALID_USER_SELECTED_CUSTOMER_ID,
   * INVALID_LOGIN_CUSTOMER_ID
   *
   * @param self::HEADER_ERROR_* $headerError
   */
  public function setHeaderError($headerError)
  {
    $this->headerError = $headerError;
  }
  /**
   * @return self::HEADER_ERROR_*
   */
  public function getHeaderError()
  {
    return $this->headerError;
  }
  /**
   * An unexpected server-side error.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, INTERNAL_ERROR,
   * ERROR_CODE_NOT_PUBLISHED, TRANSIENT_ERROR, DEADLINE_EXCEEDED
   *
   * @param self::INTERNAL_ERROR_* $internalError
   */
  public function setInternalError($internalError)
  {
    $this->internalError = $internalError;
  }
  /**
   * @return self::INTERNAL_ERROR_*
   */
  public function getInternalError()
  {
    return $this->internalError;
  }
  /**
   * The reasons for invalid parameter errors.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, INVALID_CURRENCY_CODE
   *
   * @param self::INVALID_PARAMETER_ERROR_* $invalidParameterError
   */
  public function setInvalidParameterError($invalidParameterError)
  {
    $this->invalidParameterError = $invalidParameterError;
  }
  /**
   * @return self::INVALID_PARAMETER_ERROR_*
   */
  public function getInvalidParameterError()
  {
    return $this->invalidParameterError;
  }
  /**
   * An error with the query
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, QUERY_ERROR, BAD_ENUM_CONSTANT,
   * BAD_ESCAPE_SEQUENCE, BAD_FIELD_NAME, BAD_LIMIT_VALUE, BAD_NUMBER,
   * BAD_OPERATOR, BAD_PARAMETER_NAME, BAD_PARAMETER_VALUE,
   * BAD_RESOURCE_TYPE_IN_FROM_CLAUSE, BAD_SYMBOL, BAD_VALUE,
   * DATE_RANGE_TOO_WIDE, DATE_RANGE_TOO_NARROW, EXPECTED_AND, EXPECTED_BY,
   * EXPECTED_DIMENSION_FIELD_IN_SELECT_CLAUSE, EXPECTED_FILTERS_ON_DATE_RANGE,
   * EXPECTED_FROM, EXPECTED_LIST, EXPECTED_REFERENCED_FIELD_IN_SELECT_CLAUSE,
   * EXPECTED_SELECT, EXPECTED_SINGLE_VALUE,
   * EXPECTED_VALUE_WITH_BETWEEN_OPERATOR, INVALID_DATE_FORMAT,
   * MISALIGNED_DATE_FOR_FILTER, INVALID_STRING_VALUE,
   * INVALID_VALUE_WITH_BETWEEN_OPERATOR, INVALID_VALUE_WITH_DURING_OPERATOR,
   * INVALID_VALUE_WITH_LIKE_OPERATOR, OPERATOR_FIELD_MISMATCH,
   * PROHIBITED_EMPTY_LIST_IN_CONDITION, PROHIBITED_ENUM_CONSTANT,
   * PROHIBITED_FIELD_COMBINATION_IN_SELECT_CLAUSE,
   * PROHIBITED_FIELD_IN_ORDER_BY_CLAUSE, PROHIBITED_FIELD_IN_SELECT_CLAUSE,
   * PROHIBITED_FIELD_IN_WHERE_CLAUSE, PROHIBITED_RESOURCE_TYPE_IN_FROM_CLAUSE,
   * PROHIBITED_RESOURCE_TYPE_IN_SELECT_CLAUSE,
   * PROHIBITED_RESOURCE_TYPE_IN_WHERE_CLAUSE,
   * PROHIBITED_METRIC_IN_SELECT_OR_WHERE_CLAUSE,
   * PROHIBITED_SEGMENT_IN_SELECT_OR_WHERE_CLAUSE,
   * PROHIBITED_SEGMENT_WITH_METRIC_IN_SELECT_OR_WHERE_CLAUSE,
   * LIMIT_VALUE_TOO_LOW, PROHIBITED_NEWLINE_IN_STRING,
   * PROHIBITED_VALUE_COMBINATION_IN_LIST,
   * PROHIBITED_VALUE_COMBINATION_WITH_BETWEEN_OPERATOR, STRING_NOT_TERMINATED,
   * TOO_MANY_SEGMENTS, UNEXPECTED_END_OF_QUERY, UNEXPECTED_FROM_CLAUSE,
   * UNRECOGNIZED_FIELD, UNEXPECTED_INPUT, REQUESTED_METRICS_FOR_MANAGER,
   * FILTER_HAS_TOO_MANY_VALUES
   *
   * @param self::QUERY_ERROR_* $queryError
   */
  public function setQueryError($queryError)
  {
    $this->queryError = $queryError;
  }
  /**
   * @return self::QUERY_ERROR_*
   */
  public function getQueryError()
  {
    return $this->queryError;
  }
  /**
   * An error with the amount of quota remaining.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, RESOURCE_EXHAUSTED,
   * RESOURCE_TEMPORARILY_EXHAUSTED
   *
   * @param self::QUOTA_ERROR_* $quotaError
   */
  public function setQuotaError($quotaError)
  {
    $this->quotaError = $quotaError;
  }
  /**
   * @return self::QUOTA_ERROR_*
   */
  public function getQuotaError()
  {
    return $this->quotaError;
  }
  /**
   * An error caused by the request
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, RESOURCE_NAME_MISSING,
   * RESOURCE_NAME_MALFORMED, BAD_RESOURCE_ID, INVALID_PRODUCT_NAME,
   * INVALID_CUSTOMER_ID, OPERATION_REQUIRED, RESOURCE_NOT_FOUND,
   * INVALID_PAGE_TOKEN, EXPIRED_PAGE_TOKEN, INVALID_PAGE_SIZE,
   * REQUIRED_FIELD_MISSING, IMMUTABLE_FIELD, TOO_MANY_MUTATE_OPERATIONS,
   * CANNOT_BE_EXECUTED_BY_MANAGER_ACCOUNT, CANNOT_MODIFY_FOREIGN_FIELD,
   * INVALID_ENUM_VALUE, LOGIN_CUSTOMER_ID_PARAMETER_MISSING,
   * LOGIN_OR_LINKED_CUSTOMER_ID_PARAMETER_REQUIRED,
   * VALIDATE_ONLY_REQUEST_HAS_PAGE_TOKEN,
   * CANNOT_RETURN_SUMMARY_ROW_FOR_REQUEST_WITHOUT_METRICS,
   * CANNOT_RETURN_SUMMARY_ROW_FOR_VALIDATE_ONLY_REQUESTS,
   * INCONSISTENT_RETURN_SUMMARY_ROW_VALUE,
   * TOTAL_RESULTS_COUNT_NOT_ORIGINALLY_REQUESTED, RPC_DEADLINE_TOO_SHORT,
   * PRODUCT_NOT_SUPPORTED
   *
   * @param self::REQUEST_ERROR_* $requestError
   */
  public function setRequestError($requestError)
  {
    $this->requestError = $requestError;
  }
  /**
   * @return self::REQUEST_ERROR_*
   */
  public function getRequestError()
  {
    return $this->requestError;
  }
  /**
   * The reasons for the size limit error
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, REQUEST_SIZE_LIMIT_EXCEEDED,
   * RESPONSE_SIZE_LIMIT_EXCEEDED
   *
   * @param self::SIZE_LIMIT_ERROR_* $sizeLimitError
   */
  public function setSizeLimitError($sizeLimitError)
  {
    $this->sizeLimitError = $sizeLimitError;
  }
  /**
   * @return self::SIZE_LIMIT_ERROR_*
   */
  public function getSizeLimitError()
  {
    return $this->sizeLimitError;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ErrorsErrorCode::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ErrorsErrorCode');
