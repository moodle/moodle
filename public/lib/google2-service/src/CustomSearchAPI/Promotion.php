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

namespace Google\Service\CustomSearchAPI;

class Promotion extends \Google\Collection
{
  protected $collection_key = 'bodyLines';
  protected $bodyLinesType = PromotionBodyLines::class;
  protected $bodyLinesDataType = 'array';
  /**
   * An abridged version of this search's result URL, e.g. www.example.com.
   *
   * @var string
   */
  public $displayLink;
  /**
   * The title of the promotion, in HTML.
   *
   * @var string
   */
  public $htmlTitle;
  protected $imageType = PromotionImage::class;
  protected $imageDataType = '';
  /**
   * The URL of the promotion.
   *
   * @var string
   */
  public $link;
  /**
   * The title of the promotion.
   *
   * @var string
   */
  public $title;

  /**
   * An array of block objects for this promotion.
   *
   * @param PromotionBodyLines[] $bodyLines
   */
  public function setBodyLines($bodyLines)
  {
    $this->bodyLines = $bodyLines;
  }
  /**
   * @return PromotionBodyLines[]
   */
  public function getBodyLines()
  {
    return $this->bodyLines;
  }
  /**
   * An abridged version of this search's result URL, e.g. www.example.com.
   *
   * @param string $displayLink
   */
  public function setDisplayLink($displayLink)
  {
    $this->displayLink = $displayLink;
  }
  /**
   * @return string
   */
  public function getDisplayLink()
  {
    return $this->displayLink;
  }
  /**
   * The title of the promotion, in HTML.
   *
   * @param string $htmlTitle
   */
  public function setHtmlTitle($htmlTitle)
  {
    $this->htmlTitle = $htmlTitle;
  }
  /**
   * @return string
   */
  public function getHtmlTitle()
  {
    return $this->htmlTitle;
  }
  /**
   * Image belonging to a promotion.
   *
   * @param PromotionImage $image
   */
  public function setImage(PromotionImage $image)
  {
    $this->image = $image;
  }
  /**
   * @return PromotionImage
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * The URL of the promotion.
   *
   * @param string $link
   */
  public function setLink($link)
  {
    $this->link = $link;
  }
  /**
   * @return string
   */
  public function getLink()
  {
    return $this->link;
  }
  /**
   * The title of the promotion.
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
class_alias(Promotion::class, 'Google_Service_CustomSearchAPI_Promotion');
