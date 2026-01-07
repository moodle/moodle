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

namespace Google\Service\Config;

class PreviewOperationMetadata extends \Google\Model
{
  /**
   * Unspecified preview step.
   */
  public const STEP_PREVIEW_STEP_UNSPECIFIED = 'PREVIEW_STEP_UNSPECIFIED';
  /**
   * Infra Manager is creating a Google Cloud Storage bucket to store artifacts
   * and metadata about the preview.
   */
  public const STEP_PREPARING_STORAGE_BUCKET = 'PREPARING_STORAGE_BUCKET';
  /**
   * Downloading the blueprint onto the Google Cloud Storage bucket.
   */
  public const STEP_DOWNLOADING_BLUEPRINT = 'DOWNLOADING_BLUEPRINT';
  /**
   * Initializing Terraform using `terraform init`.
   */
  public const STEP_RUNNING_TF_INIT = 'RUNNING_TF_INIT';
  /**
   * Running `terraform plan`.
   */
  public const STEP_RUNNING_TF_PLAN = 'RUNNING_TF_PLAN';
  /**
   * Fetching a deployment.
   */
  public const STEP_FETCHING_DEPLOYMENT = 'FETCHING_DEPLOYMENT';
  /**
   * Locking a deployment.
   */
  public const STEP_LOCKING_DEPLOYMENT = 'LOCKING_DEPLOYMENT';
  /**
   * Unlocking a deployment.
   */
  public const STEP_UNLOCKING_DEPLOYMENT = 'UNLOCKING_DEPLOYMENT';
  /**
   * Operation was successful.
   */
  public const STEP_SUCCEEDED = 'SUCCEEDED';
  /**
   * Operation failed.
   */
  public const STEP_FAILED = 'FAILED';
  /**
   * Validating the provided repository.
   */
  public const STEP_VALIDATING_REPOSITORY = 'VALIDATING_REPOSITORY';
  /**
   * Output only. Cloud Build instance UUID associated with this preview.
   *
   * @var string
   */
  public $build;
  /**
   * Output only. Location of preview logs in `gs://{bucket}/{object}` format.
   *
   * @var string
   */
  public $logs;
  protected $previewArtifactsType = PreviewArtifacts::class;
  protected $previewArtifactsDataType = '';
  /**
   * The current step the preview operation is running.
   *
   * @var string
   */
  public $step;

  /**
   * Output only. Cloud Build instance UUID associated with this preview.
   *
   * @param string $build
   */
  public function setBuild($build)
  {
    $this->build = $build;
  }
  /**
   * @return string
   */
  public function getBuild()
  {
    return $this->build;
  }
  /**
   * Output only. Location of preview logs in `gs://{bucket}/{object}` format.
   *
   * @param string $logs
   */
  public function setLogs($logs)
  {
    $this->logs = $logs;
  }
  /**
   * @return string
   */
  public function getLogs()
  {
    return $this->logs;
  }
  /**
   * Artifacts from preview.
   *
   * @param PreviewArtifacts $previewArtifacts
   */
  public function setPreviewArtifacts(PreviewArtifacts $previewArtifacts)
  {
    $this->previewArtifacts = $previewArtifacts;
  }
  /**
   * @return PreviewArtifacts
   */
  public function getPreviewArtifacts()
  {
    return $this->previewArtifacts;
  }
  /**
   * The current step the preview operation is running.
   *
   * Accepted values: PREVIEW_STEP_UNSPECIFIED, PREPARING_STORAGE_BUCKET,
   * DOWNLOADING_BLUEPRINT, RUNNING_TF_INIT, RUNNING_TF_PLAN,
   * FETCHING_DEPLOYMENT, LOCKING_DEPLOYMENT, UNLOCKING_DEPLOYMENT, SUCCEEDED,
   * FAILED, VALIDATING_REPOSITORY
   *
   * @param self::STEP_* $step
   */
  public function setStep($step)
  {
    $this->step = $step;
  }
  /**
   * @return self::STEP_*
   */
  public function getStep()
  {
    return $this->step;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PreviewOperationMetadata::class, 'Google_Service_Config_PreviewOperationMetadata');
