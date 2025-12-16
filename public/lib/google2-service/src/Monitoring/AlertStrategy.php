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

namespace Google\Service\Monitoring;

class AlertStrategy extends \Google\Collection
{
  protected $collection_key = 'notificationPrompts';
  /**
   * If an alerting policy that was active has no data for this long, any open
   * incidents will close
   *
   * @var string
   */
  public $autoClose;
  protected $notificationChannelStrategyType = NotificationChannelStrategy::class;
  protected $notificationChannelStrategyDataType = 'array';
  /**
   * For log-based alert policies, the notification prompts is always OPENED.
   * For non log-based alert policies, the notification prompts can be OPENED or
   * OPENED, CLOSED.
   *
   * @var string[]
   */
  public $notificationPrompts;
  protected $notificationRateLimitType = NotificationRateLimit::class;
  protected $notificationRateLimitDataType = '';

  /**
   * If an alerting policy that was active has no data for this long, any open
   * incidents will close
   *
   * @param string $autoClose
   */
  public function setAutoClose($autoClose)
  {
    $this->autoClose = $autoClose;
  }
  /**
   * @return string
   */
  public function getAutoClose()
  {
    return $this->autoClose;
  }
  /**
   * Control how notifications will be sent out, on a per-channel basis.
   *
   * @param NotificationChannelStrategy[] $notificationChannelStrategy
   */
  public function setNotificationChannelStrategy($notificationChannelStrategy)
  {
    $this->notificationChannelStrategy = $notificationChannelStrategy;
  }
  /**
   * @return NotificationChannelStrategy[]
   */
  public function getNotificationChannelStrategy()
  {
    return $this->notificationChannelStrategy;
  }
  /**
   * For log-based alert policies, the notification prompts is always OPENED.
   * For non log-based alert policies, the notification prompts can be OPENED or
   * OPENED, CLOSED.
   *
   * @param string[] $notificationPrompts
   */
  public function setNotificationPrompts($notificationPrompts)
  {
    $this->notificationPrompts = $notificationPrompts;
  }
  /**
   * @return string[]
   */
  public function getNotificationPrompts()
  {
    return $this->notificationPrompts;
  }
  /**
   * Required for log-based alerting policies, i.e. policies with a LogMatch
   * condition.This limit is not implemented for alerting policies that do not
   * have a LogMatch condition.
   *
   * @param NotificationRateLimit $notificationRateLimit
   */
  public function setNotificationRateLimit(NotificationRateLimit $notificationRateLimit)
  {
    $this->notificationRateLimit = $notificationRateLimit;
  }
  /**
   * @return NotificationRateLimit
   */
  public function getNotificationRateLimit()
  {
    return $this->notificationRateLimit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AlertStrategy::class, 'Google_Service_Monitoring_AlertStrategy');
