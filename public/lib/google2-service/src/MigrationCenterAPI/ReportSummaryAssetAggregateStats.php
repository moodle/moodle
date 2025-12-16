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

namespace Google\Service\MigrationCenterAPI;

class ReportSummaryAssetAggregateStats extends \Google\Model
{
  protected $coreCountHistogramType = ReportSummaryHistogramChartData::class;
  protected $coreCountHistogramDataType = '';
  protected $memoryBytesHistogramType = ReportSummaryHistogramChartData::class;
  protected $memoryBytesHistogramDataType = '';
  protected $memoryUtilizationChartType = ReportSummaryUtilizationChartData::class;
  protected $memoryUtilizationChartDataType = '';
  protected $operatingSystemType = ReportSummaryChartData::class;
  protected $operatingSystemDataType = '';
  protected $softwareInstancesType = ReportSummaryChartData::class;
  protected $softwareInstancesDataType = '';
  protected $storageBytesHistogramType = ReportSummaryHistogramChartData::class;
  protected $storageBytesHistogramDataType = '';
  protected $storageUtilizationChartType = ReportSummaryUtilizationChartData::class;
  protected $storageUtilizationChartDataType = '';
  /**
   * Count of the number of unique assets in this collection.
   *
   * @var string
   */
  public $totalAssets;
  /**
   * Sum of the CPU core count of all the assets in this collection.
   *
   * @var string
   */
  public $totalCores;
  /**
   * Sum of the memory in bytes of all the assets in this collection.
   *
   * @var string
   */
  public $totalMemoryBytes;
  /**
   * Sum of persistent storage in bytes of all the assets in this collection.
   *
   * @var string
   */
  public $totalStorageBytes;

  /**
   * Histogram showing a distribution of logical CPU core counts.
   *
   * @param ReportSummaryHistogramChartData $coreCountHistogram
   */
  public function setCoreCountHistogram(ReportSummaryHistogramChartData $coreCountHistogram)
  {
    $this->coreCountHistogram = $coreCountHistogram;
  }
  /**
   * @return ReportSummaryHistogramChartData
   */
  public function getCoreCountHistogram()
  {
    return $this->coreCountHistogram;
  }
  /**
   * Histogram showing a distribution of memory sizes.
   *
   * @param ReportSummaryHistogramChartData $memoryBytesHistogram
   */
  public function setMemoryBytesHistogram(ReportSummaryHistogramChartData $memoryBytesHistogram)
  {
    $this->memoryBytesHistogram = $memoryBytesHistogram;
  }
  /**
   * @return ReportSummaryHistogramChartData
   */
  public function getMemoryBytesHistogram()
  {
    return $this->memoryBytesHistogram;
  }
  /**
   * Total memory split into Used/Free buckets.
   *
   * @param ReportSummaryUtilizationChartData $memoryUtilizationChart
   */
  public function setMemoryUtilizationChart(ReportSummaryUtilizationChartData $memoryUtilizationChart)
  {
    $this->memoryUtilizationChart = $memoryUtilizationChart;
  }
  /**
   * @return ReportSummaryUtilizationChartData
   */
  public function getMemoryUtilizationChart()
  {
    return $this->memoryUtilizationChart;
  }
  /**
   * Count of assets grouped by Operating System families.
   *
   * @param ReportSummaryChartData $operatingSystem
   */
  public function setOperatingSystem(ReportSummaryChartData $operatingSystem)
  {
    $this->operatingSystem = $operatingSystem;
  }
  /**
   * @return ReportSummaryChartData
   */
  public function getOperatingSystem()
  {
    return $this->operatingSystem;
  }
  /**
   * Output only. Count of assets grouped by software name. Only present for
   * virtual machines.
   *
   * @param ReportSummaryChartData $softwareInstances
   */
  public function setSoftwareInstances(ReportSummaryChartData $softwareInstances)
  {
    $this->softwareInstances = $softwareInstances;
  }
  /**
   * @return ReportSummaryChartData
   */
  public function getSoftwareInstances()
  {
    return $this->softwareInstances;
  }
  /**
   * Histogram showing a distribution of storage sizes.
   *
   * @param ReportSummaryHistogramChartData $storageBytesHistogram
   */
  public function setStorageBytesHistogram(ReportSummaryHistogramChartData $storageBytesHistogram)
  {
    $this->storageBytesHistogram = $storageBytesHistogram;
  }
  /**
   * @return ReportSummaryHistogramChartData
   */
  public function getStorageBytesHistogram()
  {
    return $this->storageBytesHistogram;
  }
  /**
   * Total memory split into Used/Free buckets.
   *
   * @param ReportSummaryUtilizationChartData $storageUtilizationChart
   */
  public function setStorageUtilizationChart(ReportSummaryUtilizationChartData $storageUtilizationChart)
  {
    $this->storageUtilizationChart = $storageUtilizationChart;
  }
  /**
   * @return ReportSummaryUtilizationChartData
   */
  public function getStorageUtilizationChart()
  {
    return $this->storageUtilizationChart;
  }
  /**
   * Count of the number of unique assets in this collection.
   *
   * @param string $totalAssets
   */
  public function setTotalAssets($totalAssets)
  {
    $this->totalAssets = $totalAssets;
  }
  /**
   * @return string
   */
  public function getTotalAssets()
  {
    return $this->totalAssets;
  }
  /**
   * Sum of the CPU core count of all the assets in this collection.
   *
   * @param string $totalCores
   */
  public function setTotalCores($totalCores)
  {
    $this->totalCores = $totalCores;
  }
  /**
   * @return string
   */
  public function getTotalCores()
  {
    return $this->totalCores;
  }
  /**
   * Sum of the memory in bytes of all the assets in this collection.
   *
   * @param string $totalMemoryBytes
   */
  public function setTotalMemoryBytes($totalMemoryBytes)
  {
    $this->totalMemoryBytes = $totalMemoryBytes;
  }
  /**
   * @return string
   */
  public function getTotalMemoryBytes()
  {
    return $this->totalMemoryBytes;
  }
  /**
   * Sum of persistent storage in bytes of all the assets in this collection.
   *
   * @param string $totalStorageBytes
   */
  public function setTotalStorageBytes($totalStorageBytes)
  {
    $this->totalStorageBytes = $totalStorageBytes;
  }
  /**
   * @return string
   */
  public function getTotalStorageBytes()
  {
    return $this->totalStorageBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportSummaryAssetAggregateStats::class, 'Google_Service_MigrationCenterAPI_ReportSummaryAssetAggregateStats');
