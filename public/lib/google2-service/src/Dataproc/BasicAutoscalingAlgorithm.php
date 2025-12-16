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

namespace Google\Service\Dataproc;

class BasicAutoscalingAlgorithm extends \Google\Model
{
  /**
   * Optional. Duration between scaling events. A scaling period starts after
   * the update operation from the previous event has completed.Bounds: 2m, 1d.
   * Default: 2m.
   *
   * @var string
   */
  public $cooldownPeriod;
  protected $sparkStandaloneConfigType = SparkStandaloneAutoscalingConfig::class;
  protected $sparkStandaloneConfigDataType = '';
  protected $yarnConfigType = BasicYarnAutoscalingConfig::class;
  protected $yarnConfigDataType = '';

  /**
   * Optional. Duration between scaling events. A scaling period starts after
   * the update operation from the previous event has completed.Bounds: 2m, 1d.
   * Default: 2m.
   *
   * @param string $cooldownPeriod
   */
  public function setCooldownPeriod($cooldownPeriod)
  {
    $this->cooldownPeriod = $cooldownPeriod;
  }
  /**
   * @return string
   */
  public function getCooldownPeriod()
  {
    return $this->cooldownPeriod;
  }
  /**
   * Optional. Spark Standalone autoscaling configuration
   *
   * @param SparkStandaloneAutoscalingConfig $sparkStandaloneConfig
   */
  public function setSparkStandaloneConfig(SparkStandaloneAutoscalingConfig $sparkStandaloneConfig)
  {
    $this->sparkStandaloneConfig = $sparkStandaloneConfig;
  }
  /**
   * @return SparkStandaloneAutoscalingConfig
   */
  public function getSparkStandaloneConfig()
  {
    return $this->sparkStandaloneConfig;
  }
  /**
   * Optional. YARN autoscaling configuration.
   *
   * @param BasicYarnAutoscalingConfig $yarnConfig
   */
  public function setYarnConfig(BasicYarnAutoscalingConfig $yarnConfig)
  {
    $this->yarnConfig = $yarnConfig;
  }
  /**
   * @return BasicYarnAutoscalingConfig
   */
  public function getYarnConfig()
  {
    return $this->yarnConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BasicAutoscalingAlgorithm::class, 'Google_Service_Dataproc_BasicAutoscalingAlgorithm');
