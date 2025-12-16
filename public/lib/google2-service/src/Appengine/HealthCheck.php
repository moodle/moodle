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

class HealthCheck extends \Google\Model
{
  /**
   * Interval between health checks.
   *
   * @var string
   */
  public $checkInterval;
  /**
   * Whether to explicitly disable health checks for this instance.
   *
   * @var bool
   */
  public $disableHealthCheck;
  /**
   * Number of consecutive successful health checks required before receiving
   * traffic.
   *
   * @var string
   */
  public $healthyThreshold;
  /**
   * Host header to send when performing an HTTP health check. Example:
   * "myapp.appspot.com"
   *
   * @var string
   */
  public $host;
  /**
   * Number of consecutive failed health checks required before an instance is
   * restarted.
   *
   * @var string
   */
  public $restartThreshold;
  /**
   * Time before the health check is considered failed.
   *
   * @var string
   */
  public $timeout;
  /**
   * Number of consecutive failed health checks required before removing
   * traffic.
   *
   * @var string
   */
  public $unhealthyThreshold;

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
   * Whether to explicitly disable health checks for this instance.
   *
   * @param bool $disableHealthCheck
   */
  public function setDisableHealthCheck($disableHealthCheck)
  {
    $this->disableHealthCheck = $disableHealthCheck;
  }
  /**
   * @return bool
   */
  public function getDisableHealthCheck()
  {
    return $this->disableHealthCheck;
  }
  /**
   * Number of consecutive successful health checks required before receiving
   * traffic.
   *
   * @param string $healthyThreshold
   */
  public function setHealthyThreshold($healthyThreshold)
  {
    $this->healthyThreshold = $healthyThreshold;
  }
  /**
   * @return string
   */
  public function getHealthyThreshold()
  {
    return $this->healthyThreshold;
  }
  /**
   * Host header to send when performing an HTTP health check. Example:
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
   * Number of consecutive failed health checks required before an instance is
   * restarted.
   *
   * @param string $restartThreshold
   */
  public function setRestartThreshold($restartThreshold)
  {
    $this->restartThreshold = $restartThreshold;
  }
  /**
   * @return string
   */
  public function getRestartThreshold()
  {
    return $this->restartThreshold;
  }
  /**
   * Time before the health check is considered failed.
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
  /**
   * Number of consecutive failed health checks required before removing
   * traffic.
   *
   * @param string $unhealthyThreshold
   */
  public function setUnhealthyThreshold($unhealthyThreshold)
  {
    $this->unhealthyThreshold = $unhealthyThreshold;
  }
  /**
   * @return string
   */
  public function getUnhealthyThreshold()
  {
    return $this->unhealthyThreshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HealthCheck::class, 'Google_Service_Appengine_HealthCheck');
