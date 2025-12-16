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

class AutoText extends \Google\Model
{
  /**
   * An unspecified autotext type.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Type for autotext that represents the current slide number.
   */
  public const TYPE_SLIDE_NUMBER = 'SLIDE_NUMBER';
  /**
   * The rendered content of this auto text, if available.
   *
   * @var string
   */
  public $content;
  protected $styleType = TextStyle::class;
  protected $styleDataType = '';
  /**
   * The type of this auto text.
   *
   * @var string
   */
  public $type;

  /**
   * The rendered content of this auto text, if available.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * The styling applied to this auto text.
   *
   * @param TextStyle $style
   */
  public function setStyle(TextStyle $style)
  {
    $this->style = $style;
  }
  /**
   * @return TextStyle
   */
  public function getStyle()
  {
    return $this->style;
  }
  /**
   * The type of this auto text.
   *
   * Accepted values: TYPE_UNSPECIFIED, SLIDE_NUMBER
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
class_alias(AutoText::class, 'Google_Service_Slides_AutoText');
