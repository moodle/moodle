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

class Range extends \Google\Model
{
  /**
   * The zero-based end index of this range, exclusive, in UTF-16 code units. In
   * all current uses, an end index must be provided. This field is an
   * Int32Value in order to accommodate future use cases with open-ended ranges.
   *
   * @var int
   */
  public $endIndex;
  /**
   * The ID of the header, footer, or footnote that this range is contained in.
   * An empty segment ID signifies the document's body.
   *
   * @var string
   */
  public $segmentId;
  /**
   * The zero-based start index of this range, in UTF-16 code units. In all
   * current uses, a start index must be provided. This field is an Int32Value
   * in order to accommodate future use cases with open-ended ranges.
   *
   * @var int
   */
  public $startIndex;
  /**
   * The tab that contains this range. When omitted, the request applies to the
   * first tab. In a document containing a single tab: - If provided, must match
   * the singular tab's ID. - If omitted, the request applies to the singular
   * tab. In a document containing multiple tabs: - If provided, the request
   * applies to the specified tab. - If omitted, the request applies to the
   * first tab in the document.
   *
   * @var string
   */
  public $tabId;

  /**
   * The zero-based end index of this range, exclusive, in UTF-16 code units. In
   * all current uses, an end index must be provided. This field is an
   * Int32Value in order to accommodate future use cases with open-ended ranges.
   *
   * @param int $endIndex
   */
  public function setEndIndex($endIndex)
  {
    $this->endIndex = $endIndex;
  }
  /**
   * @return int
   */
  public function getEndIndex()
  {
    return $this->endIndex;
  }
  /**
   * The ID of the header, footer, or footnote that this range is contained in.
   * An empty segment ID signifies the document's body.
   *
   * @param string $segmentId
   */
  public function setSegmentId($segmentId)
  {
    $this->segmentId = $segmentId;
  }
  /**
   * @return string
   */
  public function getSegmentId()
  {
    return $this->segmentId;
  }
  /**
   * The zero-based start index of this range, in UTF-16 code units. In all
   * current uses, a start index must be provided. This field is an Int32Value
   * in order to accommodate future use cases with open-ended ranges.
   *
   * @param int $startIndex
   */
  public function setStartIndex($startIndex)
  {
    $this->startIndex = $startIndex;
  }
  /**
   * @return int
   */
  public function getStartIndex()
  {
    return $this->startIndex;
  }
  /**
   * The tab that contains this range. When omitted, the request applies to the
   * first tab. In a document containing a single tab: - If provided, must match
   * the singular tab's ID. - If omitted, the request applies to the singular
   * tab. In a document containing multiple tabs: - If provided, the request
   * applies to the specified tab. - If omitted, the request applies to the
   * first tab in the document.
   *
   * @param string $tabId
   */
  public function setTabId($tabId)
  {
    $this->tabId = $tabId;
  }
  /**
   * @return string
   */
  public function getTabId()
  {
    return $this->tabId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Range::class, 'Google_Service_Docs_Range');
