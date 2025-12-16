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

class UpdateTextStyleRequest extends \Google\Model
{
  protected $cellLocationType = TableCellLocation::class;
  protected $cellLocationDataType = '';
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `style` is implied and should not be specified. A single `"*"` can
   * be used as short-hand for listing every field. For example, to update the
   * text style to bold, set `fields` to `"bold"`. To reset a property to its
   * default value, include its field name in the field mask but leave the field
   * itself unset.
   *
   * @var string
   */
  public $fields;
  /**
   * The object ID of the shape or table with the text to be styled.
   *
   * @var string
   */
  public $objectId;
  protected $styleType = TextStyle::class;
  protected $styleDataType = '';
  protected $textRangeType = Range::class;
  protected $textRangeDataType = '';

  /**
   * The location of the cell in the table containing the text to style. If
   * `object_id` refers to a table, `cell_location` must have a value.
   * Otherwise, it must not.
   *
   * @param TableCellLocation $cellLocation
   */
  public function setCellLocation(TableCellLocation $cellLocation)
  {
    $this->cellLocation = $cellLocation;
  }
  /**
   * @return TableCellLocation
   */
  public function getCellLocation()
  {
    return $this->cellLocation;
  }
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `style` is implied and should not be specified. A single `"*"` can
   * be used as short-hand for listing every field. For example, to update the
   * text style to bold, set `fields` to `"bold"`. To reset a property to its
   * default value, include its field name in the field mask but leave the field
   * itself unset.
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
   * The object ID of the shape or table with the text to be styled.
   *
   * @param string $objectId
   */
  public function setObjectId($objectId)
  {
    $this->objectId = $objectId;
  }
  /**
   * @return string
   */
  public function getObjectId()
  {
    return $this->objectId;
  }
  /**
   * The style(s) to set on the text. If the value for a particular style
   * matches that of the parent, that style will be set to inherit. Certain text
   * style changes may cause other changes meant to mirror the behavior of the
   * Slides editor. See the documentation of TextStyle for more information.
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
   * The range of text to style. The range may be extended to include adjacent
   * newlines. If the range fully contains a paragraph belonging to a list, the
   * paragraph's bullet is also updated with the matching text style.
   *
   * @param Range $textRange
   */
  public function setTextRange(Range $textRange)
  {
    $this->textRange = $textRange;
  }
  /**
   * @return Range
   */
  public function getTextRange()
  {
    return $this->textRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateTextStyleRequest::class, 'Google_Service_Slides_UpdateTextStyleRequest');
