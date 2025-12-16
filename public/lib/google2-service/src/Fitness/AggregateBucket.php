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

class AggregateBucket extends \Google\Collection
{
  public const TYPE_unknown = 'unknown';
  /**
   * Denotes that bucketing by time is requested. When this is specified, the
   * timeBucketDurationMillis field is used to determine how many buckets will
   * be returned.
   */
  public const TYPE_time = 'time';
  /**
   * Denotes that bucketing by session is requested. When this is specified,
   * only data that occurs within sessions that begin and end within the dataset
   * time frame, is included in the results.
   */
  public const TYPE_session = 'session';
  /**
   * Denotes that bucketing by activity type is requested. When this is
   * specified, there will be one bucket for each unique activity type that a
   * user participated in, during the dataset time frame of interest.
   */
  public const TYPE_activityType = 'activityType';
  /**
   * Denotes that bucketing by individual activity segment is requested. This
   * will aggregate data by the time boundaries specified by each activity
   * segment occurring within the dataset time frame of interest.
   */
  public const TYPE_activitySegment = 'activitySegment';
  protected $collection_key = 'dataset';
  /**
   * Available for Bucket.Type.ACTIVITY_TYPE, Bucket.Type.ACTIVITY_SEGMENT
   *
   * @var int
   */
  public $activity;
  protected $datasetType = Dataset::class;
  protected $datasetDataType = 'array';
  /**
   * The end time for the aggregated data, in milliseconds since epoch,
   * inclusive.
   *
   * @var string
   */
  public $endTimeMillis;
  protected $sessionType = Session::class;
  protected $sessionDataType = '';
  /**
   * The start time for the aggregated data, in milliseconds since epoch,
   * inclusive.
   *
   * @var string
   */
  public $startTimeMillis;
  /**
   * The type of a bucket signifies how the data aggregation is performed in the
   * bucket.
   *
   * @var string
   */
  public $type;

  /**
   * Available for Bucket.Type.ACTIVITY_TYPE, Bucket.Type.ACTIVITY_SEGMENT
   *
   * @param int $activity
   */
  public function setActivity($activity)
  {
    $this->activity = $activity;
  }
  /**
   * @return int
   */
  public function getActivity()
  {
    return $this->activity;
  }
  /**
   * There will be one dataset per AggregateBy in the request.
   *
   * @param Dataset[] $dataset
   */
  public function setDataset($dataset)
  {
    $this->dataset = $dataset;
  }
  /**
   * @return Dataset[]
   */
  public function getDataset()
  {
    return $this->dataset;
  }
  /**
   * The end time for the aggregated data, in milliseconds since epoch,
   * inclusive.
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
   * Available for Bucket.Type.SESSION
   *
   * @param Session $session
   */
  public function setSession(Session $session)
  {
    $this->session = $session;
  }
  /**
   * @return Session
   */
  public function getSession()
  {
    return $this->session;
  }
  /**
   * The start time for the aggregated data, in milliseconds since epoch,
   * inclusive.
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
  /**
   * The type of a bucket signifies how the data aggregation is performed in the
   * bucket.
   *
   * Accepted values: unknown, time, session, activityType, activitySegment
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AggregateBucket::class, 'Google_Service_Fitness_AggregateBucket');
