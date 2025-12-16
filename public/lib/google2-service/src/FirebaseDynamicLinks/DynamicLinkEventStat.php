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

namespace Google\Service\FirebaseDynamicLinks;

class DynamicLinkEventStat extends \Google\Model
{
  /**
   * Unspecified type.
   */
  public const EVENT_DYNAMIC_LINK_EVENT_UNSPECIFIED = 'DYNAMIC_LINK_EVENT_UNSPECIFIED';
  /**
   * Indicates that an FDL is clicked by users.
   */
  public const EVENT_CLICK = 'CLICK';
  /**
   * Indicates that an FDL redirects users to fallback link.
   */
  public const EVENT_REDIRECT = 'REDIRECT';
  /**
   * Indicates that an FDL triggers an app install from Play store, currently
   * it's impossible to get stats from App store.
   */
  public const EVENT_APP_INSTALL = 'APP_INSTALL';
  /**
   * Indicates that the app is opened for the first time after an install
   * triggered by FDLs
   */
  public const EVENT_APP_FIRST_OPEN = 'APP_FIRST_OPEN';
  /**
   * Indicates that the app is opened via an FDL for non-first time.
   */
  public const EVENT_APP_RE_OPEN = 'APP_RE_OPEN';
  /**
   * Unspecified platform.
   */
  public const PLATFORM_DYNAMIC_LINK_PLATFORM_UNSPECIFIED = 'DYNAMIC_LINK_PLATFORM_UNSPECIFIED';
  /**
   * Represents Android platform. All apps and browsers on Android are classfied
   * in this category.
   */
  public const PLATFORM_ANDROID = 'ANDROID';
  /**
   * Represents iOS platform. All apps and browsers on iOS are classfied in this
   * category.
   */
  public const PLATFORM_IOS = 'IOS';
  /**
   * Represents desktop.
   *
   * @deprecated
   */
  public const PLATFORM_DESKTOP = 'DESKTOP';
  /**
   * Platforms are not categorized as Android/iOS/Destop fall into here.
   */
  public const PLATFORM_OTHER = 'OTHER';
  /**
   * The number of times this event occurred.
   *
   * @var string
   */
  public $count;
  /**
   * Link event.
   *
   * @var string
   */
  public $event;
  /**
   * Requested platform.
   *
   * @var string
   */
  public $platform;

  /**
   * The number of times this event occurred.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Link event.
   *
   * Accepted values: DYNAMIC_LINK_EVENT_UNSPECIFIED, CLICK, REDIRECT,
   * APP_INSTALL, APP_FIRST_OPEN, APP_RE_OPEN
   *
   * @param self::EVENT_* $event
   */
  public function setEvent($event)
  {
    $this->event = $event;
  }
  /**
   * @return self::EVENT_*
   */
  public function getEvent()
  {
    return $this->event;
  }
  /**
   * Requested platform.
   *
   * Accepted values: DYNAMIC_LINK_PLATFORM_UNSPECIFIED, ANDROID, IOS, DESKTOP,
   * OTHER
   *
   * @param self::PLATFORM_* $platform
   */
  public function setPlatform($platform)
  {
    $this->platform = $platform;
  }
  /**
   * @return self::PLATFORM_*
   */
  public function getPlatform()
  {
    return $this->platform;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DynamicLinkEventStat::class, 'Google_Service_FirebaseDynamicLinks_DynamicLinkEventStat');
