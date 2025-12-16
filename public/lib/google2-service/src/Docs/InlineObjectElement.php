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

class InlineObjectElement extends \Google\Collection
{
  protected $collection_key = 'suggestedInsertionIds';
  /**
   * The ID of the InlineObject this element contains.
   *
   * @var string
   */
  public $inlineObjectId;
  /**
   * The suggested deletion IDs. If empty, then there are no suggested deletions
   * of this content.
   *
   * @var string[]
   */
  public $suggestedDeletionIds;
  /**
   * The suggested insertion IDs. An InlineObjectElement may have multiple
   * insertion IDs if it's a nested suggested change. If empty, then this is not
   * a suggested insertion.
   *
   * @var string[]
   */
  public $suggestedInsertionIds;
  protected $suggestedTextStyleChangesType = SuggestedTextStyle::class;
  protected $suggestedTextStyleChangesDataType = 'map';
  protected $textStyleType = TextStyle::class;
  protected $textStyleDataType = '';

  /**
   * The ID of the InlineObject this element contains.
   *
   * @param string $inlineObjectId
   */
  public function setInlineObjectId($inlineObjectId)
  {
    $this->inlineObjectId = $inlineObjectId;
  }
  /**
   * @return string
   */
  public function getInlineObjectId()
  {
    return $this->inlineObjectId;
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
   * The suggested insertion IDs. An InlineObjectElement may have multiple
   * insertion IDs if it's a nested suggested change. If empty, then this is not
   * a suggested insertion.
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
  /**
   * The suggested text style changes to this InlineObject, keyed by suggestion
   * ID.
   *
   * @param SuggestedTextStyle[] $suggestedTextStyleChanges
   */
  public function setSuggestedTextStyleChanges($suggestedTextStyleChanges)
  {
    $this->suggestedTextStyleChanges = $suggestedTextStyleChanges;
  }
  /**
   * @return SuggestedTextStyle[]
   */
  public function getSuggestedTextStyleChanges()
  {
    return $this->suggestedTextStyleChanges;
  }
  /**
   * The text style of this InlineObjectElement. Similar to text content, like
   * text runs and footnote references, the text style of an inline object
   * element can affect content layout as well as the styling of text inserted
   * next to it.
   *
   * @param TextStyle $textStyle
   */
  public function setTextStyle(TextStyle $textStyle)
  {
    $this->textStyle = $textStyle;
  }
  /**
   * @return TextStyle
   */
  public function getTextStyle()
  {
    return $this->textStyle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InlineObjectElement::class, 'Google_Service_Docs_InlineObjectElement');
