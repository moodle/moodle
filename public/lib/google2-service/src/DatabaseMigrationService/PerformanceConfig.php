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

namespace Google\Service\DatabaseMigrationService;

class PerformanceConfig extends \Google\Model
{
  /**
   * Unknown dump parallel level. Will be defaulted to OPTIMAL.
   */
  public const DUMP_PARALLEL_LEVEL_DUMP_PARALLEL_LEVEL_UNSPECIFIED = 'DUMP_PARALLEL_LEVEL_UNSPECIFIED';
  /**
   * Minimal parallel level.
   */
  public const DUMP_PARALLEL_LEVEL_MIN = 'MIN';
  /**
   * Optimal parallel level.
   */
  public const DUMP_PARALLEL_LEVEL_OPTIMAL = 'OPTIMAL';
  /**
   * Maximum parallel level.
   */
  public const DUMP_PARALLEL_LEVEL_MAX = 'MAX';
  /**
   * Initial dump parallelism level.
   *
   * @var string
   */
  public $dumpParallelLevel;

  /**
   * Initial dump parallelism level.
   *
   * Accepted values: DUMP_PARALLEL_LEVEL_UNSPECIFIED, MIN, OPTIMAL, MAX
   *
   * @param self::DUMP_PARALLEL_LEVEL_* $dumpParallelLevel
   */
  public function setDumpParallelLevel($dumpParallelLevel)
  {
    $this->dumpParallelLevel = $dumpParallelLevel;
  }
  /**
   * @return self::DUMP_PARALLEL_LEVEL_*
   */
  public function getDumpParallelLevel()
  {
    return $this->dumpParallelLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PerformanceConfig::class, 'Google_Service_DatabaseMigrationService_PerformanceConfig');
