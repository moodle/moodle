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

namespace Google\Service\Storagetransfer;

class UpdateTransferJobRequest extends \Google\Model
{
  /**
   * Required. The ID of the Google Cloud project that owns the job.
   *
   * @var string
   */
  public $projectId;
  protected $transferJobType = TransferJob::class;
  protected $transferJobDataType = '';
  /**
   * The field mask of the fields in `transferJob` that are to be updated in
   * this request. Fields in `transferJob` that can be updated are: description,
   * transfer_spec, notification_config, logging_config, and status. To update
   * the `transfer_spec` of the job, a complete transfer specification must be
   * provided. An incomplete specification missing any required fields is
   * rejected with the error INVALID_ARGUMENT.
   *
   * @var string
   */
  public $updateTransferJobFieldMask;

  /**
   * Required. The ID of the Google Cloud project that owns the job.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Required. The job to update. `transferJob` is expected to specify one or
   * more of five fields: description, transfer_spec, notification_config,
   * logging_config, and status. An `UpdateTransferJobRequest` that specifies
   * other fields are rejected with the error INVALID_ARGUMENT. Updating a job
   * status to DELETED requires `storagetransfer.jobs.delete` permission.
   *
   * @param TransferJob $transferJob
   */
  public function setTransferJob(TransferJob $transferJob)
  {
    $this->transferJob = $transferJob;
  }
  /**
   * @return TransferJob
   */
  public function getTransferJob()
  {
    return $this->transferJob;
  }
  /**
   * The field mask of the fields in `transferJob` that are to be updated in
   * this request. Fields in `transferJob` that can be updated are: description,
   * transfer_spec, notification_config, logging_config, and status. To update
   * the `transfer_spec` of the job, a complete transfer specification must be
   * provided. An incomplete specification missing any required fields is
   * rejected with the error INVALID_ARGUMENT.
   *
   * @param string $updateTransferJobFieldMask
   */
  public function setUpdateTransferJobFieldMask($updateTransferJobFieldMask)
  {
    $this->updateTransferJobFieldMask = $updateTransferJobFieldMask;
  }
  /**
   * @return string
   */
  public function getUpdateTransferJobFieldMask()
  {
    return $this->updateTransferJobFieldMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateTransferJobRequest::class, 'Google_Service_Storagetransfer_UpdateTransferJobRequest');
