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

namespace Google\Service\Bigquery;

class DataFormatOptions extends \Google\Model
{
  /**
   * Corresponds to default API output behavior, which is FLOAT64.
   */
  public const TIMESTAMP_OUTPUT_FORMAT_TIMESTAMP_OUTPUT_FORMAT_UNSPECIFIED = 'TIMESTAMP_OUTPUT_FORMAT_UNSPECIFIED';
  /**
   * Timestamp is output as float64 seconds since Unix epoch.
   */
  public const TIMESTAMP_OUTPUT_FORMAT_FLOAT64 = 'FLOAT64';
  /**
   * Timestamp is output as int64 microseconds since Unix epoch.
   */
  public const TIMESTAMP_OUTPUT_FORMAT_INT64 = 'INT64';
  /**
   * Timestamp is output as ISO 8601 String ("YYYY-MM-
   * DDTHH:MM:SS.FFFFFFFFFFFFZ").
   */
  public const TIMESTAMP_OUTPUT_FORMAT_ISO8601_STRING = 'ISO8601_STRING';
  /**
   * Optional. The API output format for a timestamp. This offers more explicit
   * control over the timestamp output format as compared to the existing
   * `use_int64_timestamp` option.
   *
   * @var string
   */
  public $timestampOutputFormat;
  /**
   * Optional. Output timestamp as usec int64. Default is false.
   *
   * @var bool
   */
  public $useInt64Timestamp;

  /**
   * Optional. The API output format for a timestamp. This offers more explicit
   * control over the timestamp output format as compared to the existing
   * `use_int64_timestamp` option.
   *
   * Accepted values: TIMESTAMP_OUTPUT_FORMAT_UNSPECIFIED, FLOAT64, INT64,
   * ISO8601_STRING
   *
   * @param self::TIMESTAMP_OUTPUT_FORMAT_* $timestampOutputFormat
   */
  public function setTimestampOutputFormat($timestampOutputFormat)
  {
    $this->timestampOutputFormat = $timestampOutputFormat;
  }
  /**
   * @return self::TIMESTAMP_OUTPUT_FORMAT_*
   */
  public function getTimestampOutputFormat()
  {
    return $this->timestampOutputFormat;
  }
  /**
   * Optional. Output timestamp as usec int64. Default is false.
   *
   * @param bool $useInt64Timestamp
   */
  public function setUseInt64Timestamp($useInt64Timestamp)
  {
    $this->useInt64Timestamp = $useInt64Timestamp;
  }
  /**
   * @return bool
   */
  public function getUseInt64Timestamp()
  {
    return $this->useInt64Timestamp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataFormatOptions::class, 'Google_Service_Bigquery_DataFormatOptions');
