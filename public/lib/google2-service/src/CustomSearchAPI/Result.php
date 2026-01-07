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

class Result extends \Google\Collection
{
  protected $collection_key = 'labels';
  /**
   * Indicates the ID of Google's cached version of the search result.
   *
   * @var string
   */
  public $cacheId;
  /**
   * An abridged version of this search result’s URL, e.g. www.example.com.
   *
   * @var string
   */
  public $displayLink;
  /**
   * The file format of the search result.
   *
   * @var string
   */
  public $fileFormat;
  /**
   * The URL displayed after the snippet for each search result.
   *
   * @var string
   */
  public $formattedUrl;
  /**
   * The HTML-formatted URL displayed after the snippet for each search result.
   *
   * @var string
   */
  public $htmlFormattedUrl;
  /**
   * The snippet of the search result, in HTML.
   *
   * @var string
   */
  public $htmlSnippet;
  /**
   * The title of the search result, in HTML.
   *
   * @var string
   */
  public $htmlTitle;
  protected $imageType = ResultImage::class;
  protected $imageDataType = '';
  /**
   * A unique identifier for the type of current object. For this API, it is
   * `customsearch#result.`
   *
   * @var string
   */
  public $kind;
  protected $labelsType = ResultLabels::class;
  protected $labelsDataType = 'array';
  /**
   * The full URL to which the search result is pointing, e.g.
   * http://www.example.com/foo/bar.
   *
   * @var string
   */
  public $link;
  /**
   * The MIME type of the search result.
   *
   * @var string
   */
  public $mime;
  /**
   * Contains [PageMap](https://developers.google.com/custom-
   * search/docs/structured_data#pagemaps) information for this search result.
   *
   * @var array[]
   */
  public $pagemap;
  /**
   * The snippet of the search result, in plain text.
   *
   * @var string
   */
  public $snippet;
  /**
   * The title of the search result, in plain text.
   *
   * @var string
   */
  public $title;

  /**
   * Indicates the ID of Google's cached version of the search result.
   *
   * @param string $cacheId
   */
  public function setCacheId($cacheId)
  {
    $this->cacheId = $cacheId;
  }
  /**
   * @return string
   */
  public function getCacheId()
  {
    return $this->cacheId;
  }
  /**
   * An abridged version of this search result’s URL, e.g. www.example.com.
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
   * The file format of the search result.
   *
   * @param string $fileFormat
   */
  public function setFileFormat($fileFormat)
  {
    $this->fileFormat = $fileFormat;
  }
  /**
   * @return string
   */
  public function getFileFormat()
  {
    return $this->fileFormat;
  }
  /**
   * The URL displayed after the snippet for each search result.
   *
   * @param string $formattedUrl
   */
  public function setFormattedUrl($formattedUrl)
  {
    $this->formattedUrl = $formattedUrl;
  }
  /**
   * @return string
   */
  public function getFormattedUrl()
  {
    return $this->formattedUrl;
  }
  /**
   * The HTML-formatted URL displayed after the snippet for each search result.
   *
   * @param string $htmlFormattedUrl
   */
  public function setHtmlFormattedUrl($htmlFormattedUrl)
  {
    $this->htmlFormattedUrl = $htmlFormattedUrl;
  }
  /**
   * @return string
   */
  public function getHtmlFormattedUrl()
  {
    return $this->htmlFormattedUrl;
  }
  /**
   * The snippet of the search result, in HTML.
   *
   * @param string $htmlSnippet
   */
  public function setHtmlSnippet($htmlSnippet)
  {
    $this->htmlSnippet = $htmlSnippet;
  }
  /**
   * @return string
   */
  public function getHtmlSnippet()
  {
    return $this->htmlSnippet;
  }
  /**
   * The title of the search result, in HTML.
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
   * Image belonging to a custom search result.
   *
   * @param ResultImage $image
   */
  public function setImage(ResultImage $image)
  {
    $this->image = $image;
  }
  /**
   * @return ResultImage
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * A unique identifier for the type of current object. For this API, it is
   * `customsearch#result.`
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Encapsulates all information about refinement labels.
   *
   * @param ResultLabels[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return ResultLabels[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The full URL to which the search result is pointing, e.g.
   * http://www.example.com/foo/bar.
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
   * The MIME type of the search result.
   *
   * @param string $mime
   */
  public function setMime($mime)
  {
    $this->mime = $mime;
  }
  /**
   * @return string
   */
  public function getMime()
  {
    return $this->mime;
  }
  /**
   * Contains [PageMap](https://developers.google.com/custom-
   * search/docs/structured_data#pagemaps) information for this search result.
   *
   * @param array[] $pagemap
   */
  public function setPagemap($pagemap)
  {
    $this->pagemap = $pagemap;
  }
  /**
   * @return array[]
   */
  public function getPagemap()
  {
    return $this->pagemap;
  }
  /**
   * The snippet of the search result, in plain text.
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
   * The title of the search result, in plain text.
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
class_alias(Result::class, 'Google_Service_CustomSearchAPI_Result');
