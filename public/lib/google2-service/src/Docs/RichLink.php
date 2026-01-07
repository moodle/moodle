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

class RichLink extends \Google\Collection
{
  protected $collection_key = 'suggestedInsertionIds';
  /**
   * Output only. The ID of this link.
   *
   * @var string
   */
  public $richLinkId;
  protected $richLinkPropertiesType = RichLinkProperties::class;
  protected $richLinkPropertiesDataType = '';
  /**
   * IDs for suggestions that remove this link from the document. A RichLink
   * might have multiple deletion IDs if, for example, multiple users suggest
   * deleting it. If empty, then this person link isn't suggested for deletion.
   *
   * @var string[]
   */
  public $suggestedDeletionIds;
  /**
   * IDs for suggestions that insert this link into the document. A RichLink
   * might have multiple insertion IDs if it's a nested suggested change (a
   * suggestion within a suggestion made by a different user, for example). If
   * empty, then this person link isn't a suggested insertion.
   *
   * @var string[]
   */
  public $suggestedInsertionIds;
  protected $suggestedTextStyleChangesType = SuggestedTextStyle::class;
  protected $suggestedTextStyleChangesDataType = 'map';
  protected $textStyleType = TextStyle::class;
  protected $textStyleDataType = '';

  /**
   * Output only. The ID of this link.
   *
   * @param string $richLinkId
   */
  public function setRichLinkId($richLinkId)
  {
    $this->richLinkId = $richLinkId;
  }
  /**
   * @return string
   */
  public function getRichLinkId()
  {
    return $this->richLinkId;
  }
  /**
   * Output only. The properties of this RichLink. This field is always present.
   *
   * @param RichLinkProperties $richLinkProperties
   */
  public function setRichLinkProperties(RichLinkProperties $richLinkProperties)
  {
    $this->richLinkProperties = $richLinkProperties;
  }
  /**
   * @return RichLinkProperties
   */
  public function getRichLinkProperties()
  {
    return $this->richLinkProperties;
  }
  /**
   * IDs for suggestions that remove this link from the document. A RichLink
   * might have multiple deletion IDs if, for example, multiple users suggest
   * deleting it. If empty, then this person link isn't suggested for deletion.
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
   * IDs for suggestions that insert this link into the document. A RichLink
   * might have multiple insertion IDs if it's a nested suggested change (a
   * suggestion within a suggestion made by a different user, for example). If
   * empty, then this person link isn't a suggested insertion.
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
   * The suggested text style changes to this RichLink, keyed by suggestion ID.
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
   * The text style of this RichLink.
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
class_alias(RichLink::class, 'Google_Service_Docs_RichLink');
