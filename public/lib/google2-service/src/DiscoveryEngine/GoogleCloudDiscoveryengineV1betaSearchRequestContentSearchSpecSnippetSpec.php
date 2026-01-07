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

class GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSnippetSpec extends \Google\Model
{
  /**
   * [DEPRECATED] This field is deprecated. To control snippet return, use
   * `return_snippet` field. For backwards compatibility, we will return snippet
   * if max_snippet_count > 0.
   *
   * @deprecated
   * @var int
   */
  public $maxSnippetCount;
  /**
   * [DEPRECATED] This field is deprecated and will have no affect on the
   * snippet.
   *
   * @deprecated
   * @var bool
   */
  public $referenceOnly;
  /**
   * If `true`, then return snippet. If no snippet can be generated, we return
   * "No snippet is available for this page." A `snippet_status` with `SUCCESS`
   * or `NO_SNIPPET_AVAILABLE` will also be returned.
   *
   * @var bool
   */
  public $returnSnippet;

  /**
   * [DEPRECATED] This field is deprecated. To control snippet return, use
   * `return_snippet` field. For backwards compatibility, we will return snippet
   * if max_snippet_count > 0.
   *
   * @deprecated
   * @param int $maxSnippetCount
   */
  public function setMaxSnippetCount($maxSnippetCount)
  {
    $this->maxSnippetCount = $maxSnippetCount;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getMaxSnippetCount()
  {
    return $this->maxSnippetCount;
  }
  /**
   * [DEPRECATED] This field is deprecated and will have no affect on the
   * snippet.
   *
   * @deprecated
   * @param bool $referenceOnly
   */
  public function setReferenceOnly($referenceOnly)
  {
    $this->referenceOnly = $referenceOnly;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getReferenceOnly()
  {
    return $this->referenceOnly;
  }
  /**
   * If `true`, then return snippet. If no snippet can be generated, we return
   * "No snippet is available for this page." A `snippet_status` with `SUCCESS`
   * or `NO_SNIPPET_AVAILABLE` will also be returned.
   *
   * @param bool $returnSnippet
   */
  public function setReturnSnippet($returnSnippet)
  {
    $this->returnSnippet = $returnSnippet;
  }
  /**
   * @return bool
   */
  public function getReturnSnippet()
  {
    return $this->returnSnippet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSnippetSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSnippetSpec');
