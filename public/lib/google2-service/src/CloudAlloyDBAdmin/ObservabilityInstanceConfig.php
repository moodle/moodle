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

class ObservabilityInstanceConfig extends \Google\Model
{
  /**
   * Observability feature status for an instance. This flag is turned "off" by
   * default.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Query string length. The default value is 10k.
   *
   * @var int
   */
  public $maxQueryStringLength;
  /**
   * Preserve comments in query string for an instance. This flag is turned
   * "off" by default.
   *
   * @var bool
   */
  public $preserveComments;
  /**
   * Number of query execution plans captured by Insights per minute for all
   * queries combined. The default value is 200. Any integer between 0 to 200 is
   * considered valid.
   *
   * @var int
   */
  public $queryPlansPerMinute;
  /**
   * Record application tags for an instance. This flag is turned "off" by
   * default.
   *
   * @var bool
   */
  public $recordApplicationTags;
  /**
   * Track actively running queries on the instance. If not set, this flag is
   * "off" by default.
   *
   * @var bool
   */
  public $trackActiveQueries;
  /**
   * Output only. Track wait event types during query execution for an instance.
   * This flag is turned "on" by default but tracking is enabled only after
   * observability enabled flag is also turned on. This is read-only flag and
   * only modifiable by internal API.
   *
   * @var bool
   */
  public $trackWaitEventTypes;
  /**
   * Track wait events during query execution for an instance. This flag is
   * turned "on" by default but tracking is enabled only after observability
   * enabled flag is also turned on.
   *
   * @var bool
   */
  public $trackWaitEvents;

  /**
   * Observability feature status for an instance. This flag is turned "off" by
   * default.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Query string length. The default value is 10k.
   *
   * @param int $maxQueryStringLength
   */
  public function setMaxQueryStringLength($maxQueryStringLength)
  {
    $this->maxQueryStringLength = $maxQueryStringLength;
  }
  /**
   * @return int
   */
  public function getMaxQueryStringLength()
  {
    return $this->maxQueryStringLength;
  }
  /**
   * Preserve comments in query string for an instance. This flag is turned
   * "off" by default.
   *
   * @param bool $preserveComments
   */
  public function setPreserveComments($preserveComments)
  {
    $this->preserveComments = $preserveComments;
  }
  /**
   * @return bool
   */
  public function getPreserveComments()
  {
    return $this->preserveComments;
  }
  /**
   * Number of query execution plans captured by Insights per minute for all
   * queries combined. The default value is 200. Any integer between 0 to 200 is
   * considered valid.
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
   * Record application tags for an instance. This flag is turned "off" by
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
   * Track actively running queries on the instance. If not set, this flag is
   * "off" by default.
   *
   * @param bool $trackActiveQueries
   */
  public function setTrackActiveQueries($trackActiveQueries)
  {
    $this->trackActiveQueries = $trackActiveQueries;
  }
  /**
   * @return bool
   */
  public function getTrackActiveQueries()
  {
    return $this->trackActiveQueries;
  }
  /**
   * Output only. Track wait event types during query execution for an instance.
   * This flag is turned "on" by default but tracking is enabled only after
   * observability enabled flag is also turned on. This is read-only flag and
   * only modifiable by internal API.
   *
   * @param bool $trackWaitEventTypes
   */
  public function setTrackWaitEventTypes($trackWaitEventTypes)
  {
    $this->trackWaitEventTypes = $trackWaitEventTypes;
  }
  /**
   * @return bool
   */
  public function getTrackWaitEventTypes()
  {
    return $this->trackWaitEventTypes;
  }
  /**
   * Track wait events during query execution for an instance. This flag is
   * turned "on" by default but tracking is enabled only after observability
   * enabled flag is also turned on.
   *
   * @param bool $trackWaitEvents
   */
  public function setTrackWaitEvents($trackWaitEvents)
  {
    $this->trackWaitEvents = $trackWaitEvents;
  }
  /**
   * @return bool
   */
  public function getTrackWaitEvents()
  {
    return $this->trackWaitEvents;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ObservabilityInstanceConfig::class, 'Google_Service_CloudAlloyDBAdmin_ObservabilityInstanceConfig');
