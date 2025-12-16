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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1SearchResponseSummaryCitation extends \Google\Collection
{
  protected $collection_key = 'sources';
  /**
   * End of the attributed segment, exclusive.
   *
   * @var string
   */
  public $endIndex;
  protected $sourcesType = GoogleCloudDiscoveryengineV1SearchResponseSummaryCitationSource::class;
  protected $sourcesDataType = 'array';
  /**
   * Index indicates the start of the segment, measured in bytes/unicode.
   *
   * @var string
   */
  public $startIndex;

  /**
   * End of the attributed segment, exclusive.
   *
   * @param string $endIndex
   */
  public function setEndIndex($endIndex)
  {
    $this->endIndex = $endIndex;
  }
  /**
   * @return string
   */
  public function getEndIndex()
  {
    return $this->endIndex;
  }
  /**
   * Citation sources for the attributed segment.
   *
   * @param GoogleCloudDiscoveryengineV1SearchResponseSummaryCitationSource[] $sources
   */
  public function setSources($sources)
  {
    $this->sources = $sources;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchResponseSummaryCitationSource[]
   */
  public function getSources()
  {
    return $this->sources;
  }
  /**
   * Index indicates the start of the segment, measured in bytes/unicode.
   *
   * @param string $startIndex
   */
  public function setStartIndex($startIndex)
  {
    $this->startIndex = $startIndex;
  }
  /**
   * @return string
   */
  public function getStartIndex()
  {
    return $this->startIndex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SearchResponseSummaryCitation::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchResponseSummaryCitation');
