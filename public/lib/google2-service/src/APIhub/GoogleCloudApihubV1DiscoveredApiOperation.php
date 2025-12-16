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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1DiscoveredApiOperation extends \Google\Collection
{
  /**
   * Operation is not classified as known or unknown.
   */
  public const CLASSIFICATION_CLASSIFICATION_UNSPECIFIED = 'CLASSIFICATION_UNSPECIFIED';
  /**
   * Operation has a matched catalog operation.
   */
  public const CLASSIFICATION_KNOWN = 'KNOWN';
  /**
   * Operation does not have a matched catalog operation.
   */
  public const CLASSIFICATION_UNKNOWN = 'UNKNOWN';
  protected $collection_key = 'matchResults';
  /**
   * Output only. The classification of the discovered API operation.
   *
   * @var string
   */
  public $classification;
  /**
   * Optional. The number of occurrences of this API Operation.
   *
   * @var string
   */
  public $count;
  /**
   * Output only. Create time stamp of the discovered API operation in API Hub.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. First seen time stamp
   *
   * @var string
   */
  public $firstSeenTime;
  protected $httpOperationType = GoogleCloudApihubV1HttpOperationDetails::class;
  protected $httpOperationDataType = '';
  /**
   * Optional. Last seen time stamp
   *
   * @var string
   */
  public $lastSeenTime;
  protected $matchResultsType = GoogleCloudApihubV1MatchResult::class;
  protected $matchResultsDataType = 'array';
  /**
   * Identifier. The name of the discovered API Operation. Format: `projects/{pr
   * oject}/locations/{location}/discoveredApiObservations/{discovered_api_obser
   * vation}/discoveredApiOperations/{discovered_api_operation}`
   *
   * @var string
   */
  public $name;
  protected $sourceMetadataType = GoogleCloudApihubV1SourceMetadata::class;
  protected $sourceMetadataDataType = '';
  /**
   * Output only. Update time stamp of the discovered API operation in API Hub.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The classification of the discovered API operation.
   *
   * Accepted values: CLASSIFICATION_UNSPECIFIED, KNOWN, UNKNOWN
   *
   * @param self::CLASSIFICATION_* $classification
   */
  public function setClassification($classification)
  {
    $this->classification = $classification;
  }
  /**
   * @return self::CLASSIFICATION_*
   */
  public function getClassification()
  {
    return $this->classification;
  }
  /**
   * Optional. The number of occurrences of this API Operation.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Output only. Create time stamp of the discovered API operation in API Hub.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. First seen time stamp
   *
   * @param string $firstSeenTime
   */
  public function setFirstSeenTime($firstSeenTime)
  {
    $this->firstSeenTime = $firstSeenTime;
  }
  /**
   * @return string
   */
  public function getFirstSeenTime()
  {
    return $this->firstSeenTime;
  }
  /**
   * Optional. An HTTP Operation.
   *
   * @param GoogleCloudApihubV1HttpOperationDetails $httpOperation
   */
  public function setHttpOperation(GoogleCloudApihubV1HttpOperationDetails $httpOperation)
  {
    $this->httpOperation = $httpOperation;
  }
  /**
   * @return GoogleCloudApihubV1HttpOperationDetails
   */
  public function getHttpOperation()
  {
    return $this->httpOperation;
  }
  /**
   * Optional. Last seen time stamp
   *
   * @param string $lastSeenTime
   */
  public function setLastSeenTime($lastSeenTime)
  {
    $this->lastSeenTime = $lastSeenTime;
  }
  /**
   * @return string
   */
  public function getLastSeenTime()
  {
    return $this->lastSeenTime;
  }
  /**
   * Output only. The list of matched results for the discovered API operation.
   * This will be populated only if the classification is known. The current
   * usecase is for a single match. Keeping it repeated to support multiple
   * matches in future.
   *
   * @param GoogleCloudApihubV1MatchResult[] $matchResults
   */
  public function setMatchResults($matchResults)
  {
    $this->matchResults = $matchResults;
  }
  /**
   * @return GoogleCloudApihubV1MatchResult[]
   */
  public function getMatchResults()
  {
    return $this->matchResults;
  }
  /**
   * Identifier. The name of the discovered API Operation. Format: `projects/{pr
   * oject}/locations/{location}/discoveredApiObservations/{discovered_api_obser
   * vation}/discoveredApiOperations/{discovered_api_operation}`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The metadata of the source from which the api operation was
   * collected.
   *
   * @param GoogleCloudApihubV1SourceMetadata $sourceMetadata
   */
  public function setSourceMetadata(GoogleCloudApihubV1SourceMetadata $sourceMetadata)
  {
    $this->sourceMetadata = $sourceMetadata;
  }
  /**
   * @return GoogleCloudApihubV1SourceMetadata
   */
  public function getSourceMetadata()
  {
    return $this->sourceMetadata;
  }
  /**
   * Output only. Update time stamp of the discovered API operation in API Hub.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1DiscoveredApiOperation::class, 'Google_Service_APIhub_GoogleCloudApihubV1DiscoveredApiOperation');
