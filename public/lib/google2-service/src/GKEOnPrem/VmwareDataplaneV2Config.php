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

namespace Google\Service\GKEOnPrem;

class VmwareDataplaneV2Config extends \Google\Model
{
  /**
   * Enable advanced networking which requires dataplane_v2_enabled to be set
   * true.
   *
   * @var bool
   */
  public $advancedNetworking;
  /**
   * Enables Dataplane V2.
   *
   * @var bool
   */
  public $dataplaneV2Enabled;
  /**
   * Configure ForwardMode for Dataplane v2.
   *
   * @var string
   */
  public $forwardMode;
  /**
   * Enable Dataplane V2 for clusters with Windows nodes.
   *
   * @var bool
   */
  public $windowsDataplaneV2Enabled;

  /**
   * Enable advanced networking which requires dataplane_v2_enabled to be set
   * true.
   *
   * @param bool $advancedNetworking
   */
  public function setAdvancedNetworking($advancedNetworking)
  {
    $this->advancedNetworking = $advancedNetworking;
  }
  /**
   * @return bool
   */
  public function getAdvancedNetworking()
  {
    return $this->advancedNetworking;
  }
  /**
   * Enables Dataplane V2.
   *
   * @param bool $dataplaneV2Enabled
   */
  public function setDataplaneV2Enabled($dataplaneV2Enabled)
  {
    $this->dataplaneV2Enabled = $dataplaneV2Enabled;
  }
  /**
   * @return bool
   */
  public function getDataplaneV2Enabled()
  {
    return $this->dataplaneV2Enabled;
  }
  /**
   * Configure ForwardMode for Dataplane v2.
   *
   * @param string $forwardMode
   */
  public function setForwardMode($forwardMode)
  {
    $this->forwardMode = $forwardMode;
  }
  /**
   * @return string
   */
  public function getForwardMode()
  {
    return $this->forwardMode;
  }
  /**
   * Enable Dataplane V2 for clusters with Windows nodes.
   *
   * @param bool $windowsDataplaneV2Enabled
   */
  public function setWindowsDataplaneV2Enabled($windowsDataplaneV2Enabled)
  {
    $this->windowsDataplaneV2Enabled = $windowsDataplaneV2Enabled;
  }
  /**
   * @return bool
   */
  public function getWindowsDataplaneV2Enabled()
  {
    return $this->windowsDataplaneV2Enabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareDataplaneV2Config::class, 'Google_Service_GKEOnPrem_VmwareDataplaneV2Config');
