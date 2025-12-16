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

class GoogleCloudDiscoveryengineV1SearchResponseSummarySummaryWithMetadata extends \Google\Collection
{
  protected $collection_key = 'references';
  protected $citationMetadataType = GoogleCloudDiscoveryengineV1SearchResponseSummaryCitationMetadata::class;
  protected $citationMetadataDataType = '';
  protected $referencesType = GoogleCloudDiscoveryengineV1SearchResponseSummaryReference::class;
  protected $referencesDataType = 'array';
  /**
   * Summary text with no citation information.
   *
   * @var string
   */
  public $summary;

  /**
   * Citation metadata for given summary.
   *
   * @param GoogleCloudDiscoveryengineV1SearchResponseSummaryCitationMetadata $citationMetadata
   */
  public function setCitationMetadata(GoogleCloudDiscoveryengineV1SearchResponseSummaryCitationMetadata $citationMetadata)
  {
    $this->citationMetadata = $citationMetadata;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchResponseSummaryCitationMetadata
   */
  public function getCitationMetadata()
  {
    return $this->citationMetadata;
  }
  /**
   * Document References.
   *
   * @param GoogleCloudDiscoveryengineV1SearchResponseSummaryReference[] $references
   */
  public function setReferences($references)
  {
    $this->references = $references;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchResponseSummaryReference[]
   */
  public function getReferences()
  {
    return $this->references;
  }
  /**
   * Summary text with no citation information.
   *
   * @param string $summary
   */
  public function setSummary($summary)
  {
    $this->summary = $summary;
  }
  /**
   * @return string
   */
  public function getSummary()
  {
    return $this->summary;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SearchResponseSummarySummaryWithMetadata::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchResponseSummarySummaryWithMetadata');
