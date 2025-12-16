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

namespace Google\Service\Dfareporting;

class ObaIcon extends \Google\Model
{
  /**
   * URL to redirect to when an OBA icon is clicked.
   *
   * @var string
   */
  public $iconClickThroughUrl;
  /**
   * URL to track click when an OBA icon is clicked.
   *
   * @var string
   */
  public $iconClickTrackingUrl;
  /**
   * URL to track view when an OBA icon is clicked.
   *
   * @var string
   */
  public $iconViewTrackingUrl;
  /**
   * Identifies the industry initiative that the icon supports. For example,
   * AdChoices.
   *
   * @var string
   */
  public $program;
  /**
   * OBA icon resource URL. Campaign Manager only supports image and JavaScript
   * icons. Learn more
   *
   * @var string
   */
  public $resourceUrl;
  protected $sizeType = Size::class;
  protected $sizeDataType = '';
  /**
   * OBA icon x coordinate position. Accepted values are left or right.
   *
   * @var string
   */
  public $xPosition;
  /**
   * OBA icon y coordinate position. Accepted values are top or bottom.
   *
   * @var string
   */
  public $yPosition;

  /**
   * URL to redirect to when an OBA icon is clicked.
   *
   * @param string $iconClickThroughUrl
   */
  public function setIconClickThroughUrl($iconClickThroughUrl)
  {
    $this->iconClickThroughUrl = $iconClickThroughUrl;
  }
  /**
   * @return string
   */
  public function getIconClickThroughUrl()
  {
    return $this->iconClickThroughUrl;
  }
  /**
   * URL to track click when an OBA icon is clicked.
   *
   * @param string $iconClickTrackingUrl
   */
  public function setIconClickTrackingUrl($iconClickTrackingUrl)
  {
    $this->iconClickTrackingUrl = $iconClickTrackingUrl;
  }
  /**
   * @return string
   */
  public function getIconClickTrackingUrl()
  {
    return $this->iconClickTrackingUrl;
  }
  /**
   * URL to track view when an OBA icon is clicked.
   *
   * @param string $iconViewTrackingUrl
   */
  public function setIconViewTrackingUrl($iconViewTrackingUrl)
  {
    $this->iconViewTrackingUrl = $iconViewTrackingUrl;
  }
  /**
   * @return string
   */
  public function getIconViewTrackingUrl()
  {
    return $this->iconViewTrackingUrl;
  }
  /**
   * Identifies the industry initiative that the icon supports. For example,
   * AdChoices.
   *
   * @param string $program
   */
  public function setProgram($program)
  {
    $this->program = $program;
  }
  /**
   * @return string
   */
  public function getProgram()
  {
    return $this->program;
  }
  /**
   * OBA icon resource URL. Campaign Manager only supports image and JavaScript
   * icons. Learn more
   *
   * @param string $resourceUrl
   */
  public function setResourceUrl($resourceUrl)
  {
    $this->resourceUrl = $resourceUrl;
  }
  /**
   * @return string
   */
  public function getResourceUrl()
  {
    return $this->resourceUrl;
  }
  /**
   * OBA icon size.
   *
   * @param Size $size
   */
  public function setSize(Size $size)
  {
    $this->size = $size;
  }
  /**
   * @return Size
   */
  public function getSize()
  {
    return $this->size;
  }
  /**
   * OBA icon x coordinate position. Accepted values are left or right.
   *
   * @param string $xPosition
   */
  public function setXPosition($xPosition)
  {
    $this->xPosition = $xPosition;
  }
  /**
   * @return string
   */
  public function getXPosition()
  {
    return $this->xPosition;
  }
  /**
   * OBA icon y coordinate position. Accepted values are top or bottom.
   *
   * @param string $yPosition
   */
  public function setYPosition($yPosition)
  {
    $this->yPosition = $yPosition;
  }
  /**
   * @return string
   */
  public function getYPosition()
  {
    return $this->yPosition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ObaIcon::class, 'Google_Service_Dfareporting_ObaIcon');
