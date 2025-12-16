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

namespace Google\Service\CloudHealthcare;

class TimePartitioning extends \Google\Model
{
  /**
   * Default unknown time.
   */
  public const TYPE_PARTITION_TYPE_UNSPECIFIED = 'PARTITION_TYPE_UNSPECIFIED';
  /**
   * Data partitioned by hour.
   */
  public const TYPE_HOUR = 'HOUR';
  /**
   * Data partitioned by day.
   */
  public const TYPE_DAY = 'DAY';
  /**
   * Data partitioned by month.
   */
  public const TYPE_MONTH = 'MONTH';
  /**
   * Data partitioned by year.
   */
  public const TYPE_YEAR = 'YEAR';
  /**
   * Number of milliseconds for which to keep the storage for a partition.
   *
   * @var string
   */
  public $expirationMs;
  /**
   * Type of partitioning.
   *
   * @var string
   */
  public $type;

  /**
   * Number of milliseconds for which to keep the storage for a partition.
   *
   * @param string $expirationMs
   */
  public function setExpirationMs($expirationMs)
  {
    $this->expirationMs = $expirationMs;
  }
  /**
   * @return string
   */
  public function getExpirationMs()
  {
    return $this->expirationMs;
  }
  /**
   * Type of partitioning.
   *
   * Accepted values: PARTITION_TYPE_UNSPECIFIED, HOUR, DAY, MONTH, YEAR
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
class_alias(TimePartitioning::class, 'Google_Service_CloudHealthcare_TimePartitioning');
