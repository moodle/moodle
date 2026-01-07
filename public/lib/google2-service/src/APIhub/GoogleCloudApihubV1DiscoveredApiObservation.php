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

class GoogleCloudApihubV1DiscoveredApiObservation extends \Google\Collection
{
  /**
   * Unknown style
   */
  public const STYLE_STYLE_UNSPECIFIED = 'STYLE_UNSPECIFIED';
  /**
   * Style is Rest API
   */
  public const STYLE_REST = 'REST';
  /**
   * Style is Grpc API
   */
  public const STYLE_GRPC = 'GRPC';
  /**
   * Style is GraphQL API
   */
  public const STYLE_GRAPHQL = 'GRAPHQL';
  protected $collection_key = 'sourceTypes';
  /**
   * Optional. The number of observed API Operations.
   *
   * @var string
   */
  public $apiOperationCount;
  /**
   * Output only. Create time stamp of the observation in API Hub.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The hostname of requests processed for this Observation.
   *
   * @var string
   */
  public $hostname;
  /**
   * Output only. The number of known API Operations.
   *
   * @var string
   */
  public $knownOperationsCount;
  /**
   * Optional. Last event detected time stamp
   *
   * @var string
   */
  public $lastEventDetectedTime;
  /**
   * Identifier. The name of the discovered API Observation. Format: `projects/{
   * project}/locations/{location}/discoveredApiObservations/{discovered_api_obs
   * ervation}`
   *
   * @var string
   */
  public $name;
  /**
   * Optional. For an observation pushed from a gcp resource, this would be the
   * gcp project id.
   *
   * @var string
   */
  public $origin;
  /**
   * Optional. The IP address (IPv4 or IPv6) of the origin server that the
   * request was sent to. This field can include port information. Examples:
   * `"192.168.1.1"`, `"10.0.0.1:80"`, `"FE80::0202:B3FF:FE1E:8329"`.
   *
   * @var string[]
   */
  public $serverIps;
  /**
   * Optional. The location of the observation source.
   *
   * @var string[]
   */
  public $sourceLocations;
  protected $sourceMetadataType = GoogleCloudApihubV1SourceMetadata::class;
  protected $sourceMetadataDataType = '';
  /**
   * Optional. The type of the source from which the observation was collected.
   *
   * @var string[]
   */
  public $sourceTypes;
  /**
   * Optional. Style of ApiObservation
   *
   * @var string
   */
  public $style;
  /**
   * Output only. The number of unknown API Operations.
   *
   * @var string
   */
  public $unknownOperationsCount;
  /**
   * Output only. Update time stamp of the observation in API Hub.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The number of observed API Operations.
   *
   * @param string $apiOperationCount
   */
  public function setApiOperationCount($apiOperationCount)
  {
    $this->apiOperationCount = $apiOperationCount;
  }
  /**
   * @return string
   */
  public function getApiOperationCount()
  {
    return $this->apiOperationCount;
  }
  /**
   * Output only. Create time stamp of the observation in API Hub.
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
   * Optional. The hostname of requests processed for this Observation.
   *
   * @param string $hostname
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;
  }
  /**
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
  }
  /**
   * Output only. The number of known API Operations.
   *
   * @param string $knownOperationsCount
   */
  public function setKnownOperationsCount($knownOperationsCount)
  {
    $this->knownOperationsCount = $knownOperationsCount;
  }
  /**
   * @return string
   */
  public function getKnownOperationsCount()
  {
    return $this->knownOperationsCount;
  }
  /**
   * Optional. Last event detected time stamp
   *
   * @param string $lastEventDetectedTime
   */
  public function setLastEventDetectedTime($lastEventDetectedTime)
  {
    $this->lastEventDetectedTime = $lastEventDetectedTime;
  }
  /**
   * @return string
   */
  public function getLastEventDetectedTime()
  {
    return $this->lastEventDetectedTime;
  }
  /**
   * Identifier. The name of the discovered API Observation. Format: `projects/{
   * project}/locations/{location}/discoveredApiObservations/{discovered_api_obs
   * ervation}`
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
   * Optional. For an observation pushed from a gcp resource, this would be the
   * gcp project id.
   *
   * @param string $origin
   */
  public function setOrigin($origin)
  {
    $this->origin = $origin;
  }
  /**
   * @return string
   */
  public function getOrigin()
  {
    return $this->origin;
  }
  /**
   * Optional. The IP address (IPv4 or IPv6) of the origin server that the
   * request was sent to. This field can include port information. Examples:
   * `"192.168.1.1"`, `"10.0.0.1:80"`, `"FE80::0202:B3FF:FE1E:8329"`.
   *
   * @param string[] $serverIps
   */
  public function setServerIps($serverIps)
  {
    $this->serverIps = $serverIps;
  }
  /**
   * @return string[]
   */
  public function getServerIps()
  {
    return $this->serverIps;
  }
  /**
   * Optional. The location of the observation source.
   *
   * @param string[] $sourceLocations
   */
  public function setSourceLocations($sourceLocations)
  {
    $this->sourceLocations = $sourceLocations;
  }
  /**
   * @return string[]
   */
  public function getSourceLocations()
  {
    return $this->sourceLocations;
  }
  /**
   * Output only. The metadata of the source from which the observation was
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
   * Optional. The type of the source from which the observation was collected.
   *
   * @param string[] $sourceTypes
   */
  public function setSourceTypes($sourceTypes)
  {
    $this->sourceTypes = $sourceTypes;
  }
  /**
   * @return string[]
   */
  public function getSourceTypes()
  {
    return $this->sourceTypes;
  }
  /**
   * Optional. Style of ApiObservation
   *
   * Accepted values: STYLE_UNSPECIFIED, REST, GRPC, GRAPHQL
   *
   * @param self::STYLE_* $style
   */
  public function setStyle($style)
  {
    $this->style = $style;
  }
  /**
   * @return self::STYLE_*
   */
  public function getStyle()
  {
    return $this->style;
  }
  /**
   * Output only. The number of unknown API Operations.
   *
   * @param string $unknownOperationsCount
   */
  public function setUnknownOperationsCount($unknownOperationsCount)
  {
    $this->unknownOperationsCount = $unknownOperationsCount;
  }
  /**
   * @return string
   */
  public function getUnknownOperationsCount()
  {
    return $this->unknownOperationsCount;
  }
  /**
   * Output only. Update time stamp of the observation in API Hub.
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
class_alias(GoogleCloudApihubV1DiscoveredApiObservation::class, 'Google_Service_APIhub_GoogleCloudApihubV1DiscoveredApiObservation');
