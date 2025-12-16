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

class TimePartitioning extends \Google\Model
{
  /**
   * Optional. Number of milliseconds for which to keep the storage for a
   * partition. A wrapper is used here because 0 is an invalid value.
   *
   * @var string
   */
  public $expirationMs;
  /**
   * Optional. If not set, the table is partitioned by pseudo column
   * '_PARTITIONTIME'; if set, the table is partitioned by this field. The field
   * must be a top-level TIMESTAMP or DATE field. Its mode must be NULLABLE or
   * REQUIRED. A wrapper is used here because an empty string is an invalid
   * value.
   *
   * @var string
   */
  public $field;
  /**
   * If set to true, queries over this table require a partition filter that can
   * be used for partition elimination to be specified. This field is
   * deprecated; please set the field with the same name on the table itself
   * instead. This field needs a wrapper because we want to output the default
   * value, false, if the user explicitly set it.
   *
   * @deprecated
   * @var bool
   */
  public $requirePartitionFilter;
  /**
   * Required. The supported types are DAY, HOUR, MONTH, and YEAR, which will
   * generate one partition per day, hour, month, and year, respectively.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. Number of milliseconds for which to keep the storage for a
   * partition. A wrapper is used here because 0 is an invalid value.
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
   * Optional. If not set, the table is partitioned by pseudo column
   * '_PARTITIONTIME'; if set, the table is partitioned by this field. The field
   * must be a top-level TIMESTAMP or DATE field. Its mode must be NULLABLE or
   * REQUIRED. A wrapper is used here because an empty string is an invalid
   * value.
   *
   * @param string $field
   */
  public function setField($field)
  {
    $this->field = $field;
  }
  /**
   * @return string
   */
  public function getField()
  {
    return $this->field;
  }
  /**
   * If set to true, queries over this table require a partition filter that can
   * be used for partition elimination to be specified. This field is
   * deprecated; please set the field with the same name on the table itself
   * instead. This field needs a wrapper because we want to output the default
   * value, false, if the user explicitly set it.
   *
   * @deprecated
   * @param bool $requirePartitionFilter
   */
  public function setRequirePartitionFilter($requirePartitionFilter)
  {
    $this->requirePartitionFilter = $requirePartitionFilter;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getRequirePartitionFilter()
  {
    return $this->requirePartitionFilter;
  }
  /**
   * Required. The supported types are DAY, HOUR, MONTH, and YEAR, which will
   * generate one partition per day, hour, month, and year, respectively.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TimePartitioning::class, 'Google_Service_Bigquery_TimePartitioning');
