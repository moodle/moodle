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

class UpdateParagraphStyleRequest extends \Google\Model
{
  protected $cellLocationType = TableCellLocation::class;
  protected $cellLocationDataType = '';
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `style` is implied and should not be specified. A single `"*"` can
   * be used as short-hand for listing every field. For example, to update the
   * paragraph alignment, set `fields` to `"alignment"`. To reset a property to
   * its default value, include its field name in the field mask but leave the
   * field itself unset.
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
  protected $styleType = ParagraphStyle::class;
  protected $styleDataType = '';
  protected $textRangeType = Range::class;
  protected $textRangeDataType = '';

  /**
   * The location of the cell in the table containing the paragraph(s) to style.
   * If `object_id` refers to a table, `cell_location` must have a value.
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
   * paragraph alignment, set `fields` to `"alignment"`. To reset a property to
   * its default value, include its field name in the field mask but leave the
   * field itself unset.
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
   * The paragraph's style.
   *
   * @param ParagraphStyle $style
   */
  public function setStyle(ParagraphStyle $style)
  {
    $this->style = $style;
  }
  /**
   * @return ParagraphStyle
   */
  public function getStyle()
  {
    return $this->style;
  }
  /**
   * The range of text containing the paragraph(s) to style.
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
class_alias(UpdateParagraphStyleRequest::class, 'Google_Service_Slides_UpdateParagraphStyleRequest');
