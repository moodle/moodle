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

class UpdateDocumentStyleRequest extends \Google\Model
{
  protected $documentStyleType = DocumentStyle::class;
  protected $documentStyleDataType = '';
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `document_style` is implied and should not be specified. A single
   * `"*"` can be used as short-hand for listing every field. For example to
   * update the background, set `fields` to `"background"`.
   *
   * @var string
   */
  public $fields;
  /**
   * The tab that contains the style to update. When omitted, the request
   * applies to the first tab. In a document containing a single tab: - If
   * provided, must match the singular tab's ID. - If omitted, the request
   * applies to the singular tab. In a document containing multiple tabs: - If
   * provided, the request applies to the specified tab. - If not provided, the
   * request applies to the first tab in the document.
   *
   * @var string
   */
  public $tabId;

  /**
   * The styles to set on the document. Certain document style changes may cause
   * other changes in order to mirror the behavior of the Docs editor. See the
   * documentation of DocumentStyle for more information.
   *
   * @param DocumentStyle $documentStyle
   */
  public function setDocumentStyle(DocumentStyle $documentStyle)
  {
    $this->documentStyle = $documentStyle;
  }
  /**
   * @return DocumentStyle
   */
  public function getDocumentStyle()
  {
    return $this->documentStyle;
  }
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `document_style` is implied and should not be specified. A single
   * `"*"` can be used as short-hand for listing every field. For example to
   * update the background, set `fields` to `"background"`.
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
   * The tab that contains the style to update. When omitted, the request
   * applies to the first tab. In a document containing a single tab: - If
   * provided, must match the singular tab's ID. - If omitted, the request
   * applies to the singular tab. In a document containing multiple tabs: - If
   * provided, the request applies to the specified tab. - If not provided, the
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
class_alias(UpdateDocumentStyleRequest::class, 'Google_Service_Docs_UpdateDocumentStyleRequest');
