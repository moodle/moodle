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

namespace Google\Service\AndroidManagement;

class ApplicationEvent extends \Google\Model
{
  /**
   * This value is disallowed.
   */
  public const EVENT_TYPE_APPLICATION_EVENT_TYPE_UNSPECIFIED = 'APPLICATION_EVENT_TYPE_UNSPECIFIED';
  /**
   * The app was installed.
   */
  public const EVENT_TYPE_INSTALLED = 'INSTALLED';
  /**
   * The app was changed, for example, a component was enabled or disabled.
   */
  public const EVENT_TYPE_CHANGED = 'CHANGED';
  /**
   * The app data was cleared.
   */
  public const EVENT_TYPE_DATA_CLEARED = 'DATA_CLEARED';
  /**
   * The app was removed.
   */
  public const EVENT_TYPE_REMOVED = 'REMOVED';
  /**
   * A new version of the app has been installed, replacing the old version.
   */
  public const EVENT_TYPE_REPLACED = 'REPLACED';
  /**
   * The app was restarted.
   */
  public const EVENT_TYPE_RESTARTED = 'RESTARTED';
  /**
   * The app was pinned to the foreground.
   */
  public const EVENT_TYPE_PINNED = 'PINNED';
  /**
   * The app was unpinned.
   */
  public const EVENT_TYPE_UNPINNED = 'UNPINNED';
  /**
   * The creation time of the event.
   *
   * @var string
   */
  public $createTime;
  /**
   * App event type.
   *
   * @var string
   */
  public $eventType;

  /**
   * The creation time of the event.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * App event type.
   *
   * Accepted values: APPLICATION_EVENT_TYPE_UNSPECIFIED, INSTALLED, CHANGED,
   * DATA_CLEARED, REMOVED, REPLACED, RESTARTED, PINNED, UNPINNED
   *
   * @param self::EVENT_TYPE_* $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return self::EVENT_TYPE_*
   */
  public function getEventType()
  {
    return $this->eventType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApplicationEvent::class, 'Google_Service_AndroidManagement_ApplicationEvent');
