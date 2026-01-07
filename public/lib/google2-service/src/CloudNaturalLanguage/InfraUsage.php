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

class InfraUsage extends \Google\Collection
{
  protected $collection_key = 'tpuMetrics';
  protected $cpuMetricsType = CpuMetric::class;
  protected $cpuMetricsDataType = 'array';
  protected $diskMetricsType = DiskMetric::class;
  protected $diskMetricsDataType = 'array';
  protected $gpuMetricsType = GpuMetric::class;
  protected $gpuMetricsDataType = 'array';
  protected $ramMetricsType = RamMetric::class;
  protected $ramMetricsDataType = 'array';
  protected $tpuMetricsType = TpuMetric::class;
  protected $tpuMetricsDataType = 'array';

  /**
   * Aggregated core metrics since requested start_time.
   *
   * @param CpuMetric[] $cpuMetrics
   */
  public function setCpuMetrics($cpuMetrics)
  {
    $this->cpuMetrics = $cpuMetrics;
  }
  /**
   * @return CpuMetric[]
   */
  public function getCpuMetrics()
  {
    return $this->cpuMetrics;
  }
  /**
   * Aggregated persistent disk metrics since requested start_time.
   *
   * @param DiskMetric[] $diskMetrics
   */
  public function setDiskMetrics($diskMetrics)
  {
    $this->diskMetrics = $diskMetrics;
  }
  /**
   * @return DiskMetric[]
   */
  public function getDiskMetrics()
  {
    return $this->diskMetrics;
  }
  /**
   * Aggregated gpu metrics since requested start_time.
   *
   * @param GpuMetric[] $gpuMetrics
   */
  public function setGpuMetrics($gpuMetrics)
  {
    $this->gpuMetrics = $gpuMetrics;
  }
  /**
   * @return GpuMetric[]
   */
  public function getGpuMetrics()
  {
    return $this->gpuMetrics;
  }
  /**
   * Aggregated ram metrics since requested start_time.
   *
   * @param RamMetric[] $ramMetrics
   */
  public function setRamMetrics($ramMetrics)
  {
    $this->ramMetrics = $ramMetrics;
  }
  /**
   * @return RamMetric[]
   */
  public function getRamMetrics()
  {
    return $this->ramMetrics;
  }
  /**
   * Aggregated tpu metrics since requested start_time.
   *
   * @param TpuMetric[] $tpuMetrics
   */
  public function setTpuMetrics($tpuMetrics)
  {
    $this->tpuMetrics = $tpuMetrics;
  }
  /**
   * @return TpuMetric[]
   */
  public function getTpuMetrics()
  {
    return $this->tpuMetrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InfraUsage::class, 'Google_Service_CloudNaturalLanguage_InfraUsage');
