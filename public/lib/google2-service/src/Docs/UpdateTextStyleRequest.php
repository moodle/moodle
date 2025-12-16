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

namespace Google\Service\Docs;

class UpdateTextStyleRequest extends \Google\Model
{
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `text_style` is implied and should not be specified. A single
   * `"*"` can be used as short-hand for listing every field. For example, to
   * update the text style to bold, set `fields` to `"bold"`. To reset a
   * property to its default value, include its field name in the field mask but
   * leave the field itself unset.
   *
   * @var string
   */
  public $fields;
  protected $rangeType = Range::class;
  protected $rangeDataType = '';
  protected $textStyleType = TextStyle::class;
  protected $textStyleDataType = '';

  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `text_style` is implied and should not be specified. A single
   * `"*"` can be used as short-hand for listing every field. For example, to
   * update the text style to bold, set `fields` to `"bold"`. To reset a
   * property to its default value, include its field name in the field mask but
   * leave the field itself unset.
   *
   * @param string $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return string
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * The range of text to style. The range may be extended to include adjacent
   * newlines. If the range fully contains a paragraph belonging to a list, the
   * paragraph's bullet is also updated with the matching text style. Ranges
   * cannot be inserted inside a relative UpdateTextStyleRequest.
   *
   * @param Range $range
   */
  public function setRange(Range $range)
  {
    $this->range = $range;
  }
  /**
   * @return Range
   */
  public function getRange()
  {
    return $this->range;
  }
  /**
   * The styles to set on the text. If the value for a particular style matches
   * that of the parent, that style will be set to inherit. Certain text style
   * changes may cause other changes in order to to mirror the behavior of the
   * Docs editor. See the documentation of TextStyle for more information.
   *
   * @param TextStyle $textStyle
   */
  public function setTextStyle(TextStyle $textStyle)
  {
    $this->textStyle = $textStyle;
  }
  /**
   * @return TextStyle
   */
  public function getTextStyle()
  {
    return $this->textStyle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateTextStyleRequest::class, 'Google_Service_Docs_UpdateTextStyleRequest');
