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

namespace Google\Service\CloudAlloyDBAdmin;

class QueryInsightsInstanceConfig extends \Google\Model
{
  /**
   * Number of query execution plans captured by Insights per minute for all
   * queries combined. The default value is 5. Any integer between 0 and 20 is
   * considered valid.
   *
   * @var string
   */
  public $queryPlansPerMinute;
  /**
   * Query string length. The default value is 1024. Any integer between 256 and
   * 4500 is considered valid.
   *
   * @var string
   */
  public $queryStringLength;
  /**
   * Record application tags for an instance. This flag is turned "on" by
   * default.
   *
   * @var bool
   */
  public $recordApplicationTags;
  /**
   * Record client address for an instance. Client address is PII information.
   * This flag is turned "on" by default.
   *
   * @var bool
   */
  public $recordClientAddress;

  /**
   * Number of query execution plans captured by Insights per minute for all
   * queries combined. The default value is 5. Any integer between 0 and 20 is
   * considered valid.
   *
   * @param string $queryPlansPerMinute
   */
  public function setQueryPlansPerMinute($queryPlansPerMinute)
  {
    $this->queryPlansPerMinute = $queryPlansPerMinute;
  }
  /**
   * @return string
   */
  public function getQueryPlansPerMinute()
  {
    return $this->queryPlansPerMinute;
  }
  /**
   * Query string length. The default value is 1024. Any integer between 256 and
   * 4500 is considered valid.
   *
   * @param string $queryStringLength
   */
  public function setQueryStringLength($queryStringLength)
  {
    $this->queryStringLength = $queryStringLength;
  }
  /**
   * @return string
   */
  public function getQueryStringLength()
  {
    return $this->queryStringLength;
  }
  /**
   * Record application tags for an instance. This flag is turned "on" by
   * default.
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
   * Record client address for an instance. Client address is PII information.
   * This flag is turned "on" by default.
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
class_alias(QueryInsightsInstanceConfig::class, 'Google_Service_CloudAlloyDBAdmin_QueryInsightsInstanceConfig');
