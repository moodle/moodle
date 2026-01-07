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

namespace Google\Service\SQLAdmin;

class InsightsConfig extends \Google\Model
{
  /**
   * Whether Query Insights feature is enabled.
   *
   * @var bool
   */
  public $queryInsightsEnabled;
  /**
   * Number of query execution plans captured by Insights per minute for all
   * queries combined. Default is 5.
   *
   * @var int
   */
  public $queryPlansPerMinute;
  /**
   * Maximum query length stored in bytes. Default value: 1024 bytes. Range:
   * 256-4500 bytes. Query lengths greater than this field value will be
   * truncated to this value. When unset, query length will be the default
   * value. Changing query length will restart the database.
   *
   * @var int
   */
  public $queryStringLength;
  /**
   * Whether Query Insights will record application tags from query when
   * enabled.
   *
   * @var bool
   */
  public $recordApplicationTags;
  /**
   * Whether Query Insights will record client address when enabled.
   *
   * @var bool
   */
  public $recordClientAddress;

  /**
   * Whether Query Insights feature is enabled.
   *
   * @param bool $queryInsightsEnabled
   */
  public function setQueryInsightsEnabled($queryInsightsEnabled)
  {
    $this->queryInsightsEnabled = $queryInsightsEnabled;
  }
  /**
   * @return bool
   */
  public function getQueryInsightsEnabled()
  {
    return $this->queryInsightsEnabled;
  }
  /**
   * Number of query execution plans captured by Insights per minute for all
   * queries combined. Default is 5.
   *
   * @param int $queryPlansPerMinute
   */
  public function setQueryPlansPerMinute($queryPlansPerMinute)
  {
    $this->queryPlansPerMinute = $queryPlansPerMinute;
  }
  /**
   * @return int
   */
  public function getQueryPlansPerMinute()
  {
    return $this->queryPlansPerMinute;
  }
  /**
   * Maximum query length stored in bytes. Default value: 1024 bytes. Range:
   * 256-4500 bytes. Query lengths greater than this field value will be
   * truncated to this value. When unset, query length will be the default
   * value. Changing query length will restart the database.
   *
   * @param int $queryStringLength
   */
  public function setQueryStringLength($queryStringLength)
  {
    $this->queryStringLength = $queryStringLength;
  }
  /**
   * @return int
   */
  public function getQueryStringLength()
  {
    return $this->queryStringLength;
  }
  /**
   * Whether Query Insights will record application tags from query when
   * enabled.
   *
   * @param bool $recordApplicationTags
   */
  public function setRecordApplicationTags($recordApplicationTags)
  {
    $this->recordApplicationTags = $recordApplicationTags;
  }
  /**
   * @return bool
   */
  public function getRecordApplicationTags()
  {
    return $this->recordApplicationTags;
  }
  /**
   * Whether Query Insights will record client address when enabled.
   *
   * @param bool $recordClientAddress
   */
  public function setRecordClientAddress($recordClientAddress)
  {
    $this->recordClientAddress = $recordClientAddress;
  }
  /**
   * @return bool
   */
  public function getRecordClientAddress()
  {
    return $this->recordClientAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InsightsConfig::class, 'Google_Service_SQLAdmin_InsightsConfig');
