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

class GoogleAppsCardV1ChipList extends \Google\Collection
{
  /**
   * Don't use. Unspecified.
   */
  public const LAYOUT_LAYOUT_UNSPECIFIED = 'LAYOUT_UNSPECIFIED';
  /**
   * Default value. The chip list wraps to the next line if there isn't enough
   * horizontal space.
   */
  public const LAYOUT_WRAPPED = 'WRAPPED';
  /**
   * The chips scroll horizontally if they don't fit in the available space.
   */
  public const LAYOUT_HORIZONTAL_SCROLLABLE = 'HORIZONTAL_SCROLLABLE';
  protected $collection_key = 'chips';
  protected $chipsType = GoogleAppsCardV1Chip::class;
  protected $chipsDataType = 'array';
  /**
   * Specified chip list layout.
   *
   * @var string
   */
  public $layout;

  /**
   * An array of chips.
   *
   * @param GoogleAppsCardV1Chip[] $chips
   */
  public function setChips($chips)
  {
    $this->chips = $chips;
  }
  /**
   * @return GoogleAppsCardV1Chip[]
   */
  public function getChips()
  {
    return $this->chips;
  }
  /**
   * Specified chip list layout.
   *
   * Accepted values: LAYOUT_UNSPECIFIED, WRAPPED, HORIZONTAL_SCROLLABLE
   *
   * @param self::LAYOUT_* $layout
   */
  public function setLayout($layout)
  {
    $this->layout = $layout;
  }
  /**
   * @return self::LAYOUT_*
   */
  public function getLayout()
  {
    return $this->layout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1ChipList::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1ChipList');
