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

class GoogleCloudRunV2ExportStatusResponse extends \Google\Collection
{
  /**
   * State unspecified.
   */
  public const OPERATION_STATE_OPERATION_STATE_UNSPECIFIED = 'OPERATION_STATE_UNSPECIFIED';
  /**
   * Operation still in progress.
   */
  public const OPERATION_STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Operation finished.
   */
  public const OPERATION_STATE_FINISHED = 'FINISHED';
  protected $collection_key = 'imageExportStatuses';
  protected $imageExportStatusesType = GoogleCloudRunV2ImageExportStatus::class;
  protected $imageExportStatusesDataType = 'array';
  /**
   * The operation id.
   *
   * @var string
   */
  public $operationId;
  /**
   * Output only. The state of the overall export operation.
   *
   * @var string
   */
  public $operationState;

  /**
   * The status of each image export job.
   *
   * @param GoogleCloudRunV2ImageExportStatus[] $imageExportStatuses
   */
  public function setImageExportStatuses($imageExportStatuses)
  {
    $this->imageExportStatuses = $imageExportStatuses;
  }
  /**
   * @return GoogleCloudRunV2ImageExportStatus[]
   */
  public function getImageExportStatuses()
  {
    return $this->imageExportStatuses;
  }
  /**
   * The operation id.
   *
   * @param string $operationId
   */
  public function setOperationId($operationId)
  {
    $this->operationId = $operationId;
  }
  /**
   * @return string
   */
  public function getOperationId()
  {
    return $this->operationId;
  }
  /**
   * Output only. The state of the overall export operation.
   *
   * Accepted values: OPERATION_STATE_UNSPECIFIED, IN_PROGRESS, FINISHED
   *
   * @param self::OPERATION_STATE_* $operationState
   */
  public function setOperationState($operationState)
  {
    $this->operationState = $operationState;
  }
  /**
   * @return self::OPERATION_STATE_*
   */
  public function getOperationState()
  {
    return $this->operationState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2ExportStatusResponse::class, 'Google_Service_CloudRun_GoogleCloudRunV2ExportStatusResponse');
