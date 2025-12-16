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

class InsertTextRequest extends \Google\Model
{
  protected $cellLocationType = TableCellLocation::class;
  protected $cellLocationDataType = '';
  /**
   * The index where the text will be inserted, in Unicode code units, based on
   * TextElement indexes. The index is zero-based and is computed from the start
   * of the string. The index may be adjusted to prevent insertions inside
   * Unicode grapheme clusters. In these cases, the text will be inserted
   * immediately after the grapheme cluster.
   *
   * @var int
   */
  public $insertionIndex;
  /**
   * The object ID of the shape or table where the text will be inserted.
   *
   * @var string
   */
  public $objectId;
  /**
   * The text to be inserted. Inserting a newline character will implicitly
   * create a new ParagraphMarker at that index. The paragraph style of the new
   * paragraph will be copied from the paragraph at the current insertion index,
   * including lists and bullets. Text styles for inserted text will be
   * determined automatically, generally preserving the styling of neighboring
   * text. In most cases, the text will be added to the TextRun that exists at
   * the insertion index. Some control characters (U+0000-U+0008, U+000C-U+001F)
   * and characters from the Unicode Basic Multilingual Plane Private Use Area
   * (U+E000-U+F8FF) will be stripped out of the inserted text.
   *
   * @var string
   */
  public $text;

  /**
   * The optional table cell location if the text is to be inserted into a table
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
   * The index where the text will be inserted, in Unicode code units, based on
   * TextElement indexes. The index is zero-based and is computed from the start
   * of the string. The index may be adjusted to prevent insertions inside
   * Unicode grapheme clusters. In these cases, the text will be inserted
   * immediately after the grapheme cluster.
   *
   * @param int $insertionIndex
   */
  public function setInsertionIndex($insertionIndex)
  {
    $this->insertionIndex = $insertionIndex;
  }
  /**
   * @return int
   */
  public function getInsertionIndex()
  {
    return $this->insertionIndex;
  }
  /**
   * The object ID of the shape or table where the text will be inserted.
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
   * The text to be inserted. Inserting a newline character will implicitly
   * create a new ParagraphMarker at that index. The paragraph style of the new
   * paragraph will be copied from the paragraph at the current insertion index,
   * including lists and bullets. Text styles for inserted text will be
   * determined automatically, generally preserving the styling of neighboring
   * text. In most cases, the text will be added to the TextRun that exists at
   * the insertion index. Some control characters (U+0000-U+0008, U+000C-U+001F)
   * and characters from the Unicode Basic Multilingual Plane Private Use Area
   * (U+E000-U+F8FF) will be stripped out of the inserted text.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InsertTextRequest::class, 'Google_Service_Slides_InsertTextRequest');
