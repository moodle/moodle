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

class GoogleCloudDiscoveryengineV1SearchResponseSummary extends \Google\Collection
{
  protected $collection_key = 'summarySkippedReasons';
  protected $safetyAttributesType = GoogleCloudDiscoveryengineV1SearchResponseSummarySafetyAttributes::class;
  protected $safetyAttributesDataType = '';
  /**
   * Additional summary-skipped reasons. This provides the reason for ignored
   * cases. If nothing is skipped, this field is not set.
   *
   * @var string[]
   */
  public $summarySkippedReasons;
  /**
   * The summary content.
   *
   * @var string
   */
  public $summaryText;
  protected $summaryWithMetadataType = GoogleCloudDiscoveryengineV1SearchResponseSummarySummaryWithMetadata::class;
  protected $summaryWithMetadataDataType = '';

  /**
   * A collection of Safety Attribute categories and their associated confidence
   * scores.
   *
   * @param GoogleCloudDiscoveryengineV1SearchResponseSummarySafetyAttributes $safetyAttributes
   */
  public function setSafetyAttributes(GoogleCloudDiscoveryengineV1SearchResponseSummarySafetyAttributes $safetyAttributes)
  {
    $this->safetyAttributes = $safetyAttributes;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchResponseSummarySafetyAttributes
   */
  public function getSafetyAttributes()
  {
    return $this->safetyAttributes;
  }
  /**
   * Additional summary-skipped reasons. This provides the reason for ignored
   * cases. If nothing is skipped, this field is not set.
   *
   * @param string[] $summarySkippedReasons
   */
  public function setSummarySkippedReasons($summarySkippedReasons)
  {
    $this->summarySkippedReasons = $summarySkippedReasons;
  }
  /**
   * @return string[]
   */
  public function getSummarySkippedReasons()
  {
    return $this->summarySkippedReasons;
  }
  /**
   * The summary content.
   *
   * @param string $summaryText
   */
  public function setSummaryText($summaryText)
  {
    $this->summaryText = $summaryText;
  }
  /**
   * @return string
   */
  public function getSummaryText()
  {
    return $this->summaryText;
  }
  /**
   * Summary with metadata information.
   *
   * @param GoogleCloudDiscoveryengineV1SearchResponseSummarySummaryWithMetadata $summaryWithMetadata
   */
  public function setSummaryWithMetadata(GoogleCloudDiscoveryengineV1SearchResponseSummarySummaryWithMetadata $summaryWithMetadata)
  {
    $this->summaryWithMetadata = $summaryWithMetadata;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchResponseSummarySummaryWithMetadata
   */
  public function getSummaryWithMetadata()
  {
    return $this->summaryWithMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SearchResponseSummary::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchResponseSummary');
