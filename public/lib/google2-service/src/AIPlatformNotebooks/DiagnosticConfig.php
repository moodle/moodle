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

namespace Google\Service\AIPlatformNotebooks;

class DiagnosticConfig extends \Google\Model
{
  /**
   * Optional. Enables flag to copy all `/home/jupyter` folder contents
   *
   * @var bool
   */
  public $enableCopyHomeFilesFlag;
  /**
   * Optional. Enables flag to capture packets from the instance for 30 seconds
   *
   * @var bool
   */
  public $enablePacketCaptureFlag;
  /**
   * Optional. Enables flag to repair service for instance
   *
   * @var bool
   */
  public $enableRepairFlag;
  /**
   * Required. User Cloud Storage bucket location (REQUIRED). Must be formatted
   * with path prefix (`gs://$GCS_BUCKET`). Permissions: User Managed Notebooks:
   * - storage.buckets.writer: Must be given to the project's service account
   * attached to VM. Google Managed Notebooks: - storage.buckets.writer: Must be
   * given to the project's service account or user credentials attached to VM
   * depending on authentication mode. Cloud Storage bucket Log file will be
   * written to `gs://$GCS_BUCKET/$RELATIVE_PATH/$VM_DATE_$TIME.tar.gz`
   *
   * @var string
   */
  public $gcsBucket;
  /**
   * Optional. Defines the relative storage path in the Cloud Storage bucket
   * where the diagnostic logs will be written: Default path will be the root
   * directory of the Cloud Storage bucket
   * (`gs://$GCS_BUCKET/$DATE_$TIME.tar.gz`) Example of full path where Log file
   * will be written: `gs://$GCS_BUCKET/$RELATIVE_PATH/`
   *
   * @var string
   */
  public $relativePath;

  /**
   * Optional. Enables flag to copy all `/home/jupyter` folder contents
   *
   * @param bool $enableCopyHomeFilesFlag
   */
  public function setEnableCopyHomeFilesFlag($enableCopyHomeFilesFlag)
  {
    $this->enableCopyHomeFilesFlag = $enableCopyHomeFilesFlag;
  }
  /**
   * @return bool
   */
  public function getEnableCopyHomeFilesFlag()
  {
    return $this->enableCopyHomeFilesFlag;
  }
  /**
   * Optional. Enables flag to capture packets from the instance for 30 seconds
   *
   * @param bool $enablePacketCaptureFlag
   */
  public function setEnablePacketCaptureFlag($enablePacketCaptureFlag)
  {
    $this->enablePacketCaptureFlag = $enablePacketCaptureFlag;
  }
  /**
   * @return bool
   */
  public function getEnablePacketCaptureFlag()
  {
    return $this->enablePacketCaptureFlag;
  }
  /**
   * Optional. Enables flag to repair service for instance
   *
   * @param bool $enableRepairFlag
   */
  public function setEnableRepairFlag($enableRepairFlag)
  {
    $this->enableRepairFlag = $enableRepairFlag;
  }
  /**
   * @return bool
   */
  public function getEnableRepairFlag()
  {
    return $this->enableRepairFlag;
  }
  /**
   * Required. User Cloud Storage bucket location (REQUIRED). Must be formatted
   * with path prefix (`gs://$GCS_BUCKET`). Permissions: User Managed Notebooks:
   * - storage.buckets.writer: Must be given to the project's service account
   * attached to VM. Google Managed Notebooks: - storage.buckets.writer: Must be
   * given to the project's service account or user credentials attached to VM
   * depending on authentication mode. Cloud Storage bucket Log file will be
   * written to `gs://$GCS_BUCKET/$RELATIVE_PATH/$VM_DATE_$TIME.tar.gz`
   *
   * @param string $gcsBucket
   */
  public function setGcsBucket($gcsBucket)
  {
    $this->gcsBucket = $gcsBucket;
  }
  /**
   * @return string
   */
  public function getGcsBucket()
  {
    return $this->gcsBucket;
  }
  /**
   * Optional. Defines the relative storage path in the Cloud Storage bucket
   * where the diagnostic logs will be written: Default path will be the root
   * directory of the Cloud Storage bucket
   * (`gs://$GCS_BUCKET/$DATE_$TIME.tar.gz`) Example of full path where Log file
   * will be written: `gs://$GCS_BUCKET/$RELATIVE_PATH/`
   *
   * @param string $relativePath
   */
  public function setRelativePath($relativePath)
  {
    $this->relativePath = $relativePath;
  }
  /**
   * @return string
   */
  public function getRelativePath()
  {
    return $this->relativePath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiagnosticConfig::class, 'Google_Service_AIPlatformNotebooks_DiagnosticConfig');
