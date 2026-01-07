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

namespace Google\Service\Slides;

class Bullet extends \Google\Model
{
  protected $bulletStyleType = TextStyle::class;
  protected $bulletStyleDataType = '';
  /**
   * The rendered bullet glyph for this paragraph.
   *
   * @var string
   */
  public $glyph;
  /**
   * The ID of the list this paragraph belongs to.
   *
   * @var string
   */
  public $listId;
  /**
   * The nesting level of this paragraph in the list.
   *
   * @var int
   */
  public $nestingLevel;

  /**
   * The paragraph specific text style applied to this bullet.
   *
   * @param TextStyle $bulletStyle
   */
  public function setBulletStyle(TextStyle $bulletStyle)
  {
    $this->bulletStyle = $bulletStyle;
  }
  /**
   * @return TextStyle
   */
  public function getBulletStyle()
  {
    return $this->bulletStyle;
  }
  /**
   * The rendered bullet glyph for this paragraph.
   *
   * @param string $glyph
   */
  public function setGlyph($glyph)
  {
    $this->glyph = $glyph;
  }
  /**
   * @return string
   */
  public function getGlyph()
  {
    return $this->glyph;
  }
  /**
   * The ID of the list this paragraph belongs to.
   *
   * @param string $listId
   */
  public function setListId($listId)
  {
    $this->listId = $listId;
  }
  /**
   * @return string
   */
  public function getListId()
  {
    return $this->listId;
  }
  /**
   * The nesting level of this paragraph in the list.
   *
   * @param int $nestingLevel
   */
  public function setNestingLevel($nestingLevel)
  {
    $this->nestingLevel = $nestingLevel;
  }
  /**
   * @return int
   */
  public function getNestingLevel()
  {
    return $this->nestingLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Bullet::class, 'Google_Service_Slides_Bullet');
