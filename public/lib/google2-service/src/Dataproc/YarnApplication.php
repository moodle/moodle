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

class YarnApplication extends \Google\Model
{
  /**
   * Status is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Status is NEW.
   */
  public const STATE_NEW = 'NEW';
  /**
   * Status is NEW_SAVING.
   */
  public const STATE_NEW_SAVING = 'NEW_SAVING';
  /**
   * Status is SUBMITTED.
   */
  public const STATE_SUBMITTED = 'SUBMITTED';
  /**
   * Status is ACCEPTED.
   */
  public const STATE_ACCEPTED = 'ACCEPTED';
  /**
   * Status is RUNNING.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * Status is FINISHED.
   */
  public const STATE_FINISHED = 'FINISHED';
  /**
   * Status is FAILED.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Status is KILLED.
   */
  public const STATE_KILLED = 'KILLED';
  /**
   * Optional. The cumulative memory usage of the application for a job,
   * measured in mb-seconds.
   *
   * @var string
   */
  public $memoryMbSeconds;
  /**
   * Required. The application name.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The numerical progress of the application, from 1 to 100.
   *
   * @var float
   */
  public $progress;
  /**
   * Required. The application state.
   *
   * @var string
   */
  public $state;
  /**
   * Optional. The HTTP URL of the ApplicationMaster, HistoryServer, or
   * TimelineServer that provides application-specific information. The URL uses
   * the internal hostname, and requires a proxy server for resolution and,
   * possibly, access.
   *
   * @var string
   */
  public $trackingUrl;
  /**
   * Optional. The cumulative CPU time consumed by the application for a job,
   * measured in vcore-seconds.
   *
   * @var string
   */
  public $vcoreSeconds;

  /**
   * Optional. The cumulative memory usage of the application for a job,
   * measured in mb-seconds.
   *
   * @param string $memoryMbSeconds
   */
  public function setMemoryMbSeconds($memoryMbSeconds)
  {
    $this->memoryMbSeconds = $memoryMbSeconds;
  }
  /**
   * @return string
   */
  public function getMemoryMbSeconds()
  {
    return $this->memoryMbSeconds;
  }
  /**
   * Required. The application name.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. The numerical progress of the application, from 1 to 100.
   *
   * @param float $progress
   */
  public function setProgress($progress)
  {
    $this->progress = $progress;
  }
  /**
   * @return float
   */
  public function getProgress()
  {
    return $this->progress;
  }
  /**
   * Required. The application state.
   *
   * Accepted values: STATE_UNSPECIFIED, NEW, NEW_SAVING, SUBMITTED, ACCEPTED,
   * RUNNING, FINISHED, FAILED, KILLED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Optional. The HTTP URL of the ApplicationMaster, HistoryServer, or
   * TimelineServer that provides application-specific information. The URL uses
   * the internal hostname, and requires a proxy server for resolution and,
   * possibly, access.
   *
   * @param string $trackingUrl
   */
  public function setTrackingUrl($trackingUrl)
  {
    $this->trackingUrl = $trackingUrl;
  }
  /**
   * @return string
   */
  public function getTrackingUrl()
  {
    return $this->trackingUrl;
  }
  /**
   * Optional. The cumulative CPU time consumed by the application for a job,
   * measured in vcore-seconds.
   *
   * @param string $vcoreSeconds
   */
  public function setVcoreSeconds($vcoreSeconds)
  {
    $this->vcoreSeconds = $vcoreSeconds;
  }
  /**
   * @return string
   */
  public function getVcoreSeconds()
  {
    return $this->vcoreSeconds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(YarnApplication::class, 'Google_Service_Dataproc_YarnApplication');
