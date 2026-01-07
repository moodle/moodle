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

class PageProperties extends \Google\Model
{
  protected $colorSchemeType = ColorScheme::class;
  protected $colorSchemeDataType = '';
  protected $pageBackgroundFillType = PageBackgroundFill::class;
  protected $pageBackgroundFillDataType = '';

  /**
   * The color scheme of the page. If unset, the color scheme is inherited from
   * a parent page. If the page has no parent, the color scheme uses a default
   * Slides color scheme, matching the defaults in the Slides editor. Only the
   * concrete colors of the first 12 ThemeColorTypes are editable. In addition,
   * only the color scheme on `Master` pages can be updated. To update the
   * field, a color scheme containing mappings from all the first 12
   * ThemeColorTypes to their concrete colors must be provided. Colors for the
   * remaining ThemeColorTypes will be ignored.
   *
   * @param ColorScheme $colorScheme
   */
  public function setColorScheme(ColorScheme $colorScheme)
  {
    $this->colorScheme = $colorScheme;
  }
  /**
   * @return ColorScheme
   */
  public function getColorScheme()
  {
    return $this->colorScheme;
  }
  /**
   * The background fill of the page. If unset, the background fill is inherited
   * from a parent page if it exists. If the page has no parent, then the
   * background fill defaults to the corresponding fill in the Slides editor.
   *
   * @param PageBackgroundFill $pageBackgroundFill
   */
  public function setPageBackgroundFill(PageBackgroundFill $pageBackgroundFill)
  {
    $this->pageBackgroundFill = $pageBackgroundFill;
  }
  /**
   * @return PageBackgroundFill
   */
  public function getPageBackgroundFill()
  {
    return $this->pageBackgroundFill;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PageProperties::class, 'Google_Service_Slides_PageProperties');
