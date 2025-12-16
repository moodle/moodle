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

namespace Google\Service\Dataflow;

class DebugOptions extends \Google\Model
{
  protected $dataSamplingType = DataSamplingConfig::class;
  protected $dataSamplingDataType = '';
  /**
   * Optional. When true, enables the logging of the literal hot key to the
   * user's Cloud Logging.
   *
   * @var bool
   */
  public $enableHotKeyLogging;

  /**
   * Configuration options for sampling elements from a running pipeline.
   *
   * @param DataSamplingConfig $dataSampling
   */
  public function setDataSampling(DataSamplingConfig $dataSampling)
  {
    $this->dataSampling = $dataSampling;
  }
  /**
   * @return DataSamplingConfig
   */
  public function getDataSampling()
  {
    return $this->dataSampling;
  }
  /**
   * Optional. When true, enables the logging of the literal hot key to the
   * user's Cloud Logging.
   *
   * @param bool $enableHotKeyLogging
   */
  public function setEnableHotKeyLogging($enableHotKeyLogging)
  {
    $this->enableHotKeyLogging = $enableHotKeyLogging;
  }
  /**
   * @return bool
   */
  public function getEnableHotKeyLogging()
  {
    return $this->enableHotKeyLogging;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DebugOptions::class, 'Google_Service_Dataflow_DebugOptions');
