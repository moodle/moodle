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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1AudioStatusReport extends \Google\Model
{
  /**
   * Output only. Active input device's name.
   *
   * @var string
   */
  public $inputDevice;
  /**
   * Output only. Active input device's gain in [0, 100].
   *
   * @var int
   */
  public $inputGain;
  /**
   * Output only. Is active input device mute or not.
   *
   * @var bool
   */
  public $inputMute;
  /**
   * Output only. Active output device's name.
   *
   * @var string
   */
  public $outputDevice;
  /**
   * Output only. Is active output device mute or not.
   *
   * @var bool
   */
  public $outputMute;
  /**
   * Output only. Active output device's volume in [0, 100].
   *
   * @var int
   */
  public $outputVolume;
  /**
   * Output only. Timestamp of when the sample was collected on device.
   *
   * @var string
   */
  public $reportTime;

  /**
   * Output only. Active input device's name.
   *
   * @param string $inputDevice
   */
  public function setInputDevice($inputDevice)
  {
    $this->inputDevice = $inputDevice;
  }
  /**
   * @return string
   */
  public function getInputDevice()
  {
    return $this->inputDevice;
  }
  /**
   * Output only. Active input device's gain in [0, 100].
   *
   * @param int $inputGain
   */
  public function setInputGain($inputGain)
  {
    $this->inputGain = $inputGain;
  }
  /**
   * @return int
   */
  public function getInputGain()
  {
    return $this->inputGain;
  }
  /**
   * Output only. Is active input device mute or not.
   *
   * @param bool $inputMute
   */
  public function setInputMute($inputMute)
  {
    $this->inputMute = $inputMute;
  }
  /**
   * @return bool
   */
  public function getInputMute()
  {
    return $this->inputMute;
  }
  /**
   * Output only. Active output device's name.
   *
   * @param string $outputDevice
   */
  public function setOutputDevice($outputDevice)
  {
    $this->outputDevice = $outputDevice;
  }
  /**
   * @return string
   */
  public function getOutputDevice()
  {
    return $this->outputDevice;
  }
  /**
   * Output only. Is active output device mute or not.
   *
   * @param bool $outputMute
   */
  public function setOutputMute($outputMute)
  {
    $this->outputMute = $outputMute;
  }
  /**
   * @return bool
   */
  public function getOutputMute()
  {
    return $this->outputMute;
  }
  /**
   * Output only. Active output device's volume in [0, 100].
   *
   * @param int $outputVolume
   */
  public function setOutputVolume($outputVolume)
  {
    $this->outputVolume = $outputVolume;
  }
  /**
   * @return int
   */
  public function getOutputVolume()
  {
    return $this->outputVolume;
  }
  /**
   * Output only. Timestamp of when the sample was collected on device.
   *
   * @param string $reportTime
   */
  public function setReportTime($reportTime)
  {
    $this->reportTime = $reportTime;
  }
  /**
   * @return string
   */
  public function getReportTime()
  {
    return $this->reportTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1AudioStatusReport::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1AudioStatusReport');
