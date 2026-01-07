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

class DeleteTextRequest extends \Google\Model
{
  protected $cellLocationType = TableCellLocation::class;
  protected $cellLocationDataType = '';
  /**
   * The object ID of the shape or table from which the text will be deleted.
   *
   * @var string
   */
  public $objectId;
  protected $textRangeType = Range::class;
  protected $textRangeDataType = '';

  /**
   * The optional table cell location if the text is to be deleted from a table
   * cell. If present, the object_id must refer to a table.
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
   * The object ID of the shape or table from which the text will be deleted.
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
   * The range of text to delete, based on TextElement indexes. There is always
   * an implicit newline character at the end of a shape's or table cell's text
   * that cannot be deleted. `Range.Type.ALL` will use the correct bounds, but
   * care must be taken when specifying explicit bounds for range types
   * `FROM_START_INDEX` and `FIXED_RANGE`. For example, if the text is "ABC",
   * followed by an implicit newline, then the maximum value is 2 for
   * `text_range.start_index` and 3 for `text_range.end_index`. Deleting text
   * that crosses a paragraph boundary may result in changes to paragraph styles
   * and lists as the two paragraphs are merged. Ranges that include only one
   * code unit of a surrogate pair are expanded to include both code units.
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
class_alias(DeleteTextRequest::class, 'Google_Service_Slides_DeleteTextRequest');
