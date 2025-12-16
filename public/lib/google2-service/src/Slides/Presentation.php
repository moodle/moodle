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

class Presentation extends \Google\Collection
{
  protected $collection_key = 'slides';
  protected $layoutsType = Page::class;
  protected $layoutsDataType = 'array';
  /**
   * The locale of the presentation, as an IETF BCP 47 language tag.
   *
   * @var string
   */
  public $locale;
  protected $mastersType = Page::class;
  protected $mastersDataType = 'array';
  protected $notesMasterType = Page::class;
  protected $notesMasterDataType = '';
  protected $pageSizeType = Size::class;
  protected $pageSizeDataType = '';
  /**
   * The ID of the presentation.
   *
   * @var string
   */
  public $presentationId;
  /**
   * Output only. The revision ID of the presentation. Can be used in update
   * requests to assert the presentation revision hasn't changed since the last
   * read operation. Only populated if the user has edit access to the
   * presentation. The revision ID is not a sequential number but a nebulous
   * string. The format of the revision ID may change over time, so it should be
   * treated opaquely. A returned revision ID is only guaranteed to be valid for
   * 24 hours after it has been returned and cannot be shared across users. If
   * the revision ID is unchanged between calls, then the presentation has not
   * changed. Conversely, a changed ID (for the same presentation and user)
   * usually means the presentation has been updated. However, a changed ID can
   * also be due to internal factors such as ID format changes.
   *
   * @var string
   */
  public $revisionId;
  protected $slidesType = Page::class;
  protected $slidesDataType = 'array';
  /**
   * The title of the presentation.
   *
   * @var string
   */
  public $title;

  /**
   * The layouts in the presentation. A layout is a template that determines how
   * content is arranged and styled on the slides that inherit from that layout.
   *
   * @param Page[] $layouts
   */
  public function setLayouts($layouts)
  {
    $this->layouts = $layouts;
  }
  /**
   * @return Page[]
   */
  public function getLayouts()
  {
    return $this->layouts;
  }
  /**
   * The locale of the presentation, as an IETF BCP 47 language tag.
   *
   * @param string $locale
   */
  public function setLocale($locale)
  {
    $this->locale = $locale;
  }
  /**
   * @return string
   */
  public function getLocale()
  {
    return $this->locale;
  }
  /**
   * The slide masters in the presentation. A slide master contains all common
   * page elements and the common properties for a set of layouts. They serve
   * three purposes: - Placeholder shapes on a master contain the default text
   * styles and shape properties of all placeholder shapes on pages that use
   * that master. - The master page properties define the common page properties
   * inherited by its layouts. - Any other shapes on the master slide appear on
   * all slides using that master, regardless of their layout.
   *
   * @param Page[] $masters
   */
  public function setMasters($masters)
  {
    $this->masters = $masters;
  }
  /**
   * @return Page[]
   */
  public function getMasters()
  {
    return $this->masters;
  }
  /**
   * The notes master in the presentation. It serves three purposes: -
   * Placeholder shapes on a notes master contain the default text styles and
   * shape properties of all placeholder shapes on notes pages. Specifically, a
   * `SLIDE_IMAGE` placeholder shape contains the slide thumbnail, and a `BODY`
   * placeholder shape contains the speaker notes. - The notes master page
   * properties define the common page properties inherited by all notes pages.
   * - Any other shapes on the notes master appear on all notes pages. The notes
   * master is read-only.
   *
   * @param Page $notesMaster
   */
  public function setNotesMaster(Page $notesMaster)
  {
    $this->notesMaster = $notesMaster;
  }
  /**
   * @return Page
   */
  public function getNotesMaster()
  {
    return $this->notesMaster;
  }
  /**
   * The size of pages in the presentation.
   *
   * @param Size $pageSize
   */
  public function setPageSize(Size $pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return Size
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * The ID of the presentation.
   *
   * @param string $presentationId
   */
  public function setPresentationId($presentationId)
  {
    $this->presentationId = $presentationId;
  }
  /**
   * @return string
   */
  public function getPresentationId()
  {
    return $this->presentationId;
  }
  /**
   * Output only. The revision ID of the presentation. Can be used in update
   * requests to assert the presentation revision hasn't changed since the last
   * read operation. Only populated if the user has edit access to the
   * presentation. The revision ID is not a sequential number but a nebulous
   * string. The format of the revision ID may change over time, so it should be
   * treated opaquely. A returned revision ID is only guaranteed to be valid for
   * 24 hours after it has been returned and cannot be shared across users. If
   * the revision ID is unchanged between calls, then the presentation has not
   * changed. Conversely, a changed ID (for the same presentation and user)
   * usually means the presentation has been updated. However, a changed ID can
   * also be due to internal factors such as ID format changes.
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
   * The slides in the presentation. A slide inherits properties from a slide
   * layout.
   *
   * @param Page[] $slides
   */
  public function setSlides($slides)
  {
    $this->slides = $slides;
  }
  /**
   * @return Page[]
   */
  public function getSlides()
  {
    return $this->slides;
  }
  /**
   * The title of the presentation.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Presentation::class, 'Google_Service_Slides_Presentation');
