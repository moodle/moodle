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

namespace Google\Service\Datastream;

class MongodbSourceConfig extends \Google\Model
{
  /**
   * Unspecified JSON mode.
   */
  public const JSON_MODE_MONGODB_JSON_MODE_UNSPECIFIED = 'MONGODB_JSON_MODE_UNSPECIFIED';
  /**
   * Strict JSON mode.
   */
  public const JSON_MODE_STRICT = 'STRICT';
  /**
   * Canonical JSON mode.
   */
  public const JSON_MODE_CANONICAL = 'CANONICAL';
  protected $excludeObjectsType = MongodbCluster::class;
  protected $excludeObjectsDataType = '';
  protected $includeObjectsType = MongodbCluster::class;
  protected $includeObjectsDataType = '';
  /**
   * Optional. MongoDB JSON mode to use for the stream.
   *
   * @var string
   */
  public $jsonMode;
  /**
   * Optional. Maximum number of concurrent backfill tasks. The number should be
   * non-negative and less than or equal to 50. If not set (or set to 0), the
   * system's default value is used
   *
   * @var int
   */
  public $maxConcurrentBackfillTasks;

  /**
   * MongoDB collections to exclude from the stream.
   *
   * @param MongodbCluster $excludeObjects
   */
  public function setExcludeObjects(MongodbCluster $excludeObjects)
  {
    $this->excludeObjects = $excludeObjects;
  }
  /**
   * @return MongodbCluster
   */
  public function getExcludeObjects()
  {
    return $this->excludeObjects;
  }
  /**
   * MongoDB collections to include in the stream.
   *
   * @param MongodbCluster $includeObjects
   */
  public function setIncludeObjects(MongodbCluster $includeObjects)
  {
    $this->includeObjects = $includeObjects;
  }
  /**
   * @return MongodbCluster
   */
  public function getIncludeObjects()
  {
    return $this->includeObjects;
  }
  /**
   * Optional. MongoDB JSON mode to use for the stream.
   *
   * Accepted values: MONGODB_JSON_MODE_UNSPECIFIED, STRICT, CANONICAL
   *
   * @param self::JSON_MODE_* $jsonMode
   */
  public function setJsonMode($jsonMode)
  {
    $this->jsonMode = $jsonMode;
  }
  /**
   * @return self::JSON_MODE_*
   */
  public function getJsonMode()
  {
    return $this->jsonMode;
  }
  /**
   * Optional. Maximum number of concurrent backfill tasks. The number should be
   * non-negative and less than or equal to 50. If not set (or set to 0), the
   * system's default value is used
   *
   * @param int $maxConcurrentBackfillTasks
   */
  public function setMaxConcurrentBackfillTasks($maxConcurrentBackfillTasks)
  {
    $this->maxConcurrentBackfillTasks = $maxConcurrentBackfillTasks;
  }
  /**
   * @return int
   */
  public function getMaxConcurrentBackfillTasks()
  {
    return $this->maxConcurrentBackfillTasks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MongodbSourceConfig::class, 'Google_Service_Datastream_MongodbSourceConfig');
