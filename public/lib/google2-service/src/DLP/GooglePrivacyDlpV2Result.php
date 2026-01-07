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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2Result extends \Google\Collection
{
  protected $collection_key = 'infoTypeStats';
  protected $hybridStatsType = GooglePrivacyDlpV2HybridInspectStatistics::class;
  protected $hybridStatsDataType = '';
  protected $infoTypeStatsType = GooglePrivacyDlpV2InfoTypeStats::class;
  protected $infoTypeStatsDataType = 'array';
  /**
   * Number of rows scanned after sampling and time filtering (applicable for
   * row based stores such as BigQuery).
   *
   * @var string
   */
  public $numRowsProcessed;
  /**
   * Total size in bytes that were processed.
   *
   * @var string
   */
  public $processedBytes;
  /**
   * Estimate of the number of bytes to process.
   *
   * @var string
   */
  public $totalEstimatedBytes;

  /**
   * Statistics related to the processing of hybrid inspect.
   *
   * @param GooglePrivacyDlpV2HybridInspectStatistics $hybridStats
   */
  public function setHybridStats(GooglePrivacyDlpV2HybridInspectStatistics $hybridStats)
  {
    $this->hybridStats = $hybridStats;
  }
  /**
   * @return GooglePrivacyDlpV2HybridInspectStatistics
   */
  public function getHybridStats()
  {
    return $this->hybridStats;
  }
  /**
   * Statistics of how many instances of each info type were found during
   * inspect job.
   *
   * @param GooglePrivacyDlpV2InfoTypeStats[] $infoTypeStats
   */
  public function setInfoTypeStats($infoTypeStats)
  {
    $this->infoTypeStats = $infoTypeStats;
  }
  /**
   * @return GooglePrivacyDlpV2InfoTypeStats[]
   */
  public function getInfoTypeStats()
  {
    return $this->infoTypeStats;
  }
  /**
   * Number of rows scanned after sampling and time filtering (applicable for
   * row based stores such as BigQuery).
   *
   * @param string $numRowsProcessed
   */
  public function setNumRowsProcessed($numRowsProcessed)
  {
    $this->numRowsProcessed = $numRowsProcessed;
  }
  /**
   * @return string
   */
  public function getNumRowsProcessed()
  {
    return $this->numRowsProcessed;
  }
  /**
   * Total size in bytes that were processed.
   *
   * @param string $processedBytes
   */
  public function setProcessedBytes($processedBytes)
  {
    $this->processedBytes = $processedBytes;
  }
  /**
   * @return string
   */
  public function getProcessedBytes()
  {
    return $this->processedBytes;
  }
  /**
   * Estimate of the number of bytes to process.
   *
   * @param string $totalEstimatedBytes
   */
  public function setTotalEstimatedBytes($totalEstimatedBytes)
  {
    $this->totalEstimatedBytes = $totalEstimatedBytes;
  }
  /**
   * @return string
   */
  public function getTotalEstimatedBytes()
  {
    return $this->totalEstimatedBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2Result::class, 'Google_Service_DLP_GooglePrivacyDlpV2Result');
