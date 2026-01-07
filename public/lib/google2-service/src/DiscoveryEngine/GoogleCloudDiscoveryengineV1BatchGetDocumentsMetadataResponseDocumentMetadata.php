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

class GoogleCloudDiscoveryengineV1BatchGetDocumentsMetadataResponseDocumentMetadata extends \Google\Model
{
  /**
   * Should never be set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The Document is indexed.
   */
  public const STATE_INDEXED = 'INDEXED';
  /**
   * The Document is not indexed because its URI is not in the TargetSite.
   */
  public const STATE_NOT_IN_TARGET_SITE = 'NOT_IN_TARGET_SITE';
  /**
   * The Document is not indexed.
   */
  public const STATE_NOT_IN_INDEX = 'NOT_IN_INDEX';
  /**
   * The data ingestion source of the Document. Allowed values are: * `batch`:
   * Data ingested via Batch API, e.g., ImportDocuments. * `streaming` Data
   * ingested via Streaming API, e.g., FHIR streaming.
   *
   * @var string
   */
  public $dataIngestionSource;
  /**
   * The timestamp of the last time the Document was last indexed.
   *
   * @var string
   */
  public $lastRefreshedTime;
  protected $matcherValueType = GoogleCloudDiscoveryengineV1BatchGetDocumentsMetadataResponseDocumentMetadataMatcherValue::class;
  protected $matcherValueDataType = '';
  /**
   * The state of the document.
   *
   * @var string
   */
  public $state;

  /**
   * The data ingestion source of the Document. Allowed values are: * `batch`:
   * Data ingested via Batch API, e.g., ImportDocuments. * `streaming` Data
   * ingested via Streaming API, e.g., FHIR streaming.
   *
   * @param string $dataIngestionSource
   */
  public function setDataIngestionSource($dataIngestionSource)
  {
    $this->dataIngestionSource = $dataIngestionSource;
  }
  /**
   * @return string
   */
  public function getDataIngestionSource()
  {
    return $this->dataIngestionSource;
  }
  /**
   * The timestamp of the last time the Document was last indexed.
   *
   * @param string $lastRefreshedTime
   */
  public function setLastRefreshedTime($lastRefreshedTime)
  {
    $this->lastRefreshedTime = $lastRefreshedTime;
  }
  /**
   * @return string
   */
  public function getLastRefreshedTime()
  {
    return $this->lastRefreshedTime;
  }
  /**
   * The value of the matcher that was used to match the Document.
   *
   * @param GoogleCloudDiscoveryengineV1BatchGetDocumentsMetadataResponseDocumentMetadataMatcherValue $matcherValue
   */
  public function setMatcherValue(GoogleCloudDiscoveryengineV1BatchGetDocumentsMetadataResponseDocumentMetadataMatcherValue $matcherValue)
  {
    $this->matcherValue = $matcherValue;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1BatchGetDocumentsMetadataResponseDocumentMetadataMatcherValue
   */
  public function getMatcherValue()
  {
    return $this->matcherValue;
  }
  /**
   * The state of the document.
   *
   * Accepted values: STATE_UNSPECIFIED, INDEXED, NOT_IN_TARGET_SITE,
   * NOT_IN_INDEX
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1BatchGetDocumentsMetadataResponseDocumentMetadata::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1BatchGetDocumentsMetadataResponseDocumentMetadata');
