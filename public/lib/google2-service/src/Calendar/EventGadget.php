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

class EventGadget extends \Google\Model
{
  /**
   * The gadget's display mode. Deprecated. Possible values are: - "icon" - The
   * gadget displays next to the event's title in the calendar view.  - "chip" -
   * The gadget displays when the event is clicked.
   *
   * @var string
   */
  public $display;
  /**
   * The gadget's height in pixels. The height must be an integer greater than
   * 0. Optional. Deprecated.
   *
   * @var int
   */
  public $height;
  /**
   * The gadget's icon URL. The URL scheme must be HTTPS. Deprecated.
   *
   * @var string
   */
  public $iconLink;
  /**
   * The gadget's URL. The URL scheme must be HTTPS. Deprecated.
   *
   * @var string
   */
  public $link;
  /**
   * Preferences.
   *
   * @var string[]
   */
  public $preferences;
  /**
   * The gadget's title. Deprecated.
   *
   * @var string
   */
  public $title;
  /**
   * The gadget's type. Deprecated.
   *
   * @var string
   */
  public $type;
  /**
   * The gadget's width in pixels. The width must be an integer greater than 0.
   * Optional. Deprecated.
   *
   * @var int
   */
  public $width;

  /**
   * The gadget's display mode. Deprecated. Possible values are: - "icon" - The
   * gadget displays next to the event's title in the calendar view.  - "chip" -
   * The gadget displays when the event is clicked.
   *
   * @param string $display
   */
  public function setDisplay($display)
  {
    $this->display = $display;
  }
  /**
   * @return string
   */
  public function getDisplay()
  {
    return $this->display;
  }
  /**
   * The gadget's height in pixels. The height must be an integer greater than
   * 0. Optional. Deprecated.
   *
   * @param int $height
   */
  public function setHeight($height)
  {
    $this->height = $height;
  }
  /**
   * @return int
   */
  public function getHeight()
  {
    return $this->height;
  }
  /**
   * The gadget's icon URL. The URL scheme must be HTTPS. Deprecated.
   *
   * @param string $iconLink
   */
  public function setIconLink($iconLink)
  {
    $this->iconLink = $iconLink;
  }
  /**
   * @return string
   */
  public function getIconLink()
  {
    return $this->iconLink;
  }
  /**
   * The gadget's URL. The URL scheme must be HTTPS. Deprecated.
   *
   * @param string $link
   */
  public function setLink($link)
  {
    $this->link = $link;
  }
  /**
   * @return string
   */
  public function getLink()
  {
    return $this->link;
  }
  /**
   * Preferences.
   *
   * @param string[] $preferences
   */
  public function setPreferences($preferences)
  {
    $this->preferences = $preferences;
  }
  /**
   * @return string[]
   */
  public function getPreferences()
  {
    return $this->preferences;
  }
  /**
   * The gadget's title. Deprecated.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * The gadget's type. Deprecated.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The gadget's width in pixels. The width must be an integer greater than 0.
   * Optional. Deprecated.
   *
   * @param int $width
   */
  public function setWidth($width)
  {
    $this->width = $width;
  }
  /**
   * @return int
   */
  public function getWidth()
  {
    return $this->width;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventGadget::class, 'Google_Service_Calendar_EventGadget');
