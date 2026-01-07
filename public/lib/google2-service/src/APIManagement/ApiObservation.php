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

namespace Google\Service\APIManagement;

class ApiObservation extends \Google\Collection
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
  protected $collection_key = 'tags';
  /**
   * The number of observed API Operations.
   *
   * @var string
   */
  public $apiOperationCount;
  /**
   * Create time stamp
   *
   * @var string
   */
  public $createTime;
  /**
   * The hostname of requests processed for this Observation.
   *
   * @var string
   */
  public $hostname;
  /**
   * Last event detected time stamp
   *
   * @var string
   */
  public $lastEventDetectedTime;
  /**
   * Identifier. Name of resource
   *
   * @var string
   */
  public $name;
  /**
   * The IP address (IPv4 or IPv6) of the origin server that the request was
   * sent to. This field can include port information. Examples:
   * `"192.168.1.1"`, `"10.0.0.1:80"`, `"FE80::0202:B3FF:FE1E:8329"`.
   *
   * @var string[]
   */
  public $serverIps;
  /**
   * Location of the Observation Source, for example "us-central1" or "europe-
   * west1."
   *
   * @var string[]
   */
  public $sourceLocations;
  /**
   * Style of ApiObservation
   *
   * @var string
   */
  public $style;
  /**
   * User-defined tags to organize and sort
   *
   * @var string[]
   */
  public $tags;
  /**
   * Update time stamp
   *
   * @var string
   */
  public $updateTime;

  /**
   * The number of observed API Operations.
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
   * Create time stamp
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
   * The hostname of requests processed for this Observation.
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
   * Last event detected time stamp
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
   * Identifier. Name of resource
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
   * The IP address (IPv4 or IPv6) of the origin server that the request was
   * sent to. This field can include port information. Examples:
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
   * Location of the Observation Source, for example "us-central1" or "europe-
   * west1."
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
   * Style of ApiObservation
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
   * User-defined tags to organize and sort
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * Update time stamp
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
class_alias(ApiObservation::class, 'Google_Service_APIManagement_ApiObservation');
