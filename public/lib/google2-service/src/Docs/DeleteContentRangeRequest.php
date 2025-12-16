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

class DeleteContentRangeRequest extends \Google\Model
{
  protected $rangeType = Range::class;
  protected $rangeDataType = '';

  /**
   * The range of content to delete. Deleting text that crosses a paragraph
   * boundary may result in changes to paragraph styles, lists, positioned
   * objects and bookmarks as the two paragraphs are merged. Attempting to
   * delete certain ranges can result in an invalid document structure in which
   * case a 400 bad request error is returned. Some examples of invalid delete
   * requests include: * Deleting one code unit of a surrogate pair. * Deleting
   * the last newline character of a Body, Header, Footer, Footnote, TableCell
   * or TableOfContents. * Deleting the start or end of a Table, TableOfContents
   * or Equation without deleting the entire element. * Deleting the newline
   * character before a Table, TableOfContents or SectionBreak without deleting
   * the element. * Deleting individual rows or cells of a table. Deleting the
   * content within a table cell is allowed.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeleteContentRangeRequest::class, 'Google_Service_Docs_DeleteContentRangeRequest');
