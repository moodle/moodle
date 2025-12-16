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

namespace Google\Service\AlertCenter;

class SensitiveAdminAction extends \Google\Model
{
  /**
   * Email of person who performed the action
   *
   * @var string
   */
  public $actorEmail;
  /**
   * The time at which event occurred
   *
   * @var string
   */
  public $eventTime;
  protected $primaryAdminChangedEventType = PrimaryAdminChangedEvent::class;
  protected $primaryAdminChangedEventDataType = '';
  protected $ssoProfileCreatedEventType = SSOProfileCreatedEvent::class;
  protected $ssoProfileCreatedEventDataType = '';
  protected $ssoProfileDeletedEventType = SSOProfileDeletedEvent::class;
  protected $ssoProfileDeletedEventDataType = '';
  protected $ssoProfileUpdatedEventType = SSOProfileUpdatedEvent::class;
  protected $ssoProfileUpdatedEventDataType = '';
  protected $superAdminPasswordResetEventType = SuperAdminPasswordResetEvent::class;
  protected $superAdminPasswordResetEventDataType = '';

  /**
   * Email of person who performed the action
   *
   * @param string $actorEmail
   */
  public function setActorEmail($actorEmail)
  {
    $this->actorEmail = $actorEmail;
  }
  /**
   * @return string
   */
  public function getActorEmail()
  {
    return $this->actorEmail;
  }
  /**
   * The time at which event occurred
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * Event occurred when primary admin changed in customer's account
   *
   * @param PrimaryAdminChangedEvent $primaryAdminChangedEvent
   */
  public function setPrimaryAdminChangedEvent(PrimaryAdminChangedEvent $primaryAdminChangedEvent)
  {
    $this->primaryAdminChangedEvent = $primaryAdminChangedEvent;
  }
  /**
   * @return PrimaryAdminChangedEvent
   */
  public function getPrimaryAdminChangedEvent()
  {
    return $this->primaryAdminChangedEvent;
  }
  /**
   * Event occurred when SSO Profile created in customer's account
   *
   * @param SSOProfileCreatedEvent $ssoProfileCreatedEvent
   */
  public function setSsoProfileCreatedEvent(SSOProfileCreatedEvent $ssoProfileCreatedEvent)
  {
    $this->ssoProfileCreatedEvent = $ssoProfileCreatedEvent;
  }
  /**
   * @return SSOProfileCreatedEvent
   */
  public function getSsoProfileCreatedEvent()
  {
    return $this->ssoProfileCreatedEvent;
  }
  /**
   * Event occurred when SSO Profile deleted in customer's account
   *
   * @param SSOProfileDeletedEvent $ssoProfileDeletedEvent
   */
  public function setSsoProfileDeletedEvent(SSOProfileDeletedEvent $ssoProfileDeletedEvent)
  {
    $this->ssoProfileDeletedEvent = $ssoProfileDeletedEvent;
  }
  /**
   * @return SSOProfileDeletedEvent
   */
  public function getSsoProfileDeletedEvent()
  {
    return $this->ssoProfileDeletedEvent;
  }
  /**
   * Event occurred when SSO Profile updated in customer's account
   *
   * @param SSOProfileUpdatedEvent $ssoProfileUpdatedEvent
   */
  public function setSsoProfileUpdatedEvent(SSOProfileUpdatedEvent $ssoProfileUpdatedEvent)
  {
    $this->ssoProfileUpdatedEvent = $ssoProfileUpdatedEvent;
  }
  /**
   * @return SSOProfileUpdatedEvent
   */
  public function getSsoProfileUpdatedEvent()
  {
    return $this->ssoProfileUpdatedEvent;
  }
  /**
   * Event occurred when password was reset for super admin in customer's
   * account
   *
   * @param SuperAdminPasswordResetEvent $superAdminPasswordResetEvent
   */
  public function setSuperAdminPasswordResetEvent(SuperAdminPasswordResetEvent $superAdminPasswordResetEvent)
  {
    $this->superAdminPasswordResetEvent = $superAdminPasswordResetEvent;
  }
  /**
   * @return SuperAdminPasswordResetEvent
   */
  public function getSuperAdminPasswordResetEvent()
  {
    return $this->superAdminPasswordResetEvent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SensitiveAdminAction::class, 'Google_Service_AlertCenter_SensitiveAdminAction');
