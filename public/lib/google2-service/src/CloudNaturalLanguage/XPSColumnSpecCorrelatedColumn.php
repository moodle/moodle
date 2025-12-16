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

namespace Google\Service\CloudNaturalLanguage;

class XPSColumnSpecCorrelatedColumn extends \Google\Model
{
  /**
   * @var int
   */
  public $columnId;
  protected $correlationStatsType = XPSCorrelationStats::class;
  protected $correlationStatsDataType = '';

  /**
   * @param int $columnId
   */
  public function setColumnId($columnId)
  {
    $this->columnId = $columnId;
  }
  /**
   * @return int
   */
  public function getColumnId()
  {
    return $this->columnId;
  }
  /**
   * @param XPSCorrelationStats $correlationStats
   */
  public function setCorrelationStats(XPSCorrelationStats $correlationStats)
  {
    $this->correlationStats = $correlationStats;
  }
  /**
   * @return XPSCorrelationStats
   */
  public function getCorrelationStats()
  {
    return $this->correlationStats;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSColumnSpecCorrelatedColumn::class, 'Google_Service_CloudNaturalLanguage_XPSColumnSpecCorrelatedColumn');
