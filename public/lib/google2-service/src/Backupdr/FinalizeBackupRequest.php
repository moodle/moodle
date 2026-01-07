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

namespace Google\Service\Backupdr;

class FinalizeBackupRequest extends \Google\Model
{
  /**
   * Required. Resource ID of the Backup resource to be finalized. This must be
   * the same backup_id that was used in the InitiateBackupRequest.
   *
   * @var string
   */
  public $backupId;
  /**
   * The point in time when this backup was captured from the source. This will
   * be assigned to the consistency_time field of the newly created Backup.
   *
   * @var string
   */
  public $consistencyTime;
  /**
   * This will be assigned to the description field of the newly created Backup.
   *
   * @var string
   */
  public $description;
  /**
   * The latest timestamp of data available in this Backup. This will be set on
   * the newly created Backup.
   *
   * @var string
   */
  public $recoveryRangeEndTime;
  /**
   * The earliest timestamp of data available in this Backup. This will set on
   * the newly created Backup.
   *
   * @var string
   */
  public $recoveryRangeStartTime;
  /**
   * Optional. An optional request ID to identify requests. Specify a unique
   * request ID so that if you must retry your request, the server will know to
   * ignore the request if it has already been completed. The server will
   * guarantee that for at least 60 minutes after the first request. For
   * example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @var string
   */
  public $requestId;
  /**
   * The ExpireTime on the backup will be set to FinalizeTime plus this
   * duration. If the resulting ExpireTime is less than
   * EnforcedRetentionEndTime, then ExpireTime is set to
   * EnforcedRetentionEndTime.
   *
   * @var string
   */
  public $retentionDuration;

  /**
   * Required. Resource ID of the Backup resource to be finalized. This must be
   * the same backup_id that was used in the InitiateBackupRequest.
   *
   * @param string $backupId
   */
  public function setBackupId($backupId)
  {
    $this->backupId = $backupId;
  }
  /**
   * @return string
   */
  public function getBackupId()
  {
    return $this->backupId;
  }
  /**
   * The point in time when this backup was captured from the source. This will
   * be assigned to the consistency_time field of the newly created Backup.
   *
   * @param string $consistencyTime
   */
  public function setConsistencyTime($consistencyTime)
  {
    $this->consistencyTime = $consistencyTime;
  }
  /**
   * @return string
   */
  public function getConsistencyTime()
  {
    return $this->consistencyTime;
  }
  /**
   * This will be assigned to the description field of the newly created Backup.
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
   * The latest timestamp of data available in this Backup. This will be set on
   * the newly created Backup.
   *
   * @param string $recoveryRangeEndTime
   */
  public function setRecoveryRangeEndTime($recoveryRangeEndTime)
  {
    $this->recoveryRangeEndTime = $recoveryRangeEndTime;
  }
  /**
   * @return string
   */
  public function getRecoveryRangeEndTime()
  {
    return $this->recoveryRangeEndTime;
  }
  /**
   * The earliest timestamp of data available in this Backup. This will set on
   * the newly created Backup.
   *
   * @param string $recoveryRangeStartTime
   */
  public function setRecoveryRangeStartTime($recoveryRangeStartTime)
  {
    $this->recoveryRangeStartTime = $recoveryRangeStartTime;
  }
  /**
   * @return string
   */
  public function getRecoveryRangeStartTime()
  {
    return $this->recoveryRangeStartTime;
  }
  /**
   * Optional. An optional request ID to identify requests. Specify a unique
   * request ID so that if you must retry your request, the server will know to
   * ignore the request if it has already been completed. The server will
   * guarantee that for at least 60 minutes after the first request. For
   * example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * The ExpireTime on the backup will be set to FinalizeTime plus this
   * duration. If the resulting ExpireTime is less than
   * EnforcedRetentionEndTime, then ExpireTime is set to
   * EnforcedRetentionEndTime.
   *
   * @param string $retentionDuration
   */
  public function setRetentionDuration($retentionDuration)
  {
    $this->retentionDuration = $retentionDuration;
  }
  /**
   * @return string
   */
  public function getRetentionDuration()
  {
    return $this->retentionDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FinalizeBackupRequest::class, 'Google_Service_Backupdr_FinalizeBackupRequest');
