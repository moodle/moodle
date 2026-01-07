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

namespace Google\Service\Appengine;

class LivenessCheck extends \Google\Model
{
  /**
   * Interval between health checks.
   *
   * @var string
   */
  public $checkInterval;
  /**
   * Number of consecutive failed checks required before considering the VM
   * unhealthy.
   *
   * @var string
   */
  public $failureThreshold;
  /**
   * Host header to send when performing a HTTP Liveness check. Example:
   * "myapp.appspot.com"
   *
   * @var string
   */
  public $host;
  /**
   * The initial delay before starting to execute the checks.
   *
   * @var string
   */
  public $initialDelay;
  /**
   * The request path.
   *
   * @var string
   */
  public $path;
  /**
   * Number of consecutive successful checks required before considering the VM
   * healthy.
   *
   * @var string
   */
  public $successThreshold;
  /**
   * Time before the check is considered failed.
   *
   * @var string
   */
  public $timeout;

  /**
   * Interval between health checks.
   *
   * @param string $checkInterval
   */
  public function setCheckInterval($checkInterval)
  {
    $this->checkInterval = $checkInterval;
  }
  /**
   * @return string
   */
  public function getCheckInterval()
  {
    return $this->checkInterval;
  }
  /**
   * Number of consecutive failed checks required before considering the VM
   * unhealthy.
   *
   * @param string $failureThreshold
   */
  public function setFailureThreshold($failureThreshold)
  {
    $this->failureThreshold = $failureThreshold;
  }
  /**
   * @return string
   */
  public function getFailureThreshold()
  {
    return $this->failureThreshold;
  }
  /**
   * Host header to send when performing a HTTP Liveness check. Example:
   * "myapp.appspot.com"
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * The initial delay before starting to execute the checks.
   *
   * @param string $initialDelay
   */
  public function setInitialDelay($initialDelay)
  {
    $this->initialDelay = $initialDelay;
  }
  /**
   * @return string
   */
  public function getInitialDelay()
  {
    return $this->initialDelay;
  }
  /**
   * The request path.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Number of consecutive successful checks required before considering the VM
   * healthy.
   *
   * @param string $successThreshold
   */
  public function setSuccessThreshold($successThreshold)
  {
    $this->successThreshold = $successThreshold;
  }
  /**
   * @return string
   */
  public function getSuccessThreshold()
  {
    return $this->successThreshold;
  }
  /**
   * Time before the check is considered failed.
   *
   * @param string $timeout
   */
  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
  }
  /**
   * @return string
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LivenessCheck::class, 'Google_Service_Appengine_LivenessCheck');
