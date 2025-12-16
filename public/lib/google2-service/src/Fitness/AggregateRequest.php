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

namespace Google\Service\Fitness;

class AggregateRequest extends \Google\Collection
{
  protected $collection_key = 'filteredDataQualityStandard';
  protected $aggregateByType = AggregateBy::class;
  protected $aggregateByDataType = 'array';
  protected $bucketByActivitySegmentType = BucketByActivity::class;
  protected $bucketByActivitySegmentDataType = '';
  protected $bucketByActivityTypeType = BucketByActivity::class;
  protected $bucketByActivityTypeDataType = '';
  protected $bucketBySessionType = BucketBySession::class;
  protected $bucketBySessionDataType = '';
  protected $bucketByTimeType = BucketByTime::class;
  protected $bucketByTimeDataType = '';
  /**
   * The end of a window of time. Data that intersects with this time window
   * will be aggregated. The time is in milliseconds since epoch, inclusive. The
   * maximum allowed difference between start_time_millis // and end_time_millis
   * is 7776000000 (roughly 90 days).
   *
   * @var string
   */
  public $endTimeMillis;
  /**
   * DO NOT POPULATE THIS FIELD. It is ignored.
   *
   * @deprecated
   * @var string[]
   */
  public $filteredDataQualityStandard;
  /**
   * The start of a window of time. Data that intersects with this time window
   * will be aggregated. The time is in milliseconds since epoch, inclusive.
   *
   * @var string
   */
  public $startTimeMillis;

  /**
   * The specification of data to be aggregated. At least one aggregateBy spec
   * must be provided. All data that is specified will be aggregated using the
   * same bucketing criteria. There will be one dataset in the response for
   * every aggregateBy spec.
   *
   * @param AggregateBy[] $aggregateBy
   */
  public function setAggregateBy($aggregateBy)
  {
    $this->aggregateBy = $aggregateBy;
  }
  /**
   * @return AggregateBy[]
   */
  public function getAggregateBy()
  {
    return $this->aggregateBy;
  }
  /**
   * Specifies that data be aggregated each activity segment recorded for a
   * user. Similar to bucketByActivitySegment, but bucketing is done for each
   * activity segment rather than all segments of the same type. Mutually
   * exclusive of other bucketing specifications.
   *
   * @param BucketByActivity $bucketByActivitySegment
   */
  public function setBucketByActivitySegment(BucketByActivity $bucketByActivitySegment)
  {
    $this->bucketByActivitySegment = $bucketByActivitySegment;
  }
  /**
   * @return BucketByActivity
   */
  public function getBucketByActivitySegment()
  {
    return $this->bucketByActivitySegment;
  }
  /**
   * Specifies that data be aggregated by the type of activity being performed
   * when the data was recorded. All data that was recorded during a certain
   * activity type (.for the given time range) will be aggregated into the same
   * bucket. Data that was recorded while the user was not active will not be
   * included in the response. Mutually exclusive of other bucketing
   * specifications.
   *
   * @param BucketByActivity $bucketByActivityType
   */
  public function setBucketByActivityType(BucketByActivity $bucketByActivityType)
  {
    $this->bucketByActivityType = $bucketByActivityType;
  }
  /**
   * @return BucketByActivity
   */
  public function getBucketByActivityType()
  {
    return $this->bucketByActivityType;
  }
  /**
   * Specifies that data be aggregated by user sessions. Data that does not fall
   * within the time range of a session will not be included in the response.
   * Mutually exclusive of other bucketing specifications.
   *
   * @param BucketBySession $bucketBySession
   */
  public function setBucketBySession(BucketBySession $bucketBySession)
  {
    $this->bucketBySession = $bucketBySession;
  }
  /**
   * @return BucketBySession
   */
  public function getBucketBySession()
  {
    return $this->bucketBySession;
  }
  /**
   * Specifies that data be aggregated by a single time interval. Mutually
   * exclusive of other bucketing specifications.
   *
   * @param BucketByTime $bucketByTime
   */
  public function setBucketByTime(BucketByTime $bucketByTime)
  {
    $this->bucketByTime = $bucketByTime;
  }
  /**
   * @return BucketByTime
   */
  public function getBucketByTime()
  {
    return $this->bucketByTime;
  }
  /**
   * The end of a window of time. Data that intersects with this time window
   * will be aggregated. The time is in milliseconds since epoch, inclusive. The
   * maximum allowed difference between start_time_millis // and end_time_millis
   * is 7776000000 (roughly 90 days).
   *
   * @param string $endTimeMillis
   */
  public function setEndTimeMillis($endTimeMillis)
  {
    $this->endTimeMillis = $endTimeMillis;
  }
  /**
   * @return string
   */
  public function getEndTimeMillis()
  {
    return $this->endTimeMillis;
  }
  /**
   * DO NOT POPULATE THIS FIELD. It is ignored.
   *
   * @deprecated
   * @param string[] $filteredDataQualityStandard
   */
  public function setFilteredDataQualityStandard($filteredDataQualityStandard)
  {
    $this->filteredDataQualityStandard = $filteredDataQualityStandard;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getFilteredDataQualityStandard()
  {
    return $this->filteredDataQualityStandard;
  }
  /**
   * The start of a window of time. Data that intersects with this time window
   * will be aggregated. The time is in milliseconds since epoch, inclusive.
   *
   * @param string $startTimeMillis
   */
  public function setStartTimeMillis($startTimeMillis)
  {
    $this->startTimeMillis = $startTimeMillis;
  }
  /**
   * @return string
   */
  public function getStartTimeMillis()
  {
    return $this->startTimeMillis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AggregateRequest::class, 'Google_Service_Fitness_AggregateRequest');
