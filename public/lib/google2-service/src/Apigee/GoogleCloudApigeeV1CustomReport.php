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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1CustomReport extends \Google\Collection
{
  protected $collection_key = 'tags';
  /**
   * This field contains the chart type for the report
   *
   * @var string
   */
  public $chartType;
  /**
   * Legacy field: not used. This field contains a list of comments associated
   * with custom report
   *
   * @var string[]
   */
  public $comments;
  /**
   * Output only. Unix time when the app was created json key: createdAt
   *
   * @var string
   */
  public $createdAt;
  /**
   * This contains the list of dimensions for the report
   *
   * @var string[]
   */
  public $dimensions;
  /**
   * This is the display name for the report
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Environment name
   *
   * @var string
   */
  public $environment;
  /**
   * This field contains the filter expression
   *
   * @var string
   */
  public $filter;
  /**
   * Legacy field: not used. Contains the from time for the report
   *
   * @var string
   */
  public $fromTime;
  /**
   * Output only. Modified time of this entity as milliseconds since epoch. json
   * key: lastModifiedAt
   *
   * @var string
   */
  public $lastModifiedAt;
  /**
   * Output only. Last viewed time of this entity as milliseconds since epoch
   *
   * @var string
   */
  public $lastViewedAt;
  /**
   * Legacy field: not used This field contains the limit for the result
   * retrieved
   *
   * @var string
   */
  public $limit;
  protected $metricsType = GoogleCloudApigeeV1CustomReportMetric::class;
  protected $metricsDataType = 'array';
  /**
   * Required. Unique identifier for the report T his is a legacy field used to
   * encode custom report unique id
   *
   * @var string
   */
  public $name;
  /**
   * Legacy field: not used. This field contains the offset for the data
   *
   * @var string
   */
  public $offset;
  /**
   * Output only. Organization name
   *
   * @var string
   */
  public $organization;
  protected $propertiesType = GoogleCloudApigeeV1ReportProperty::class;
  protected $propertiesDataType = 'array';
  /**
   * Legacy field: not used much. Contains the list of sort by columns
   *
   * @var string[]
   */
  public $sortByCols;
  /**
   * Legacy field: not used much. Contains the sort order for the sort columns
   *
   * @var string
   */
  public $sortOrder;
  /**
   * Legacy field: not used. This field contains a list of tags associated with
   * custom report
   *
   * @var string[]
   */
  public $tags;
  /**
   * This field contains the time unit of aggregation for the report
   *
   * @var string
   */
  public $timeUnit;
  /**
   * Legacy field: not used. Contains the end time for the report
   *
   * @var string
   */
  public $toTime;
  /**
   * Legacy field: not used. This field contains the top k parameter value for
   * restricting the result
   *
   * @var string
   */
  public $topk;

  /**
   * This field contains the chart type for the report
   *
   * @param string $chartType
   */
  public function setChartType($chartType)
  {
    $this->chartType = $chartType;
  }
  /**
   * @return string
   */
  public function getChartType()
  {
    return $this->chartType;
  }
  /**
   * Legacy field: not used. This field contains a list of comments associated
   * with custom report
   *
   * @param string[] $comments
   */
  public function setComments($comments)
  {
    $this->comments = $comments;
  }
  /**
   * @return string[]
   */
  public function getComments()
  {
    return $this->comments;
  }
  /**
   * Output only. Unix time when the app was created json key: createdAt
   *
   * @param string $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }
  /**
   * @return string
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }
  /**
   * This contains the list of dimensions for the report
   *
   * @param string[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return string[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * This is the display name for the report
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
   * Output only. Environment name
   *
   * @param string $environment
   */
  public function setEnvironment($environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return string
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * This field contains the filter expression
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Legacy field: not used. Contains the from time for the report
   *
   * @param string $fromTime
   */
  public function setFromTime($fromTime)
  {
    $this->fromTime = $fromTime;
  }
  /**
   * @return string
   */
  public function getFromTime()
  {
    return $this->fromTime;
  }
  /**
   * Output only. Modified time of this entity as milliseconds since epoch. json
   * key: lastModifiedAt
   *
   * @param string $lastModifiedAt
   */
  public function setLastModifiedAt($lastModifiedAt)
  {
    $this->lastModifiedAt = $lastModifiedAt;
  }
  /**
   * @return string
   */
  public function getLastModifiedAt()
  {
    return $this->lastModifiedAt;
  }
  /**
   * Output only. Last viewed time of this entity as milliseconds since epoch
   *
   * @param string $lastViewedAt
   */
  public function setLastViewedAt($lastViewedAt)
  {
    $this->lastViewedAt = $lastViewedAt;
  }
  /**
   * @return string
   */
  public function getLastViewedAt()
  {
    return $this->lastViewedAt;
  }
  /**
   * Legacy field: not used This field contains the limit for the result
   * retrieved
   *
   * @param string $limit
   */
  public function setLimit($limit)
  {
    $this->limit = $limit;
  }
  /**
   * @return string
   */
  public function getLimit()
  {
    return $this->limit;
  }
  /**
   * Required. This contains the list of metrics
   *
   * @param GoogleCloudApigeeV1CustomReportMetric[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return GoogleCloudApigeeV1CustomReportMetric[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Required. Unique identifier for the report T his is a legacy field used to
   * encode custom report unique id
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
   * Legacy field: not used. This field contains the offset for the data
   *
   * @param string $offset
   */
  public function setOffset($offset)
  {
    $this->offset = $offset;
  }
  /**
   * @return string
   */
  public function getOffset()
  {
    return $this->offset;
  }
  /**
   * Output only. Organization name
   *
   * @param string $organization
   */
  public function setOrganization($organization)
  {
    $this->organization = $organization;
  }
  /**
   * @return string
   */
  public function getOrganization()
  {
    return $this->organization;
  }
  /**
   * This field contains report properties such as ui metadata etc.
   *
   * @param GoogleCloudApigeeV1ReportProperty[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return GoogleCloudApigeeV1ReportProperty[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Legacy field: not used much. Contains the list of sort by columns
   *
   * @param string[] $sortByCols
   */
  public function setSortByCols($sortByCols)
  {
    $this->sortByCols = $sortByCols;
  }
  /**
   * @return string[]
   */
  public function getSortByCols()
  {
    return $this->sortByCols;
  }
  /**
   * Legacy field: not used much. Contains the sort order for the sort columns
   *
   * @param string $sortOrder
   */
  public function setSortOrder($sortOrder)
  {
    $this->sortOrder = $sortOrder;
  }
  /**
   * @return string
   */
  public function getSortOrder()
  {
    return $this->sortOrder;
  }
  /**
   * Legacy field: not used. This field contains a list of tags associated with
   * custom report
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
   * This field contains the time unit of aggregation for the report
   *
   * @param string $timeUnit
   */
  public function setTimeUnit($timeUnit)
  {
    $this->timeUnit = $timeUnit;
  }
  /**
   * @return string
   */
  public function getTimeUnit()
  {
    return $this->timeUnit;
  }
  /**
   * Legacy field: not used. Contains the end time for the report
   *
   * @param string $toTime
   */
  public function setToTime($toTime)
  {
    $this->toTime = $toTime;
  }
  /**
   * @return string
   */
  public function getToTime()
  {
    return $this->toTime;
  }
  /**
   * Legacy field: not used. This field contains the top k parameter value for
   * restricting the result
   *
   * @param string $topk
   */
  public function setTopk($topk)
  {
    $this->topk = $topk;
  }
  /**
   * @return string
   */
  public function getTopk()
  {
    return $this->topk;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1CustomReport::class, 'Google_Service_Apigee_GoogleCloudApigeeV1CustomReport');
