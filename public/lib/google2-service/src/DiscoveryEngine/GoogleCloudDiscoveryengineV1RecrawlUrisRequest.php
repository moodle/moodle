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

class GoogleCloudDiscoveryengineV1RecrawlUrisRequest extends \Google\Collection
{
  protected $collection_key = 'uris';
  /**
   * Optional. Credential id to use for crawling.
   *
   * @var string
   */
  public $siteCredential;
  /**
   * Required. List of URIs to crawl. At most 10K URIs are supported, otherwise
   * an INVALID_ARGUMENT error is thrown. Each URI should match at least one
   * TargetSite in `site_search_engine`.
   *
   * @var string[]
   */
  public $uris;

  /**
   * Optional. Credential id to use for crawling.
   *
   * @param string $siteCredential
   */
  public function setSiteCredential($siteCredential)
  {
    $this->siteCredential = $siteCredential;
  }
  /**
   * @return string
   */
  public function getSiteCredential()
  {
    return $this->siteCredential;
  }
  /**
   * Required. List of URIs to crawl. At most 10K URIs are supported, otherwise
   * an INVALID_ARGUMENT error is thrown. Each URI should match at least one
   * TargetSite in `site_search_engine`.
   *
   * @param string[] $uris
   */
  public function setUris($uris)
  {
    $this->uris = $uris;
  }
  /**
   * @return string[]
   */
  public function getUris()
  {
    return $this->uris;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1RecrawlUrisRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1RecrawlUrisRequest');
