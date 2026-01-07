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

namespace Google\Service\DataPortability;

class PortabilityArchiveState extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The job is in progress.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * The job is complete.
   */
  public const STATE_COMPLETE = 'COMPLETE';
  /**
   * The job failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The job is cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  protected $collection_key = 'urls';
  /**
   * The timestamp that represents the end point for the data you are exporting.
   * If the end_time value is set in the InitiatePortabilityArchiveRequest, this
   * field is set to that value. If end_time is not set, this value is set to
   * the time the export was requested.
   *
   * @var string
   */
  public $exportTime;
  /**
   * The resource name of ArchiveJob's PortabilityArchiveState singleton. The
   * format is: archiveJobs/{archive_job}/portabilityArchiveState. archive_job
   * is the job ID provided in the request.
   *
   * @var string
   */
  public $name;
  /**
   * The timestamp that represents the starting point for the data you are
   * exporting. This field is set only if the start_time field is specified in
   * the InitiatePortabilityArchiveRequest.
   *
   * @var string
   */
  public $startTime;
  /**
   * Resource that represents the state of the Archive job.
   *
   * @var string
   */
  public $state;
  /**
   * If the state is complete, this method returns the signed URLs of the
   * objects in the Cloud Storage bucket.
   *
   * @var string[]
   */
  public $urls;

  /**
   * The timestamp that represents the end point for the data you are exporting.
   * If the end_time value is set in the InitiatePortabilityArchiveRequest, this
   * field is set to that value. If end_time is not set, this value is set to
   * the time the export was requested.
   *
   * @param string $exportTime
   */
  public function setExportTime($exportTime)
  {
    $this->exportTime = $exportTime;
  }
  /**
   * @return string
   */
  public function getExportTime()
  {
    return $this->exportTime;
  }
  /**
   * The resource name of ArchiveJob's PortabilityArchiveState singleton. The
   * format is: archiveJobs/{archive_job}/portabilityArchiveState. archive_job
   * is the job ID provided in the request.
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
   * The timestamp that represents the starting point for the data you are
   * exporting. This field is set only if the start_time field is specified in
   * the InitiatePortabilityArchiveRequest.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Resource that represents the state of the Archive job.
   *
   * Accepted values: STATE_UNSPECIFIED, IN_PROGRESS, COMPLETE, FAILED,
   * CANCELLED
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
  /**
   * If the state is complete, this method returns the signed URLs of the
   * objects in the Cloud Storage bucket.
   *
   * @param string[] $urls
   */
  public function setUrls($urls)
  {
    $this->urls = $urls;
  }
  /**
   * @return string[]
   */
  public function getUrls()
  {
    return $this->urls;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PortabilityArchiveState::class, 'Google_Service_DataPortability_PortabilityArchiveState');
