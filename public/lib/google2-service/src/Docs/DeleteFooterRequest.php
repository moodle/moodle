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

class DeleteFooterRequest extends \Google\Model
{
  /**
   * The id of the footer to delete. If this footer is defined on DocumentStyle,
   * the reference to this footer is removed, resulting in no footer of that
   * type for the first section of the document. If this footer is defined on a
   * SectionStyle, the reference to this footer is removed and the footer of
   * that type is now continued from the previous section.
   *
   * @var string
   */
  public $footerId;
  /**
   * The tab that contains the footer to delete. When omitted, the request is
   * applied to the first tab. In a document containing a single tab: - If
   * provided, must match the singular tab's ID. - If omitted, the request
   * applies to the singular tab. In a document containing multiple tabs: - If
   * provided, the request applies to the specified tab. - If omitted, the
   * request applies to the first tab in the document.
   *
   * @var string
   */
  public $tabId;

  /**
   * The id of the footer to delete. If this footer is defined on DocumentStyle,
   * the reference to this footer is removed, resulting in no footer of that
   * type for the first section of the document. If this footer is defined on a
   * SectionStyle, the reference to this footer is removed and the footer of
   * that type is now continued from the previous section.
   *
   * @param string $footerId
   */
  public function setFooterId($footerId)
  {
    $this->footerId = $footerId;
  }
  /**
   * @return string
   */
  public function getFooterId()
  {
    return $this->footerId;
  }
  /**
   * The tab that contains the footer to delete. When omitted, the request is
   * applied to the first tab. In a document containing a single tab: - If
   * provided, must match the singular tab's ID. - If omitted, the request
   * applies to the singular tab. In a document containing multiple tabs: - If
   * provided, the request applies to the specified tab. - If omitted, the
   * request applies to the first tab in the document.
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
class_alias(DeleteFooterRequest::class, 'Google_Service_Docs_DeleteFooterRequest');
