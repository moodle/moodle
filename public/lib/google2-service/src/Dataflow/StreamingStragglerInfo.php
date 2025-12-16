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

class StreamingStragglerInfo extends \Google\Model
{
  /**
   * The event-time watermark lag at the time of the straggler detection.
   *
   * @var string
   */
  public $dataWatermarkLag;
  /**
   * End time of this straggler.
   *
   * @var string
   */
  public $endTime;
  /**
   * Start time of this straggler.
   *
   * @var string
   */
  public $startTime;
  /**
   * The system watermark lag at the time of the straggler detection.
   *
   * @var string
   */
  public $systemWatermarkLag;
  /**
   * Name of the worker where the straggler was detected.
   *
   * @var string
   */
  public $workerName;

  /**
   * The event-time watermark lag at the time of the straggler detection.
   *
   * @param string $dataWatermarkLag
   */
  public function setDataWatermarkLag($dataWatermarkLag)
  {
    $this->dataWatermarkLag = $dataWatermarkLag;
  }
  /**
   * @return string
   */
  public function getDataWatermarkLag()
  {
    return $this->dataWatermarkLag;
  }
  /**
   * End time of this straggler.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Start time of this straggler.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * The system watermark lag at the time of the straggler detection.
   *
   * @param string $systemWatermarkLag
   */
  public function setSystemWatermarkLag($systemWatermarkLag)
  {
    $this->systemWatermarkLag = $systemWatermarkLag;
  }
  /**
   * @return string
   */
  public function getSystemWatermarkLag()
  {
    return $this->systemWatermarkLag;
  }
  /**
   * Name of the worker where the straggler was detected.
   *
   * @param string $workerName
   */
  public function setWorkerName($workerName)
  {
    $this->workerName = $workerName;
  }
  /**
   * @return string
   */
  public function getWorkerName()
  {
    return $this->workerName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StreamingStragglerInfo::class, 'Google_Service_Dataflow_StreamingStragglerInfo');
