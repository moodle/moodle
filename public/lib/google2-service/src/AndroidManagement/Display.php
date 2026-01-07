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

class Display extends \Google\Model
{
  /**
   * This value is disallowed.
   */
  public const STATE_DISPLAY_STATE_UNSPECIFIED = 'DISPLAY_STATE_UNSPECIFIED';
  /**
   * Display is off.
   */
  public const STATE_OFF = 'OFF';
  /**
   * Display is on.
   */
  public const STATE_ON = 'ON';
  /**
   * Display is dozing in a low power state
   */
  public const STATE_DOZE = 'DOZE';
  /**
   * Display is dozing in a suspended low power state.
   */
  public const STATE_SUSPENDED = 'SUSPENDED';
  /**
   * Display density expressed as dots-per-inch.
   *
   * @var int
   */
  public $density;
  /**
   * Unique display id.
   *
   * @var int
   */
  public $displayId;
  /**
   * Display height in pixels.
   *
   * @var int
   */
  public $height;
  /**
   * Name of the display.
   *
   * @var string
   */
  public $name;
  /**
   * Refresh rate of the display in frames per second.
   *
   * @var int
   */
  public $refreshRate;
  /**
   * State of the display.
   *
   * @var string
   */
  public $state;
  /**
   * Display width in pixels.
   *
   * @var int
   */
  public $width;

  /**
   * Display density expressed as dots-per-inch.
   *
   * @param int $density
   */
  public function setDensity($density)
  {
    $this->density = $density;
  }
  /**
   * @return int
   */
  public function getDensity()
  {
    return $this->density;
  }
  /**
   * Unique display id.
   *
   * @param int $displayId
   */
  public function setDisplayId($displayId)
  {
    $this->displayId = $displayId;
  }
  /**
   * @return int
   */
  public function getDisplayId()
  {
    return $this->displayId;
  }
  /**
   * Display height in pixels.
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
   * Name of the display.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Refresh rate of the display in frames per second.
   *
   * @param int $refreshRate
   */
  public function setRefreshRate($refreshRate)
  {
    $this->refreshRate = $refreshRate;
  }
  /**
   * @return int
   */
  public function getRefreshRate()
  {
    return $this->refreshRate;
  }
  /**
   * State of the display.
   *
   * Accepted values: DISPLAY_STATE_UNSPECIFIED, OFF, ON, DOZE, SUSPENDED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Display width in pixels.
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
class_alias(Display::class, 'Google_Service_AndroidManagement_Display');
