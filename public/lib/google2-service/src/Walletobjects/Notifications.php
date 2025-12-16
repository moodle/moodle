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

namespace Google\Service\Walletobjects;

class Notifications extends \Google\Model
{
  protected $expiryNotificationType = ExpiryNotification::class;
  protected $expiryNotificationDataType = '';
  protected $upcomingNotificationType = UpcomingNotification::class;
  protected $upcomingNotificationDataType = '';

  /**
   * A notification would be triggered at a specific time before the card
   * expires.
   *
   * @param ExpiryNotification $expiryNotification
   */
  public function setExpiryNotification(ExpiryNotification $expiryNotification)
  {
    $this->expiryNotification = $expiryNotification;
  }
  /**
   * @return ExpiryNotification
   */
  public function getExpiryNotification()
  {
    return $this->expiryNotification;
  }
  /**
   * A notification would be triggered at a specific time before the card
   * becomes usable.
   *
   * @param UpcomingNotification $upcomingNotification
   */
  public function setUpcomingNotification(UpcomingNotification $upcomingNotification)
  {
    $this->upcomingNotification = $upcomingNotification;
  }
  /**
   * @return UpcomingNotification
   */
  public function getUpcomingNotification()
  {
    return $this->upcomingNotification;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Notifications::class, 'Google_Service_Walletobjects_Notifications');
