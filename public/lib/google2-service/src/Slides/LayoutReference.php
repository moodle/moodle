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

class LayoutReference extends \Google\Model
{
  /**
   * Unspecified layout.
   */
  public const PREDEFINED_LAYOUT_PREDEFINED_LAYOUT_UNSPECIFIED = 'PREDEFINED_LAYOUT_UNSPECIFIED';
  /**
   * Blank layout, with no placeholders.
   */
  public const PREDEFINED_LAYOUT_BLANK = 'BLANK';
  /**
   * Layout with a caption at the bottom.
   */
  public const PREDEFINED_LAYOUT_CAPTION_ONLY = 'CAPTION_ONLY';
  /**
   * Layout with a title and a subtitle.
   */
  public const PREDEFINED_LAYOUT_TITLE = 'TITLE';
  /**
   * Layout with a title and body.
   */
  public const PREDEFINED_LAYOUT_TITLE_AND_BODY = 'TITLE_AND_BODY';
  /**
   * Layout with a title and two columns.
   */
  public const PREDEFINED_LAYOUT_TITLE_AND_TWO_COLUMNS = 'TITLE_AND_TWO_COLUMNS';
  /**
   * Layout with only a title.
   */
  public const PREDEFINED_LAYOUT_TITLE_ONLY = 'TITLE_ONLY';
  /**
   * Layout with a section title.
   */
  public const PREDEFINED_LAYOUT_SECTION_HEADER = 'SECTION_HEADER';
  /**
   * Layout with a title and subtitle on one side and description on the other.
   */
  public const PREDEFINED_LAYOUT_SECTION_TITLE_AND_DESCRIPTION = 'SECTION_TITLE_AND_DESCRIPTION';
  /**
   * Layout with one title and one body, arranged in a single column.
   */
  public const PREDEFINED_LAYOUT_ONE_COLUMN_TEXT = 'ONE_COLUMN_TEXT';
  /**
   * Layout with a main point.
   */
  public const PREDEFINED_LAYOUT_MAIN_POINT = 'MAIN_POINT';
  /**
   * Layout with a big number heading.
   */
  public const PREDEFINED_LAYOUT_BIG_NUMBER = 'BIG_NUMBER';
  /**
   * Layout ID: the object ID of one of the layouts in the presentation.
   *
   * @var string
   */
  public $layoutId;
  /**
   * Predefined layout.
   *
   * @var string
   */
  public $predefinedLayout;

  /**
   * Layout ID: the object ID of one of the layouts in the presentation.
   *
   * @param string $layoutId
   */
  public function setLayoutId($layoutId)
  {
    $this->layoutId = $layoutId;
  }
  /**
   * @return string
   */
  public function getLayoutId()
  {
    return $this->layoutId;
  }
  /**
   * Predefined layout.
   *
   * Accepted values: PREDEFINED_LAYOUT_UNSPECIFIED, BLANK, CAPTION_ONLY, TITLE,
   * TITLE_AND_BODY, TITLE_AND_TWO_COLUMNS, TITLE_ONLY, SECTION_HEADER,
   * SECTION_TITLE_AND_DESCRIPTION, ONE_COLUMN_TEXT, MAIN_POINT, BIG_NUMBER
   *
   * @param self::PREDEFINED_LAYOUT_* $predefinedLayout
   */
  public function setPredefinedLayout($predefinedLayout)
  {
    $this->predefinedLayout = $predefinedLayout;
  }
  /**
   * @return self::PREDEFINED_LAYOUT_*
   */
  public function getPredefinedLayout()
  {
    return $this->predefinedLayout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LayoutReference::class, 'Google_Service_Slides_LayoutReference');
