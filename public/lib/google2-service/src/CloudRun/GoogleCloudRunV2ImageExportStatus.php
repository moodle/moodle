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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2ImageExportStatus extends \Google\Model
{
  /**
   * State unspecified.
   */
  public const EXPORT_JOB_STATE_EXPORT_JOB_STATE_UNSPECIFIED = 'EXPORT_JOB_STATE_UNSPECIFIED';
  /**
   * Job still in progress.
   */
  public const EXPORT_JOB_STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Job finished.
   */
  public const EXPORT_JOB_STATE_FINISHED = 'FINISHED';
  /**
   * Output only. Has the image export job finished (regardless of successful or
   * failure).
   *
   * @var string
   */
  public $exportJobState;
  /**
   * The exported image ID as it will appear in Artifact Registry.
   *
   * @var string
   */
  public $exportedImageDigest;
  protected $statusType = UtilStatusProto::class;
  protected $statusDataType = '';
  /**
   * The image tag as it will appear in Artifact Registry.
   *
   * @var string
   */
  public $tag;

  /**
   * Output only. Has the image export job finished (regardless of successful or
   * failure).
   *
   * Accepted values: EXPORT_JOB_STATE_UNSPECIFIED, IN_PROGRESS, FINISHED
   *
   * @param self::EXPORT_JOB_STATE_* $exportJobState
   */
  public function setExportJobState($exportJobState)
  {
    $this->exportJobState = $exportJobState;
  }
  /**
   * @return self::EXPORT_JOB_STATE_*
   */
  public function getExportJobState()
  {
    return $this->exportJobState;
  }
  /**
   * The exported image ID as it will appear in Artifact Registry.
   *
   * @param string $exportedImageDigest
   */
  public function setExportedImageDigest($exportedImageDigest)
  {
    $this->exportedImageDigest = $exportedImageDigest;
  }
  /**
   * @return string
   */
  public function getExportedImageDigest()
  {
    return $this->exportedImageDigest;
  }
  /**
   * The status of the export task if done.
   *
   * @param UtilStatusProto $status
   */
  public function setStatus(UtilStatusProto $status)
  {
    $this->status = $status;
  }
  /**
   * @return UtilStatusProto
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The image tag as it will appear in Artifact Registry.
   *
   * @param string $tag
   */
  public function setTag($tag)
  {
    $this->tag = $tag;
  }
  /**
   * @return string
   */
  public function getTag()
  {
    return $this->tag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2ImageExportStatus::class, 'Google_Service_CloudRun_GoogleCloudRunV2ImageExportStatus');
