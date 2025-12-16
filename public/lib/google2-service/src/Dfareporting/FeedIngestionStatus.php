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

namespace Google\Service\Dfareporting;

class FeedIngestionStatus extends \Google\Collection
{
  /**
   * The feed processing state is unknown.
   */
  public const STATE_FEED_PROCESSING_STATE_UNKNOWN = 'FEED_PROCESSING_STATE_UNKNOWN';
  /**
   * The feed processing state is cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The feed processing state is ingesting queued.
   */
  public const STATE_INGESTING_QUEUED = 'INGESTING_QUEUED';
  /**
   * The feed processing state is ingesting.
   */
  public const STATE_INGESTING = 'INGESTING';
  /**
   * The feed processing state is ingested successfully.
   */
  public const STATE_INGESTED_SUCCESS = 'INGESTED_SUCCESS';
  /**
   * The feed processing state is ingested with failure.
   */
  public const STATE_INGESTED_FAILURE = 'INGESTED_FAILURE';
  /**
   * The feed processing state is request to publish.
   */
  public const STATE_REQUEST_TO_PUBLISH = 'REQUEST_TO_PUBLISH';
  /**
   * The feed processing state is publishing.
   */
  public const STATE_PUBLISHING = 'PUBLISHING';
  /**
   * The feed processing state is published successfully.
   */
  public const STATE_PUBLISHED_SUCCESS = 'PUBLISHED_SUCCESS';
  /**
   * The feed processing state is published with failure.
   */
  public const STATE_PUBLISHED_FAILURE = 'PUBLISHED_FAILURE';
  protected $collection_key = 'ingestionErrorRecords';
  protected $ingestionErrorRecordsType = IngestionErrorRecord::class;
  protected $ingestionErrorRecordsDataType = 'array';
  protected $ingestionStatusType = IngestionStatus::class;
  protected $ingestionStatusDataType = '';
  /**
   * Output only. The processing state of the feed.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The ingestion error records of the feed.
   *
   * @param IngestionErrorRecord[] $ingestionErrorRecords
   */
  public function setIngestionErrorRecords($ingestionErrorRecords)
  {
    $this->ingestionErrorRecords = $ingestionErrorRecords;
  }
  /**
   * @return IngestionErrorRecord[]
   */
  public function getIngestionErrorRecords()
  {
    return $this->ingestionErrorRecords;
  }
  /**
   * Output only. The ingestion status of the feed.
   *
   * @param IngestionStatus $ingestionStatus
   */
  public function setIngestionStatus(IngestionStatus $ingestionStatus)
  {
    $this->ingestionStatus = $ingestionStatus;
  }
  /**
   * @return IngestionStatus
   */
  public function getIngestionStatus()
  {
    return $this->ingestionStatus;
  }
  /**
   * Output only. The processing state of the feed.
   *
   * Accepted values: FEED_PROCESSING_STATE_UNKNOWN, CANCELLED,
   * INGESTING_QUEUED, INGESTING, INGESTED_SUCCESS, INGESTED_FAILURE,
   * REQUEST_TO_PUBLISH, PUBLISHING, PUBLISHED_SUCCESS, PUBLISHED_FAILURE
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
class_alias(FeedIngestionStatus::class, 'Google_Service_Dfareporting_FeedIngestionStatus');
