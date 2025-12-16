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

namespace Google\Service\Books;

class GeolayerdataCommon extends \Google\Model
{
  /**
   * The language of the information url and description.
   *
   * @var string
   */
  public $lang;
  /**
   * The URL for the preview image information.
   *
   * @var string
   */
  public $previewImageUrl;
  /**
   * The description for this location.
   *
   * @var string
   */
  public $snippet;
  /**
   * The URL for information for this location. Ex: wikipedia link.
   *
   * @var string
   */
  public $snippetUrl;
  /**
   * The display title and localized canonical name to use when searching for
   * this entity on Google search.
   *
   * @var string
   */
  public $title;

  /**
   * The language of the information url and description.
   *
   * @param string $lang
   */
  public function setLang($lang)
  {
    $this->lang = $lang;
  }
  /**
   * @return string
   */
  public function getLang()
  {
    return $this->lang;
  }
  /**
   * The URL for the preview image information.
   *
   * @param string $previewImageUrl
   */
  public function setPreviewImageUrl($previewImageUrl)
  {
    $this->previewImageUrl = $previewImageUrl;
  }
  /**
   * @return string
   */
  public function getPreviewImageUrl()
  {
    return $this->previewImageUrl;
  }
  /**
   * The description for this location.
   *
   * @param string $snippet
   */
  public function setSnippet($snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return string
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
  /**
   * The URL for information for this location. Ex: wikipedia link.
   *
   * @param string $snippetUrl
   */
  public function setSnippetUrl($snippetUrl)
  {
    $this->snippetUrl = $snippetUrl;
  }
  /**
   * @return string
   */
  public function getSnippetUrl()
  {
    return $this->snippetUrl;
  }
  /**
   * The display title and localized canonical name to use when searching for
   * this entity on Google search.
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
class_alias(GeolayerdataCommon::class, 'Google_Service_Books_GeolayerdataCommon');
