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

namespace Google\Service\Slides;

class Page extends \Google\Collection
{
  /**
   * A slide page.
   */
  public const PAGE_TYPE_SLIDE = 'SLIDE';
  /**
   * A master slide page.
   */
  public const PAGE_TYPE_MASTER = 'MASTER';
  /**
   * A layout page.
   */
  public const PAGE_TYPE_LAYOUT = 'LAYOUT';
  /**
   * A notes page.
   */
  public const PAGE_TYPE_NOTES = 'NOTES';
  /**
   * A notes master page.
   */
  public const PAGE_TYPE_NOTES_MASTER = 'NOTES_MASTER';
  protected $collection_key = 'pageElements';
  protected $layoutPropertiesType = LayoutProperties::class;
  protected $layoutPropertiesDataType = '';
  protected $masterPropertiesType = MasterProperties::class;
  protected $masterPropertiesDataType = '';
  protected $notesPropertiesType = NotesProperties::class;
  protected $notesPropertiesDataType = '';
  /**
   * The object ID for this page. Object IDs used by Page and PageElement share
   * the same namespace.
   *
   * @var string
   */
  public $objectId;
  protected $pageElementsType = PageElement::class;
  protected $pageElementsDataType = 'array';
  protected $pagePropertiesType = PageProperties::class;
  protected $pagePropertiesDataType = '';
  /**
   * The type of the page.
   *
   * @var string
   */
  public $pageType;
  /**
   * Output only. The revision ID of the presentation. Can be used in update
   * requests to assert the presentation revision hasn't changed since the last
   * read operation. Only populated if the user has edit access to the
   * presentation. The revision ID is not a sequential number but an opaque
   * string. The format of the revision ID might change over time. A returned
   * revision ID is only guaranteed to be valid for 24 hours after it has been
   * returned and cannot be shared across users. If the revision ID is unchanged
   * between calls, then the presentation has not changed. Conversely, a changed
   * ID (for the same presentation and user) usually means the presentation has
   * been updated. However, a changed ID can also be due to internal factors
   * such as ID format changes.
   *
   * @var string
   */
  public $revisionId;
  protected $slidePropertiesType = SlideProperties::class;
  protected $slidePropertiesDataType = '';

  /**
   * Layout specific properties. Only set if page_type = LAYOUT.
   *
   * @param LayoutProperties $layoutProperties
   */
  public function setLayoutProperties(LayoutProperties $layoutProperties)
  {
    $this->layoutProperties = $layoutProperties;
  }
  /**
   * @return LayoutProperties
   */
  public function getLayoutProperties()
  {
    return $this->layoutProperties;
  }
  /**
   * Master specific properties. Only set if page_type = MASTER.
   *
   * @param MasterProperties $masterProperties
   */
  public function setMasterProperties(MasterProperties $masterProperties)
  {
    $this->masterProperties = $masterProperties;
  }
  /**
   * @return MasterProperties
   */
  public function getMasterProperties()
  {
    return $this->masterProperties;
  }
  /**
   * Notes specific properties. Only set if page_type = NOTES.
   *
   * @param NotesProperties $notesProperties
   */
  public function setNotesProperties(NotesProperties $notesProperties)
  {
    $this->notesProperties = $notesProperties;
  }
  /**
   * @return NotesProperties
   */
  public function getNotesProperties()
  {
    return $this->notesProperties;
  }
  /**
   * The object ID for this page. Object IDs used by Page and PageElement share
   * the same namespace.
   *
   * @param string $objectId
   */
  public function setObjectId($objectId)
  {
    $this->objectId = $objectId;
  }
  /**
   * @return string
   */
  public function getObjectId()
  {
    return $this->objectId;
  }
  /**
   * The page elements rendered on the page.
   *
   * @param PageElement[] $pageElements
   */
  public function setPageElements($pageElements)
  {
    $this->pageElements = $pageElements;
  }
  /**
   * @return PageElement[]
   */
  public function getPageElements()
  {
    return $this->pageElements;
  }
  /**
   * The properties of the page.
   *
   * @param PageProperties $pageProperties
   */
  public function setPageProperties(PageProperties $pageProperties)
  {
    $this->pageProperties = $pageProperties;
  }
  /**
   * @return PageProperties
   */
  public function getPageProperties()
  {
    return $this->pageProperties;
  }
  /**
   * The type of the page.
   *
   * Accepted values: SLIDE, MASTER, LAYOUT, NOTES, NOTES_MASTER
   *
   * @param self::PAGE_TYPE_* $pageType
   */
  public function setPageType($pageType)
  {
    $this->pageType = $pageType;
  }
  /**
   * @return self::PAGE_TYPE_*
   */
  public function getPageType()
  {
    return $this->pageType;
  }
  /**
   * Output only. The revision ID of the presentation. Can be used in update
   * requests to assert the presentation revision hasn't changed since the last
   * read operation. Only populated if the user has edit access to the
   * presentation. The revision ID is not a sequential number but an opaque
   * string. The format of the revision ID might change over time. A returned
   * revision ID is only guaranteed to be valid for 24 hours after it has been
   * returned and cannot be shared across users. If the revision ID is unchanged
   * between calls, then the presentation has not changed. Conversely, a changed
   * ID (for the same presentation and user) usually means the presentation has
   * been updated. However, a changed ID can also be due to internal factors
   * such as ID format changes.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * Slide specific properties. Only set if page_type = SLIDE.
   *
   * @param SlideProperties $slideProperties
   */
  public function setSlideProperties(SlideProperties $slideProperties)
  {
    $this->slideProperties = $slideProperties;
  }
  /**
   * @return SlideProperties
   */
  public function getSlideProperties()
  {
    return $this->slideProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Page::class, 'Google_Service_Slides_Page');
