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

class Link extends \Google\Model
{
  /**
   * An unspecified relative slide link.
   */
  public const RELATIVE_LINK_RELATIVE_SLIDE_LINK_UNSPECIFIED = 'RELATIVE_SLIDE_LINK_UNSPECIFIED';
  /**
   * A link to the next slide.
   */
  public const RELATIVE_LINK_NEXT_SLIDE = 'NEXT_SLIDE';
  /**
   * A link to the previous slide.
   */
  public const RELATIVE_LINK_PREVIOUS_SLIDE = 'PREVIOUS_SLIDE';
  /**
   * A link to the first slide in the presentation.
   */
  public const RELATIVE_LINK_FIRST_SLIDE = 'FIRST_SLIDE';
  /**
   * A link to the last slide in the presentation.
   */
  public const RELATIVE_LINK_LAST_SLIDE = 'LAST_SLIDE';
  /**
   * If set, indicates this is a link to the specific page in this presentation
   * with this ID. A page with this ID may not exist.
   *
   * @var string
   */
  public $pageObjectId;
  /**
   * If set, indicates this is a link to a slide in this presentation, addressed
   * by its position.
   *
   * @var string
   */
  public $relativeLink;
  /**
   * If set, indicates this is a link to the slide at this zero-based index in
   * the presentation. There may not be a slide at this index.
   *
   * @var int
   */
  public $slideIndex;
  /**
   * If set, indicates this is a link to the external web page at this URL.
   *
   * @var string
   */
  public $url;

  /**
   * If set, indicates this is a link to the specific page in this presentation
   * with this ID. A page with this ID may not exist.
   *
   * @param string $pageObjectId
   */
  public function setPageObjectId($pageObjectId)
  {
    $this->pageObjectId = $pageObjectId;
  }
  /**
   * @return string
   */
  public function getPageObjectId()
  {
    return $this->pageObjectId;
  }
  /**
   * If set, indicates this is a link to a slide in this presentation, addressed
   * by its position.
   *
   * Accepted values: RELATIVE_SLIDE_LINK_UNSPECIFIED, NEXT_SLIDE,
   * PREVIOUS_SLIDE, FIRST_SLIDE, LAST_SLIDE
   *
   * @param self::RELATIVE_LINK_* $relativeLink
   */
  public function setRelativeLink($relativeLink)
  {
    $this->relativeLink = $relativeLink;
  }
  /**
   * @return self::RELATIVE_LINK_*
   */
  public function getRelativeLink()
  {
    return $this->relativeLink;
  }
  /**
   * If set, indicates this is a link to the slide at this zero-based index in
   * the presentation. There may not be a slide at this index.
   *
   * @param int $slideIndex
   */
  public function setSlideIndex($slideIndex)
  {
    $this->slideIndex = $slideIndex;
  }
  /**
   * @return int
   */
  public function getSlideIndex()
  {
    return $this->slideIndex;
  }
  /**
   * If set, indicates this is a link to the external web page at this URL.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Link::class, 'Google_Service_Slides_Link');
