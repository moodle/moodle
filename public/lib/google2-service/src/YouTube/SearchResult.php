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

namespace Google\Service\YouTube;

class SearchResult extends \Google\Model
{
  /**
   * Etag of this resource.
   *
   * @var string
   */
  public $etag;
  protected $idType = ResourceId::class;
  protected $idDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#searchResult".
   *
   * @var string
   */
  public $kind;
  protected $snippetType = SearchResultSnippet::class;
  protected $snippetDataType = '';

  /**
   * Etag of this resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The id object contains information that can be used to uniquely identify
   * the resource that matches the search request.
   *
   * @param ResourceId $id
   */
  public function setId(ResourceId $id)
  {
    $this->id = $id;
  }
  /**
   * @return ResourceId
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#searchResult".
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
   * The snippet object contains basic details about a search result, such as
   * its title or description. For example, if the search result is a video,
   * then the title will be the video's title and the description will be the
   * video's description.
   *
   * @param SearchResultSnippet $snippet
   */
  public function setSnippet(SearchResultSnippet $snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return SearchResultSnippet
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchResult::class, 'Google_Service_YouTube_SearchResult');
