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

class TableOfContents extends \Google\Collection
{
  protected $collection_key = 'suggestedInsertionIds';
  protected $contentType = StructuralElement::class;
  protected $contentDataType = 'array';
  /**
   * The suggested deletion IDs. If empty, then there are no suggested deletions
   * of this content.
   *
   * @var string[]
   */
  public $suggestedDeletionIds;
  /**
   * The suggested insertion IDs. A TableOfContents may have multiple insertion
   * IDs if it is a nested suggested change. If empty, then this is not a
   * suggested insertion.
   *
   * @var string[]
   */
  public $suggestedInsertionIds;

  /**
   * The content of the table of contents.
   *
   * @param StructuralElement[] $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return StructuralElement[]
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * The suggested deletion IDs. If empty, then there are no suggested deletions
   * of this content.
   *
   * @param string[] $suggestedDeletionIds
   */
  public function setSuggestedDeletionIds($suggestedDeletionIds)
  {
    $this->suggestedDeletionIds = $suggestedDeletionIds;
  }
  /**
   * @return string[]
   */
  public function getSuggestedDeletionIds()
  {
    return $this->suggestedDeletionIds;
  }
  /**
   * The suggested insertion IDs. A TableOfContents may have multiple insertion
   * IDs if it is a nested suggested change. If empty, then this is not a
   * suggested insertion.
   *
   * @param string[] $suggestedInsertionIds
   */
  public function setSuggestedInsertionIds($suggestedInsertionIds)
  {
    $this->suggestedInsertionIds = $suggestedInsertionIds;
  }
  /**
   * @return string[]
   */
  public function getSuggestedInsertionIds()
  {
    return $this->suggestedInsertionIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableOfContents::class, 'Google_Service_Docs_TableOfContents');
