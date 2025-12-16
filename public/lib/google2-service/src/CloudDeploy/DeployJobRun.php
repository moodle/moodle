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

namespace Google\Service\CloudDeploy;

class DeployJobRun extends \Google\Model
{
  /**
   * No reason for failure is specified.
   */
  public const FAILURE_CAUSE_FAILURE_CAUSE_UNSPECIFIED = 'FAILURE_CAUSE_UNSPECIFIED';
  /**
   * Cloud Build is not available, either because it is not enabled or because
   * Cloud Deploy has insufficient permissions. See [Required
   * permission](https://cloud.google.com/deploy/docs/cloud-deploy-service-
   * account#required_permissions).
   */
  public const FAILURE_CAUSE_CLOUD_BUILD_UNAVAILABLE = 'CLOUD_BUILD_UNAVAILABLE';
  /**
   * The deploy operation did not complete successfully; check Cloud Build logs.
   */
  public const FAILURE_CAUSE_EXECUTION_FAILED = 'EXECUTION_FAILED';
  /**
   * The deploy job run did not complete within the allotted time.
   */
  public const FAILURE_CAUSE_DEADLINE_EXCEEDED = 'DEADLINE_EXCEEDED';
  /**
   * There were missing resources in the runtime environment required for a
   * canary deployment. Check the Cloud Build logs for more information.
   */
  public const FAILURE_CAUSE_MISSING_RESOURCES_FOR_CANARY = 'MISSING_RESOURCES_FOR_CANARY';
  /**
   * Cloud Build failed to fulfill Cloud Deploy's request. See failure_message
   * for additional details.
   */
  public const FAILURE_CAUSE_CLOUD_BUILD_REQUEST_FAILED = 'CLOUD_BUILD_REQUEST_FAILED';
  /**
   * The deploy operation had a feature configured that is not supported.
   */
  public const FAILURE_CAUSE_DEPLOY_FEATURE_NOT_SUPPORTED = 'DEPLOY_FEATURE_NOT_SUPPORTED';
  protected $artifactType = DeployArtifact::class;
  protected $artifactDataType = '';
  /**
   * Output only. The resource name of the Cloud Build `Build` object that is
   * used to deploy. Format is
   * `projects/{project}/locations/{location}/builds/{build}`.
   *
   * @var string
   */
  public $build;
  /**
   * Output only. The reason the deploy failed. This will always be unspecified
   * while the deploy is in progress or if it succeeded.
   *
   * @var string
   */
  public $failureCause;
  /**
   * Output only. Additional information about the deploy failure, if available.
   *
   * @var string
   */
  public $failureMessage;
  protected $metadataType = DeployJobRunMetadata::class;
  protected $metadataDataType = '';

  /**
   * Output only. The artifact of a deploy job run, if available.
   *
   * @param DeployArtifact $artifact
   */
  public function setArtifact(DeployArtifact $artifact)
  {
    $this->artifact = $artifact;
  }
  /**
   * @return DeployArtifact
   */
  public function getArtifact()
  {
    return $this->artifact;
  }
  /**
   * Output only. The resource name of the Cloud Build `Build` object that is
   * used to deploy. Format is
   * `projects/{project}/locations/{location}/builds/{build}`.
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
   * Output only. The reason the deploy failed. This will always be unspecified
   * while the deploy is in progress or if it succeeded.
   *
   * Accepted values: FAILURE_CAUSE_UNSPECIFIED, CLOUD_BUILD_UNAVAILABLE,
   * EXECUTION_FAILED, DEADLINE_EXCEEDED, MISSING_RESOURCES_FOR_CANARY,
   * CLOUD_BUILD_REQUEST_FAILED, DEPLOY_FEATURE_NOT_SUPPORTED
   *
   * @param self::FAILURE_CAUSE_* $failureCause
   */
  public function setFailureCause($failureCause)
  {
    $this->failureCause = $failureCause;
  }
  /**
   * @return self::FAILURE_CAUSE_*
   */
  public function getFailureCause()
  {
    return $this->failureCause;
  }
  /**
   * Output only. Additional information about the deploy failure, if available.
   *
   * @param string $failureMessage
   */
  public function setFailureMessage($failureMessage)
  {
    $this->failureMessage = $failureMessage;
  }
  /**
   * @return string
   */
  public function getFailureMessage()
  {
    return $this->failureMessage;
  }
  /**
   * Output only. Metadata containing information about the deploy job run.
   *
   * @param DeployJobRunMetadata $metadata
   */
  public function setMetadata(DeployJobRunMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return DeployJobRunMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeployJobRun::class, 'Google_Service_CloudDeploy_DeployJobRun');
