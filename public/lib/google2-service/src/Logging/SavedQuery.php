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

namespace Google\Service\Logging;

class SavedQuery extends \Google\Model
{
  /**
   * The saved query visibility is unspecified. A CreateSavedQuery request with
   * an unspecified visibility will be rejected.
   */
  public const VISIBILITY_VISIBILITY_UNSPECIFIED = 'VISIBILITY_UNSPECIFIED';
  /**
   * The saved query is only visible to the user that created it.
   */
  public const VISIBILITY_PRIVATE = 'PRIVATE';
  /**
   * The saved query is visible to anyone in the project.
   */
  public const VISIBILITY_SHARED = 'SHARED';
  /**
   * Output only. The timestamp when the saved query was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A human readable description of the saved query.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The user specified title for the SavedQuery.
   *
   * @var string
   */
  public $displayName;
  protected $loggingQueryType = LoggingQuery::class;
  protected $loggingQueryDataType = '';
  /**
   * Output only. Resource name of the saved query.In the format:
   * "projects/[PROJECT_ID]/locations/[LOCATION_ID]/savedQueries/[QUERY_ID]" For
   * a list of supported locations, see Supported Regions
   * (https://cloud.google.com/logging/docs/region-support#bucket-regions)After
   * the saved query is created, the location cannot be changed.If the user
   * doesn't provide a QUERY_ID, the system will generate an alphanumeric ID.
   *
   * @var string
   */
  public $name;
  protected $opsAnalyticsQueryType = OpsAnalyticsQuery::class;
  protected $opsAnalyticsQueryDataType = '';
  /**
   * Output only. The timestamp when the saved query was last updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Required. The visibility status of this query, which determines its
   * ownership.
   *
   * @var string
   */
  public $visibility;

  /**
   * Output only. The timestamp when the saved query was created.
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
   * Optional. A human readable description of the saved query.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The user specified title for the SavedQuery.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Logging query that can be executed in Logs Explorer or via Logging API.
   *
   * @param LoggingQuery $loggingQuery
   */
  public function setLoggingQuery(LoggingQuery $loggingQuery)
  {
    $this->loggingQuery = $loggingQuery;
  }
  /**
   * @return LoggingQuery
   */
  public function getLoggingQuery()
  {
    return $this->loggingQuery;
  }
  /**
   * Output only. Resource name of the saved query.In the format:
   * "projects/[PROJECT_ID]/locations/[LOCATION_ID]/savedQueries/[QUERY_ID]" For
   * a list of supported locations, see Supported Regions
   * (https://cloud.google.com/logging/docs/region-support#bucket-regions)After
   * the saved query is created, the location cannot be changed.If the user
   * doesn't provide a QUERY_ID, the system will generate an alphanumeric ID.
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
   * Analytics query that can be executed in Log Analytics.
   *
   * @param OpsAnalyticsQuery $opsAnalyticsQuery
   */
  public function setOpsAnalyticsQuery(OpsAnalyticsQuery $opsAnalyticsQuery)
  {
    $this->opsAnalyticsQuery = $opsAnalyticsQuery;
  }
  /**
   * @return OpsAnalyticsQuery
   */
  public function getOpsAnalyticsQuery()
  {
    return $this->opsAnalyticsQuery;
  }
  /**
   * Output only. The timestamp when the saved query was last updated.
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
   * Required. The visibility status of this query, which determines its
   * ownership.
   *
   * Accepted values: VISIBILITY_UNSPECIFIED, PRIVATE, SHARED
   *
   * @param self::VISIBILITY_* $visibility
   */
  public function setVisibility($visibility)
  {
    $this->visibility = $visibility;
  }
  /**
   * @return self::VISIBILITY_*
   */
  public function getVisibility()
  {
    return $this->visibility;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SavedQuery::class, 'Google_Service_Logging_SavedQuery');
