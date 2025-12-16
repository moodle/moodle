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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1ActionInvalidDataPartition extends \Google\Model
{
  /**
   * PartitionStructure unspecified.
   */
  public const EXPECTED_STRUCTURE_PARTITION_STRUCTURE_UNSPECIFIED = 'PARTITION_STRUCTURE_UNSPECIFIED';
  /**
   * Consistent hive-style partition definition (both raw and curated zone).
   */
  public const EXPECTED_STRUCTURE_CONSISTENT_KEYS = 'CONSISTENT_KEYS';
  /**
   * Hive style partition definition (curated zone only).
   */
  public const EXPECTED_STRUCTURE_HIVE_STYLE_KEYS = 'HIVE_STYLE_KEYS';
  /**
   * The issue type of InvalidDataPartition.
   *
   * @var string
   */
  public $expectedStructure;

  /**
   * The issue type of InvalidDataPartition.
   *
   * Accepted values: PARTITION_STRUCTURE_UNSPECIFIED, CONSISTENT_KEYS,
   * HIVE_STYLE_KEYS
   *
   * @param self::EXPECTED_STRUCTURE_* $expectedStructure
   */
  public function setExpectedStructure($expectedStructure)
  {
    $this->expectedStructure = $expectedStructure;
  }
  /**
   * @return self::EXPECTED_STRUCTURE_*
   */
  public function getExpectedStructure()
  {
    return $this->expectedStructure;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1ActionInvalidDataPartition::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1ActionInvalidDataPartition');
