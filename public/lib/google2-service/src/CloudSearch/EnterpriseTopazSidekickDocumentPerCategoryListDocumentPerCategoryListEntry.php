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

namespace Google\Service\CloudSearch;

class EnterpriseTopazSidekickDocumentPerCategoryListDocumentPerCategoryListEntry extends \Google\Model
{
  public const CATEGORY_UNKNOWN_DOCUMENT = 'UNKNOWN_DOCUMENT';
  /**
   * @deprecated
   */
  public const CATEGORY_ACTIONABLE = 'ACTIONABLE';
  public const CATEGORY_VIEWED = 'VIEWED';
  /**
   * @deprecated
   */
  public const CATEGORY_REPLIED = 'REPLIED';
  /**
   * Mention categories. The mention has been viewed by the user, but the user
   * has not replied.
   */
  public const CATEGORY_MENTION_VIEWED = 'MENTION_VIEWED';
  /**
   * The user has replied to the mention.
   */
  public const CATEGORY_MENTION_REPLIED = 'MENTION_REPLIED';
  /**
   * The mention has not been viewed by the user.
   */
  public const CATEGORY_MENTION_NOT_VIEWED = 'MENTION_NOT_VIEWED';
  /**
   * Share categories. Consists of documents that have been explicitly shared
   * with the user. Document has been viewed.
   */
  public const CATEGORY_SHARED_AND_VIEWED = 'SHARED_AND_VIEWED';
  /**
   * Document has not been viewed.
   */
  public const CATEGORY_SHARED_NOT_VIEWED = 'SHARED_NOT_VIEWED';
  /**
   * Document has been edited.
   */
  public const CATEGORY_EDITED = 'EDITED';
  /**
   * @var string
   */
  public $category;
  protected $documentType = EnterpriseTopazSidekickCommonDocument::class;
  protected $documentDataType = '';
  /**
   * Reason this document was selected.
   *
   * @var string
   */
  public $rationale;

  /**
   * @param self::CATEGORY_* $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return self::CATEGORY_*
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * @param EnterpriseTopazSidekickCommonDocument $document
   */
  public function setDocument(EnterpriseTopazSidekickCommonDocument $document)
  {
    $this->document = $document;
  }
  /**
   * @return EnterpriseTopazSidekickCommonDocument
   */
  public function getDocument()
  {
    return $this->document;
  }
  /**
   * Reason this document was selected.
   *
   * @param string $rationale
   */
  public function setRationale($rationale)
  {
    $this->rationale = $rationale;
  }
  /**
   * @return string
   */
  public function getRationale()
  {
    return $this->rationale;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickDocumentPerCategoryListDocumentPerCategoryListEntry::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickDocumentPerCategoryListDocumentPerCategoryListEntry');
