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

class GoogleCloudDiscoveryengineV1alphaRecrawlUrisMetadata extends \Google\Collection
{
  protected $collection_key = 'urisNotMatchingTargetSites';
  /**
   * Operation create time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Unique URIs in the request that have invalid format. Sample limited to
   * 1000.
   *
   * @var string[]
   */
  public $invalidUris;
  /**
   * Total number of unique URIs in the request that have invalid format.
   *
   * @var int
   */
  public $invalidUrisCount;
  /**
   * URIs that have no index meta tag. Sample limited to 1000.
   *
   * @var string[]
   */
  public $noindexUris;
  /**
   * Total number of URIs that have no index meta tag.
   *
   * @var int
   */
  public $noindexUrisCount;
  /**
   * Total number of URIs that have yet to be crawled.
   *
   * @var int
   */
  public $pendingCount;
  /**
   * Total number of URIs that were rejected due to insufficient indexing
   * resources.
   *
   * @var int
   */
  public $quotaExceededCount;
  /**
   * Total number of URIs that have been crawled so far.
   *
   * @var int
   */
  public $successCount;
  /**
   * Operation last update time. If the operation is done, this is also the
   * finish time.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Unique URIs in the request that don't match any TargetSite in the
   * DataStore, only match TargetSites that haven't been fully indexed, or match
   * a TargetSite with type EXCLUDE. Sample limited to 1000.
   *
   * @var string[]
   */
  public $urisNotMatchingTargetSites;
  /**
   * Total number of URIs that don't match any TargetSites.
   *
   * @var int
   */
  public $urisNotMatchingTargetSitesCount;
  /**
   * Total number of unique URIs in the request that are not in invalid_uris.
   *
   * @var int
   */
  public $validUrisCount;

  /**
   * Operation create time.
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
   * Unique URIs in the request that have invalid format. Sample limited to
   * 1000.
   *
   * @param string[] $invalidUris
   */
  public function setInvalidUris($invalidUris)
  {
    $this->invalidUris = $invalidUris;
  }
  /**
   * @return string[]
   */
  public function getInvalidUris()
  {
    return $this->invalidUris;
  }
  /**
   * Total number of unique URIs in the request that have invalid format.
   *
   * @param int $invalidUrisCount
   */
  public function setInvalidUrisCount($invalidUrisCount)
  {
    $this->invalidUrisCount = $invalidUrisCount;
  }
  /**
   * @return int
   */
  public function getInvalidUrisCount()
  {
    return $this->invalidUrisCount;
  }
  /**
   * URIs that have no index meta tag. Sample limited to 1000.
   *
   * @param string[] $noindexUris
   */
  public function setNoindexUris($noindexUris)
  {
    $this->noindexUris = $noindexUris;
  }
  /**
   * @return string[]
   */
  public function getNoindexUris()
  {
    return $this->noindexUris;
  }
  /**
   * Total number of URIs that have no index meta tag.
   *
   * @param int $noindexUrisCount
   */
  public function setNoindexUrisCount($noindexUrisCount)
  {
    $this->noindexUrisCount = $noindexUrisCount;
  }
  /**
   * @return int
   */
  public function getNoindexUrisCount()
  {
    return $this->noindexUrisCount;
  }
  /**
   * Total number of URIs that have yet to be crawled.
   *
   * @param int $pendingCount
   */
  public function setPendingCount($pendingCount)
  {
    $this->pendingCount = $pendingCount;
  }
  /**
   * @return int
   */
  public function getPendingCount()
  {
    return $this->pendingCount;
  }
  /**
   * Total number of URIs that were rejected due to insufficient indexing
   * resources.
   *
   * @param int $quotaExceededCount
   */
  public function setQuotaExceededCount($quotaExceededCount)
  {
    $this->quotaExceededCount = $quotaExceededCount;
  }
  /**
   * @return int
   */
  public function getQuotaExceededCount()
  {
    return $this->quotaExceededCount;
  }
  /**
   * Total number of URIs that have been crawled so far.
   *
   * @param int $successCount
   */
  public function setSuccessCount($successCount)
  {
    $this->successCount = $successCount;
  }
  /**
   * @return int
   */
  public function getSuccessCount()
  {
    return $this->successCount;
  }
  /**
   * Operation last update time. If the operation is done, this is also the
   * finish time.
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
  /**
   * Unique URIs in the request that don't match any TargetSite in the
   * DataStore, only match TargetSites that haven't been fully indexed, or match
   * a TargetSite with type EXCLUDE. Sample limited to 1000.
   *
   * @param string[] $urisNotMatchingTargetSites
   */
  public function setUrisNotMatchingTargetSites($urisNotMatchingTargetSites)
  {
    $this->urisNotMatchingTargetSites = $urisNotMatchingTargetSites;
  }
  /**
   * @return string[]
   */
  public function getUrisNotMatchingTargetSites()
  {
    return $this->urisNotMatchingTargetSites;
  }
  /**
   * Total number of URIs that don't match any TargetSites.
   *
   * @param int $urisNotMatchingTargetSitesCount
   */
  public function setUrisNotMatchingTargetSitesCount($urisNotMatchingTargetSitesCount)
  {
    $this->urisNotMatchingTargetSitesCount = $urisNotMatchingTargetSitesCount;
  }
  /**
   * @return int
   */
  public function getUrisNotMatchingTargetSitesCount()
  {
    return $this->urisNotMatchingTargetSitesCount;
  }
  /**
   * Total number of unique URIs in the request that are not in invalid_uris.
   *
   * @param int $validUrisCount
   */
  public function setValidUrisCount($validUrisCount)
  {
    $this->validUrisCount = $validUrisCount;
  }
  /**
   * @return int
   */
  public function getValidUrisCount()
  {
    return $this->validUrisCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaRecrawlUrisMetadata::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaRecrawlUrisMetadata');
