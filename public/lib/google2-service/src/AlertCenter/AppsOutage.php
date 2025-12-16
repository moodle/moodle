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

namespace Google\Service\AlertCenter;

class AppsOutage extends \Google\Collection
{
  /**
   * Status is unspecified.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The incident has just been reported.
   */
  public const STATUS_NEW = 'NEW';
  /**
   * The incident is ongoing.
   */
  public const STATUS_ONGOING = 'ONGOING';
  /**
   * The incident has been resolved.
   */
  public const STATUS_RESOLVED = 'RESOLVED';
  /**
   * Further assessment indicated no customer impact.
   */
  public const STATUS_FALSE_POSITIVE = 'FALSE_POSITIVE';
  /**
   * The incident has been partially resolved.
   */
  public const STATUS_PARTIALLY_RESOLVED = 'PARTIALLY_RESOLVED';
  /**
   * The incident was merged into a parent.
   */
  public const STATUS_MERGED = 'MERGED';
  /**
   * The incident has lower impact than initially anticipated.
   */
  public const STATUS_DOWNGRADED = 'DOWNGRADED';
  protected $collection_key = 'products';
  /**
   * Link to the outage event in Google Workspace Status Dashboard
   *
   * @var string
   */
  public $dashboardUri;
  /**
   * Incident tracking ID.
   *
   * @var string
   */
  public $incidentTrackingId;
  protected $mergeInfoType = MergeInfo::class;
  protected $mergeInfoDataType = '';
  /**
   * Timestamp by which the next update is expected to arrive.
   *
   * @var string
   */
  public $nextUpdateTime;
  /**
   * List of products impacted by the outage.
   *
   * @var string[]
   */
  public $products;
  /**
   * Timestamp when the outage is expected to be resolved, or has confirmed
   * resolution. Provided only when known.
   *
   * @var string
   */
  public $resolutionTime;
  /**
   * Current outage status.
   *
   * @var string
   */
  public $status;

  /**
   * Link to the outage event in Google Workspace Status Dashboard
   *
   * @param string $dashboardUri
   */
  public function setDashboardUri($dashboardUri)
  {
    $this->dashboardUri = $dashboardUri;
  }
  /**
   * @return string
   */
  public function getDashboardUri()
  {
    return $this->dashboardUri;
  }
  /**
   * Incident tracking ID.
   *
   * @param string $incidentTrackingId
   */
  public function setIncidentTrackingId($incidentTrackingId)
  {
    $this->incidentTrackingId = $incidentTrackingId;
  }
  /**
   * @return string
   */
  public function getIncidentTrackingId()
  {
    return $this->incidentTrackingId;
  }
  /**
   * Indicates new alert details under which the outage is communicated. Only
   * populated when Status is MERGED.
   *
   * @param MergeInfo $mergeInfo
   */
  public function setMergeInfo(MergeInfo $mergeInfo)
  {
    $this->mergeInfo = $mergeInfo;
  }
  /**
   * @return MergeInfo
   */
  public function getMergeInfo()
  {
    return $this->mergeInfo;
  }
  /**
   * Timestamp by which the next update is expected to arrive.
   *
   * @param string $nextUpdateTime
   */
  public function setNextUpdateTime($nextUpdateTime)
  {
    $this->nextUpdateTime = $nextUpdateTime;
  }
  /**
   * @return string
   */
  public function getNextUpdateTime()
  {
    return $this->nextUpdateTime;
  }
  /**
   * List of products impacted by the outage.
   *
   * @param string[] $products
   */
  public function setProducts($products)
  {
    $this->products = $products;
  }
  /**
   * @return string[]
   */
  public function getProducts()
  {
    return $this->products;
  }
  /**
   * Timestamp when the outage is expected to be resolved, or has confirmed
   * resolution. Provided only when known.
   *
   * @param string $resolutionTime
   */
  public function setResolutionTime($resolutionTime)
  {
    $this->resolutionTime = $resolutionTime;
  }
  /**
   * @return string
   */
  public function getResolutionTime()
  {
    return $this->resolutionTime;
  }
  /**
   * Current outage status.
   *
   * Accepted values: STATUS_UNSPECIFIED, NEW, ONGOING, RESOLVED,
   * FALSE_POSITIVE, PARTIALLY_RESOLVED, MERGED, DOWNGRADED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppsOutage::class, 'Google_Service_AlertCenter_AppsOutage');
