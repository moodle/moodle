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

class EndOfSegmentLocation extends \Google\Model
{
  /**
   * The ID of the header, footer or footnote the location is in. An empty
   * segment ID signifies the document's body.
   *
   * @var string
   */
  public $segmentId;
  /**
   * The tab that the location is in. When omitted, the request is applied to
   * the first tab. In a document containing a single tab: - If provided, must
   * match the singular tab's ID. - If omitted, the request applies to the
   * singular tab. In a document containing multiple tabs: - If provided, the
   * request applies to the specified tab. - If omitted, the request applies to
   * the first tab in the document.
   *
   * @var string
   */
  public $tabId;

  /**
   * The ID of the header, footer or footnote the location is in. An empty
   * segment ID signifies the document's body.
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
   * The tab that the location is in. When omitted, the request is applied to
   * the first tab. In a document containing a single tab: - If provided, must
   * match the singular tab's ID. - If omitted, the request applies to the
   * singular tab. In a document containing multiple tabs: - If provided, the
   * request applies to the specified tab. - If omitted, the request applies to
   * the first tab in the document.
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
class_alias(EndOfSegmentLocation::class, 'Google_Service_Docs_EndOfSegmentLocation');
