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

class GoogleCloudDiscoveryengineV1betaReply extends \Google\Collection
{
  protected $collection_key = 'references';
  protected $referencesType = GoogleCloudDiscoveryengineV1betaReplyReference::class;
  protected $referencesDataType = 'array';
  /**
   * @var string
   */
  public $reply;
  protected $summaryType = GoogleCloudDiscoveryengineV1betaSearchResponseSummary::class;
  protected $summaryDataType = '';

  /**
   * @param GoogleCloudDiscoveryengineV1betaReplyReference[]
   */
  public function setReferences($references)
  {
    $this->references = $references;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaReplyReference[]
   */
  public function getReferences()
  {
    return $this->references;
  }
  /**
   * @param string
   */
  public function setReply($reply)
  {
    $this->reply = $reply;
  }
  /**
   * @return string
   */
  public function getReply()
  {
    return $this->reply;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaSearchResponseSummary
   */
  public function setSummary(GoogleCloudDiscoveryengineV1betaSearchResponseSummary $summary)
  {
    $this->summary = $summary;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchResponseSummary
   */
  public function getSummary()
  {
    return $this->summary;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaReply::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaReply');
