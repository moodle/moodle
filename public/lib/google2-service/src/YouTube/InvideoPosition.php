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

namespace Google\Service\YouTube;

class InvideoPosition extends \Google\Model
{
  /**
   * @deprecated
   */
  public const CORNER_POSITION_topLeft = 'topLeft';
  public const CORNER_POSITION_topRight = 'topRight';
  /**
   * @deprecated
   */
  public const CORNER_POSITION_bottomLeft = 'bottomLeft';
  /**
   * @deprecated
   */
  public const CORNER_POSITION_bottomRight = 'bottomRight';
  public const TYPE_corner = 'corner';
  /**
   * Describes in which corner of the video the visual widget will appear.
   *
   * @var string
   */
  public $cornerPosition;
  /**
   * Defines the position type.
   *
   * @var string
   */
  public $type;

  /**
   * Describes in which corner of the video the visual widget will appear.
   *
   * Accepted values: topLeft, topRight, bottomLeft, bottomRight
   *
   * @param self::CORNER_POSITION_* $cornerPosition
   */
  public function setCornerPosition($cornerPosition)
  {
    $this->cornerPosition = $cornerPosition;
  }
  /**
   * @return self::CORNER_POSITION_*
   */
  public function getCornerPosition()
  {
    return $this->cornerPosition;
  }
  /**
   * Defines the position type.
   *
   * Accepted values: corner
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InvideoPosition::class, 'Google_Service_YouTube_InvideoPosition');
