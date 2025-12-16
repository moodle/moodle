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

class InsertTextRequest extends \Google\Model
{
  protected $endOfSegmentLocationType = EndOfSegmentLocation::class;
  protected $endOfSegmentLocationDataType = '';
  protected $locationType = Location::class;
  protected $locationDataType = '';
  /**
   * The text to be inserted. Inserting a newline character will implicitly
   * create a new Paragraph at that index. The paragraph style of the new
   * paragraph will be copied from the paragraph at the current insertion index,
   * including lists and bullets. Text styles for inserted text will be
   * determined automatically, generally preserving the styling of neighboring
   * text. In most cases, the text style for the inserted text will match the
   * text immediately before the insertion index. Some control characters
   * (U+0000-U+0008, U+000C-U+001F) and characters from the Unicode Basic
   * Multilingual Plane Private Use Area (U+E000-U+F8FF) will be stripped out of
   * the inserted text.
   *
   * @var string
   */
  public $text;

  /**
   * Inserts the text at the end of a header, footer, footnote or the document
   * body.
   *
   * @param EndOfSegmentLocation $endOfSegmentLocation
   */
  public function setEndOfSegmentLocation(EndOfSegmentLocation $endOfSegmentLocation)
  {
    $this->endOfSegmentLocation = $endOfSegmentLocation;
  }
  /**
   * @return EndOfSegmentLocation
   */
  public function getEndOfSegmentLocation()
  {
    return $this->endOfSegmentLocation;
  }
  /**
   * Inserts the text at a specific index in the document. Text must be inserted
   * inside the bounds of an existing Paragraph. For instance, text cannot be
   * inserted at a table's start index (i.e. between the table and its preceding
   * paragraph). The text must be inserted in the preceding paragraph.
   *
   * @param Location $location
   */
  public function setLocation(Location $location)
  {
    $this->location = $location;
  }
  /**
   * @return Location
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The text to be inserted. Inserting a newline character will implicitly
   * create a new Paragraph at that index. The paragraph style of the new
   * paragraph will be copied from the paragraph at the current insertion index,
   * including lists and bullets. Text styles for inserted text will be
   * determined automatically, generally preserving the styling of neighboring
   * text. In most cases, the text style for the inserted text will match the
   * text immediately before the insertion index. Some control characters
   * (U+0000-U+0008, U+000C-U+001F) and characters from the Unicode Basic
   * Multilingual Plane Private Use Area (U+E000-U+F8FF) will be stripped out of
   * the inserted text.
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
class_alias(InsertTextRequest::class, 'Google_Service_Docs_InsertTextRequest');
