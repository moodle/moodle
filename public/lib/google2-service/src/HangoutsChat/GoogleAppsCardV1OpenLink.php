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

namespace Google\Service\HangoutsChat;

class GoogleAppsCardV1OpenLink extends \Google\Model
{
  /**
   * Default value. The card doesn't reload; nothing happens.
   */
  public const ON_CLOSE_NOTHING = 'NOTHING';
  /**
   * Reloads the card after the child window closes. If used in conjunction with
   * [`OpenAs.OVERLAY`](https://developers.google.com/workspace/add-
   * ons/reference/rpc/google.apps.card.v1#openas), the child window acts as a
   * modal dialog and the parent card is blocked until the child window closes.
   */
  public const ON_CLOSE_RELOAD = 'RELOAD';
  /**
   * The link opens as a full-size window (if that's the frame used by the
   * client).
   */
  public const OPEN_AS_FULL_SIZE = 'FULL_SIZE';
  /**
   * The link opens as an overlay, such as a pop-up.
   */
  public const OPEN_AS_OVERLAY = 'OVERLAY';
  /**
   * Whether the client forgets about a link after opening it, or observes it
   * until the window closes. [Google Workspace add-
   * ons](https://developers.google.com/workspace/add-ons):
   *
   * @var string
   */
  public $onClose;
  /**
   * How to open a link. [Google Workspace add-
   * ons](https://developers.google.com/workspace/add-ons):
   *
   * @var string
   */
  public $openAs;
  /**
   * The URL to open. HTTP URLs are converted to HTTPS.
   *
   * @var string
   */
  public $url;

  /**
   * Whether the client forgets about a link after opening it, or observes it
   * until the window closes. [Google Workspace add-
   * ons](https://developers.google.com/workspace/add-ons):
   *
   * Accepted values: NOTHING, RELOAD
   *
   * @param self::ON_CLOSE_* $onClose
   */
  public function setOnClose($onClose)
  {
    $this->onClose = $onClose;
  }
  /**
   * @return self::ON_CLOSE_*
   */
  public function getOnClose()
  {
    return $this->onClose;
  }
  /**
   * How to open a link. [Google Workspace add-
   * ons](https://developers.google.com/workspace/add-ons):
   *
   * Accepted values: FULL_SIZE, OVERLAY
   *
   * @param self::OPEN_AS_* $openAs
   */
  public function setOpenAs($openAs)
  {
    $this->openAs = $openAs;
  }
  /**
   * @return self::OPEN_AS_*
   */
  public function getOpenAs()
  {
    return $this->openAs;
  }
  /**
   * The URL to open. HTTP URLs are converted to HTTPS.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1OpenLink::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1OpenLink');
