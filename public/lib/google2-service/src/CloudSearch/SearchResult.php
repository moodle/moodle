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

namespace Google\Service\CloudSearch;

class SearchResult extends \Google\Collection
{
  protected $collection_key = 'clusteredResults';
  protected $clusteredResultsType = SearchResult::class;
  protected $clusteredResultsDataType = 'array';
  protected $debugInfoType = ResultDebugInfo::class;
  protected $debugInfoDataType = '';
  protected $metadataType = Metadata::class;
  protected $metadataDataType = '';
  protected $snippetType = Snippet::class;
  protected $snippetDataType = '';
  /**
   * Title of the search result.
   *
   * @var string
   */
  public $title;
  /**
   * The URL of the search result. The URL contains a Google redirect to the
   * actual item. This URL is signed and shouldn't be changed.
   *
   * @var string
   */
  public $url;

  /**
   * If source is clustered, provide list of clustered results. There will only
   * be one level of clustered results. If current source is not enabled for
   * clustering, this field will be empty.
   *
   * @param SearchResult[] $clusteredResults
   */
  public function setClusteredResults($clusteredResults)
  {
    $this->clusteredResults = $clusteredResults;
  }
  /**
   * @return SearchResult[]
   */
  public function getClusteredResults()
  {
    return $this->clusteredResults;
  }
  /**
   * Debugging information about this search result.
   *
   * @param ResultDebugInfo $debugInfo
   */
  public function setDebugInfo(ResultDebugInfo $debugInfo)
  {
    $this->debugInfo = $debugInfo;
  }
  /**
   * @return ResultDebugInfo
   */
  public function getDebugInfo()
  {
    return $this->debugInfo;
  }
  /**
   * Metadata of the search result.
   *
   * @param Metadata $metadata
   */
  public function setMetadata(Metadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return Metadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The concatenation of all snippets (summaries) available for this result.
   *
   * @param Snippet $snippet
   */
  public function setSnippet(Snippet $snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return Snippet
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
  /**
   * Title of the search result.
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
   * The URL of the search result. The URL contains a Google redirect to the
   * actual item. This URL is signed and shouldn't be changed.
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
class_alias(SearchResult::class, 'Google_Service_CloudSearch_SearchResult');
