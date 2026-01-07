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

class PromotionBodyLines extends \Google\Model
{
  /**
   * The block object's text in HTML, if it has text.
   *
   * @var string
   */
  public $htmlTitle;
  /**
   * The anchor text of the block object's link, if it has a link.
   *
   * @var string
   */
  public $link;
  /**
   * The block object's text, if it has text.
   *
   * @var string
   */
  public $title;
  /**
   * The URL of the block object's link, if it has one.
   *
   * @var string
   */
  public $url;

  /**
   * The block object's text in HTML, if it has text.
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
   * The anchor text of the block object's link, if it has a link.
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
   * The block object's text, if it has text.
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
  /**
   * The URL of the block object's link, if it has one.
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
class_alias(PromotionBodyLines::class, 'Google_Service_CustomSearchAPI_PromotionBodyLines');
