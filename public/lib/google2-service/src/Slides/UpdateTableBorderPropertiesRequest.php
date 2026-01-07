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

class UpdateTableBorderPropertiesRequest extends \Google\Model
{
  /**
   * All borders in the range.
   */
  public const BORDER_POSITION_ALL = 'ALL';
  /**
   * Borders at the bottom of the range.
   */
  public const BORDER_POSITION_BOTTOM = 'BOTTOM';
  /**
   * Borders on the inside of the range.
   */
  public const BORDER_POSITION_INNER = 'INNER';
  /**
   * Horizontal borders on the inside of the range.
   */
  public const BORDER_POSITION_INNER_HORIZONTAL = 'INNER_HORIZONTAL';
  /**
   * Vertical borders on the inside of the range.
   */
  public const BORDER_POSITION_INNER_VERTICAL = 'INNER_VERTICAL';
  /**
   * Borders at the left of the range.
   */
  public const BORDER_POSITION_LEFT = 'LEFT';
  /**
   * Borders along the outside of the range.
   */
  public const BORDER_POSITION_OUTER = 'OUTER';
  /**
   * Borders at the right of the range.
   */
  public const BORDER_POSITION_RIGHT = 'RIGHT';
  /**
   * Borders at the top of the range.
   */
  public const BORDER_POSITION_TOP = 'TOP';
  /**
   * The border position in the table range the updates should apply to. If a
   * border position is not specified, the updates will apply to all borders in
   * the table range.
   *
   * @var string
   */
  public $borderPosition;
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `tableBorderProperties` is implied and should not be specified. A
   * single `"*"` can be used as short-hand for listing every field. For example
   * to update the table border solid fill color, set `fields` to
   * `"tableBorderFill.solidFill.color"`. To reset a property to its default
   * value, include its field name in the field mask but leave the field itself
   * unset.
   *
   * @var string
   */
  public $fields;
  /**
   * The object ID of the table.
   *
   * @var string
   */
  public $objectId;
  protected $tableBorderPropertiesType = TableBorderProperties::class;
  protected $tableBorderPropertiesDataType = '';
  protected $tableRangeType = TableRange::class;
  protected $tableRangeDataType = '';

  /**
   * The border position in the table range the updates should apply to. If a
   * border position is not specified, the updates will apply to all borders in
   * the table range.
   *
   * Accepted values: ALL, BOTTOM, INNER, INNER_HORIZONTAL, INNER_VERTICAL,
   * LEFT, OUTER, RIGHT, TOP
   *
   * @param self::BORDER_POSITION_* $borderPosition
   */
  public function setBorderPosition($borderPosition)
  {
    $this->borderPosition = $borderPosition;
  }
  /**
   * @return self::BORDER_POSITION_*
   */
  public function getBorderPosition()
  {
    return $this->borderPosition;
  }
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `tableBorderProperties` is implied and should not be specified. A
   * single `"*"` can be used as short-hand for listing every field. For example
   * to update the table border solid fill color, set `fields` to
   * `"tableBorderFill.solidFill.color"`. To reset a property to its default
   * value, include its field name in the field mask but leave the field itself
   * unset.
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
   * The object ID of the table.
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
   * The table border properties to update.
   *
   * @param TableBorderProperties $tableBorderProperties
   */
  public function setTableBorderProperties(TableBorderProperties $tableBorderProperties)
  {
    $this->tableBorderProperties = $tableBorderProperties;
  }
  /**
   * @return TableBorderProperties
   */
  public function getTableBorderProperties()
  {
    return $this->tableBorderProperties;
  }
  /**
   * The table range representing the subset of the table to which the updates
   * are applied. If a table range is not specified, the updates will apply to
   * the entire table.
   *
   * @param TableRange $tableRange
   */
  public function setTableRange(TableRange $tableRange)
  {
    $this->tableRange = $tableRange;
  }
  /**
   * @return TableRange
   */
  public function getTableRange()
  {
    return $this->tableRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateTableBorderPropertiesRequest::class, 'Google_Service_Slides_UpdateTableBorderPropertiesRequest');
