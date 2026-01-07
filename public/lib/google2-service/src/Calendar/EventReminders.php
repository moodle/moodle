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

class EventReminders extends \Google\Collection
{
  protected $collection_key = 'overrides';
  protected $overridesType = EventReminder::class;
  protected $overridesDataType = 'array';
  /**
   * Whether the default reminders of the calendar apply to the event.
   *
   * @var bool
   */
  public $useDefault;

  /**
   * If the event doesn't use the default reminders, this lists the reminders
   * specific to the event, or, if not set, indicates that no reminders are set
   * for this event. The maximum number of override reminders is 5.
   *
   * @param EventReminder[] $overrides
   */
  public function setOverrides($overrides)
  {
    $this->overrides = $overrides;
  }
  /**
   * @return EventReminder[]
   */
  public function getOverrides()
  {
    return $this->overrides;
  }
  /**
   * Whether the default reminders of the calendar apply to the event.
   *
   * @param bool $useDefault
   */
  public function setUseDefault($useDefault)
  {
    $this->useDefault = $useDefault;
  }
  /**
   * @return bool
   */
  public function getUseDefault()
  {
    return $this->useDefault;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventReminders::class, 'Google_Service_Calendar_EventReminders');
