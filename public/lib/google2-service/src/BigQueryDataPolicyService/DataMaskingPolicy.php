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

namespace Google\Service\BigQueryDataPolicyService;

class DataMaskingPolicy extends \Google\Model
{
  /**
   * Default, unspecified predefined expression. No masking will take place
   * since no expression is specified.
   */
  public const PREDEFINED_EXPRESSION_PREDEFINED_EXPRESSION_UNSPECIFIED = 'PREDEFINED_EXPRESSION_UNSPECIFIED';
  /**
   * Masking expression to replace data with SHA-256 hash.
   */
  public const PREDEFINED_EXPRESSION_SHA256 = 'SHA256';
  /**
   * Masking expression to replace data with NULLs.
   */
  public const PREDEFINED_EXPRESSION_ALWAYS_NULL = 'ALWAYS_NULL';
  /**
   * Masking expression to replace data with their default masking values. The
   * default masking values for each type listed as below: * STRING: "" * BYTES:
   * b'' * INTEGER: 0 * FLOAT: 0.0 * NUMERIC: 0 * BOOLEAN: FALSE * TIMESTAMP:
   * 1970-01-01 00:00:00 UTC * DATE: 1970-01-01 * TIME: 00:00:00 * DATETIME:
   * 1970-01-01T00:00:00 * GEOGRAPHY: POINT(0 0) * BIGNUMERIC: 0 * ARRAY: [] *
   * STRUCT: NOT_APPLICABLE * JSON: NULL
   */
  public const PREDEFINED_EXPRESSION_DEFAULT_MASKING_VALUE = 'DEFAULT_MASKING_VALUE';
  /**
   * Masking expression shows the last four characters of text. The masking
   * behavior is as follows: * If text length > 4 characters: Replace text with
   * XXXXX, append last four characters of original text. * If text length <= 4
   * characters: Apply SHA-256 hash.
   */
  public const PREDEFINED_EXPRESSION_LAST_FOUR_CHARACTERS = 'LAST_FOUR_CHARACTERS';
  /**
   * Masking expression shows the first four characters of text. The masking
   * behavior is as follows: * If text length > 4 characters: Replace text with
   * XXXXX, prepend first four characters of original text. * If text length <=
   * 4 characters: Apply SHA-256 hash.
   */
  public const PREDEFINED_EXPRESSION_FIRST_FOUR_CHARACTERS = 'FIRST_FOUR_CHARACTERS';
  /**
   * Masking expression for email addresses. The masking behavior is as follows:
   * * Syntax-valid email address: Replace username with XXXXX. For example,
   * cloudysanfrancisco@gmail.com becomes XXXXX@gmail.com. * Syntax-invalid
   * email address: Apply SHA-256 hash. For more information, see [Email
   * mask](https://cloud.google.com/bigquery/docs/column-data-masking-
   * intro#masking_options).
   */
  public const PREDEFINED_EXPRESSION_EMAIL_MASK = 'EMAIL_MASK';
  /**
   * Masking expression to only show the *year* of `Date`, `DateTime` and
   * `TimeStamp`. For example, with the year 2076: * DATE : 2076-01-01 *
   * DATETIME : 2076-01-01T00:00:00 * TIMESTAMP : 2076-01-01 00:00:00 UTC
   * Truncation occurs according to the UTC time zone. To change this, adjust
   * the default time zone using the `time_zone` system variable. For more
   * information, see [System variables
   * reference](https://cloud.google.com/bigquery/docs/reference/system-
   * variables).
   */
  public const PREDEFINED_EXPRESSION_DATE_YEAR_MASK = 'DATE_YEAR_MASK';
  /**
   * Masking expression that uses hashing to mask column data. It differs from
   * SHA256 in that a unique random value is generated for each query and is
   * added to the hash input, resulting in the hash / masked result to be
   * different for each query. Hence the name "random hash".
   */
  public const PREDEFINED_EXPRESSION_RANDOM_HASH = 'RANDOM_HASH';
  /**
   * Optional. A predefined masking expression.
   *
   * @var string
   */
  public $predefinedExpression;
  /**
   * Optional. The name of the BigQuery routine that contains the custom masking
   * routine, in the format of
   * `projects/{project_number}/datasets/{dataset_id}/routines/{routine_id}`.
   *
   * @var string
   */
  public $routine;

  /**
   * Optional. A predefined masking expression.
   *
   * Accepted values: PREDEFINED_EXPRESSION_UNSPECIFIED, SHA256, ALWAYS_NULL,
   * DEFAULT_MASKING_VALUE, LAST_FOUR_CHARACTERS, FIRST_FOUR_CHARACTERS,
   * EMAIL_MASK, DATE_YEAR_MASK, RANDOM_HASH
   *
   * @param self::PREDEFINED_EXPRESSION_* $predefinedExpression
   */
  public function setPredefinedExpression($predefinedExpression)
  {
    $this->predefinedExpression = $predefinedExpression;
  }
  /**
   * @return self::PREDEFINED_EXPRESSION_*
   */
  public function getPredefinedExpression()
  {
    return $this->predefinedExpression;
  }
  /**
   * Optional. The name of the BigQuery routine that contains the custom masking
   * routine, in the format of
   * `projects/{project_number}/datasets/{dataset_id}/routines/{routine_id}`.
   *
   * @param string $routine
   */
  public function setRoutine($routine)
  {
    $this->routine = $routine;
  }
  /**
   * @return string
   */
  public function getRoutine()
  {
    return $this->routine;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataMaskingPolicy::class, 'Google_Service_BigQueryDataPolicyService_DataMaskingPolicy');
