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

namespace Google\Service\ToolResults;

class FailureDetail extends \Google\Model
{
  /**
   * If the failure was severe because the system (app) under test crashed.
   *
   * @var bool
   */
  public $crashed;
  /**
   * If the device ran out of memory during a test, causing the test to crash.
   *
   * @var bool
   */
  public $deviceOutOfMemory;
  /**
   * If the Roboscript failed to complete successfully, e.g., because a
   * Roboscript action or assertion failed or a Roboscript action could not be
   * matched during the entire crawl.
   *
   * @var bool
   */
  public $failedRoboscript;
  /**
   * If an app is not installed and thus no test can be run with the app. This
   * might be caused by trying to run a test on an unsupported platform.
   *
   * @var bool
   */
  public $notInstalled;
  /**
   * If a native process (including any other than the app) crashed.
   *
   * @var bool
   */
  public $otherNativeCrash;
  /**
   * If the test overran some time limit, and that is why it failed.
   *
   * @var bool
   */
  public $timedOut;
  /**
   * If the robo was unable to crawl the app; perhaps because the app did not
   * start.
   *
   * @var bool
   */
  public $unableToCrawl;

  /**
   * If the failure was severe because the system (app) under test crashed.
   *
   * @param bool $crashed
   */
  public function setCrashed($crashed)
  {
    $this->crashed = $crashed;
  }
  /**
   * @return bool
   */
  public function getCrashed()
  {
    return $this->crashed;
  }
  /**
   * If the device ran out of memory during a test, causing the test to crash.
   *
   * @param bool $deviceOutOfMemory
   */
  public function setDeviceOutOfMemory($deviceOutOfMemory)
  {
    $this->deviceOutOfMemory = $deviceOutOfMemory;
  }
  /**
   * @return bool
   */
  public function getDeviceOutOfMemory()
  {
    return $this->deviceOutOfMemory;
  }
  /**
   * If the Roboscript failed to complete successfully, e.g., because a
   * Roboscript action or assertion failed or a Roboscript action could not be
   * matched during the entire crawl.
   *
   * @param bool $failedRoboscript
   */
  public function setFailedRoboscript($failedRoboscript)
  {
    $this->failedRoboscript = $failedRoboscript;
  }
  /**
   * @return bool
   */
  public function getFailedRoboscript()
  {
    return $this->failedRoboscript;
  }
  /**
   * If an app is not installed and thus no test can be run with the app. This
   * might be caused by trying to run a test on an unsupported platform.
   *
   * @param bool $notInstalled
   */
  public function setNotInstalled($notInstalled)
  {
    $this->notInstalled = $notInstalled;
  }
  /**
   * @return bool
   */
  public function getNotInstalled()
  {
    return $this->notInstalled;
  }
  /**
   * If a native process (including any other than the app) crashed.
   *
   * @param bool $otherNativeCrash
   */
  public function setOtherNativeCrash($otherNativeCrash)
  {
    $this->otherNativeCrash = $otherNativeCrash;
  }
  /**
   * @return bool
   */
  public function getOtherNativeCrash()
  {
    return $this->otherNativeCrash;
  }
  /**
   * If the test overran some time limit, and that is why it failed.
   *
   * @param bool $timedOut
   */
  public function setTimedOut($timedOut)
  {
    $this->timedOut = $timedOut;
  }
  /**
   * @return bool
   */
  public function getTimedOut()
  {
    return $this->timedOut;
  }
  /**
   * If the robo was unable to crawl the app; perhaps because the app did not
   * start.
   *
   * @param bool $unableToCrawl
   */
  public function setUnableToCrawl($unableToCrawl)
  {
    $this->unableToCrawl = $unableToCrawl;
  }
  /**
   * @return bool
   */
  public function getUnableToCrawl()
  {
    return $this->unableToCrawl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FailureDetail::class, 'Google_Service_ToolResults_FailureDetail');
