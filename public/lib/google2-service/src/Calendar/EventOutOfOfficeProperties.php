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

namespace Google\Service\Calendar;

class EventOutOfOfficeProperties extends \Google\Model
{
  /**
   * Whether to decline meeting invitations which overlap Out of office events.
   * Valid values are declineNone, meaning that no meeting invitations are
   * declined; declineAllConflictingInvitations, meaning that all conflicting
   * meeting invitations that conflict with the event are declined; and
   * declineOnlyNewConflictingInvitations, meaning that only new conflicting
   * meeting invitations which arrive while the Out of office event is present
   * are to be declined.
   *
   * @var string
   */
  public $autoDeclineMode;
  /**
   * Response message to set if an existing event or new invitation is
   * automatically declined by Calendar.
   *
   * @var string
   */
  public $declineMessage;

  /**
   * Whether to decline meeting invitations which overlap Out of office events.
   * Valid values are declineNone, meaning that no meeting invitations are
   * declined; declineAllConflictingInvitations, meaning that all conflicting
   * meeting invitations that conflict with the event are declined; and
   * declineOnlyNewConflictingInvitations, meaning that only new conflicting
   * meeting invitations which arrive while the Out of office event is present
   * are to be declined.
   *
   * @param string $autoDeclineMode
   */
  public function setAutoDeclineMode($autoDeclineMode)
  {
    $this->autoDeclineMode = $autoDeclineMode;
  }
  /**
   * @return string
   */
  public function getAutoDeclineMode()
  {
    return $this->autoDeclineMode;
  }
  /**
   * Response message to set if an existing event or new invitation is
   * automatically declined by Calendar.
   *
   * @param string $declineMessage
   */
  public function setDeclineMessage($declineMessage)
  {
    $this->declineMessage = $declineMessage;
  }
  /**
   * @return string
   */
  public function getDeclineMessage()
  {
    return $this->declineMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventOutOfOfficeProperties::class, 'Google_Service_Calendar_EventOutOfOfficeProperties');
