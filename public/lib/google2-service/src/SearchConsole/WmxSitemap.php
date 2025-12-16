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

namespace Google\Service\SearchConsole;

class WmxSitemap extends \Google\Collection
{
  public const TYPE_NOT_SITEMAP = 'NOT_SITEMAP';
  public const TYPE_URL_LIST = 'URL_LIST';
  public const TYPE_SITEMAP = 'SITEMAP';
  public const TYPE_RSS_FEED = 'RSS_FEED';
  public const TYPE_ATOM_FEED = 'ATOM_FEED';
  /**
   * Unsupported sitemap types.
   *
   * @deprecated
   */
  public const TYPE_PATTERN_SITEMAP = 'PATTERN_SITEMAP';
  /**
   * @deprecated
   */
  public const TYPE_OCEANFRONT = 'OCEANFRONT';
  protected $collection_key = 'contents';
  protected $contentsType = WmxSitemapContent::class;
  protected $contentsDataType = 'array';
  /**
   * Number of errors in the sitemap. These are issues with the sitemap itself
   * that need to be fixed before it can be processed correctly.
   *
   * @var string
   */
  public $errors;
  /**
   * If true, the sitemap has not been processed.
   *
   * @var bool
   */
  public $isPending;
  /**
   * If true, the sitemap is a collection of sitemaps.
   *
   * @var bool
   */
  public $isSitemapsIndex;
  /**
   * Date & time in which this sitemap was last downloaded. Date format is in
   * RFC 3339 format (yyyy-mm-dd).
   *
   * @var string
   */
  public $lastDownloaded;
  /**
   * Date & time in which this sitemap was submitted. Date format is in RFC 3339
   * format (yyyy-mm-dd).
   *
   * @var string
   */
  public $lastSubmitted;
  /**
   * The url of the sitemap.
   *
   * @var string
   */
  public $path;
  /**
   * The type of the sitemap. For example: `rssFeed`.
   *
   * @var string
   */
  public $type;
  /**
   * Number of warnings for the sitemap. These are generally non-critical issues
   * with URLs in the sitemaps.
   *
   * @var string
   */
  public $warnings;

  /**
   * The various content types in the sitemap.
   *
   * @param WmxSitemapContent[] $contents
   */
  public function setContents($contents)
  {
    $this->contents = $contents;
  }
  /**
   * @return WmxSitemapContent[]
   */
  public function getContents()
  {
    return $this->contents;
  }
  /**
   * Number of errors in the sitemap. These are issues with the sitemap itself
   * that need to be fixed before it can be processed correctly.
   *
   * @param string $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return string
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * If true, the sitemap has not been processed.
   *
   * @param bool $isPending
   */
  public function setIsPending($isPending)
  {
    $this->isPending = $isPending;
  }
  /**
   * @return bool
   */
  public function getIsPending()
  {
    return $this->isPending;
  }
  /**
   * If true, the sitemap is a collection of sitemaps.
   *
   * @param bool $isSitemapsIndex
   */
  public function setIsSitemapsIndex($isSitemapsIndex)
  {
    $this->isSitemapsIndex = $isSitemapsIndex;
  }
  /**
   * @return bool
   */
  public function getIsSitemapsIndex()
  {
    return $this->isSitemapsIndex;
  }
  /**
   * Date & time in which this sitemap was last downloaded. Date format is in
   * RFC 3339 format (yyyy-mm-dd).
   *
   * @param string $lastDownloaded
   */
  public function setLastDownloaded($lastDownloaded)
  {
    $this->lastDownloaded = $lastDownloaded;
  }
  /**
   * @return string
   */
  public function getLastDownloaded()
  {
    return $this->lastDownloaded;
  }
  /**
   * Date & time in which this sitemap was submitted. Date format is in RFC 3339
   * format (yyyy-mm-dd).
   *
   * @param string $lastSubmitted
   */
  public function setLastSubmitted($lastSubmitted)
  {
    $this->lastSubmitted = $lastSubmitted;
  }
  /**
   * @return string
   */
  public function getLastSubmitted()
  {
    return $this->lastSubmitted;
  }
  /**
   * The url of the sitemap.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * The type of the sitemap. For example: `rssFeed`.
   *
   * Accepted values: NOT_SITEMAP, URL_LIST, SITEMAP, RSS_FEED, ATOM_FEED,
   * PATTERN_SITEMAP, OCEANFRONT
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Number of warnings for the sitemap. These are generally non-critical issues
   * with URLs in the sitemaps.
   *
   * @param string $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return string
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WmxSitemap::class, 'Google_Service_SearchConsole_WmxSitemap');
