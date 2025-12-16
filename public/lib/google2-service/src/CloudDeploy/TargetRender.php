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

class TargetRender extends \Google\Model
{
  /**
   * No reason for failure is specified.
   */
  public const FAILURE_CAUSE_FAILURE_CAUSE_UNSPECIFIED = 'FAILURE_CAUSE_UNSPECIFIED';
  /**
   * Cloud Build is not available, either because it is not enabled or because
   * Cloud Deploy has insufficient permissions. See [required
   * permission](https://cloud.google.com/deploy/docs/cloud-deploy-service-
   * account#required_permissions).
   */
  public const FAILURE_CAUSE_CLOUD_BUILD_UNAVAILABLE = 'CLOUD_BUILD_UNAVAILABLE';
  /**
   * The render operation did not complete successfully; check Cloud Build logs.
   */
  public const FAILURE_CAUSE_EXECUTION_FAILED = 'EXECUTION_FAILED';
  /**
   * Cloud Build failed to fulfill Cloud Deploy's request. See failure_message
   * for additional details.
   */
  public const FAILURE_CAUSE_CLOUD_BUILD_REQUEST_FAILED = 'CLOUD_BUILD_REQUEST_FAILED';
  /**
   * The render operation did not complete successfully because the verification
   * stanza required for verify was not found on the Skaffold configuration.
   */
  public const FAILURE_CAUSE_VERIFICATION_CONFIG_NOT_FOUND = 'VERIFICATION_CONFIG_NOT_FOUND';
  /**
   * The render operation did not complete successfully because the custom
   * action(s) required for Rollout jobs were not found in the Skaffold
   * configuration. See failure_message for additional details.
   */
  public const FAILURE_CAUSE_CUSTOM_ACTION_NOT_FOUND = 'CUSTOM_ACTION_NOT_FOUND';
  /**
   * Release failed during rendering because the release configuration is not
   * supported with the specified deployment strategy.
   */
  public const FAILURE_CAUSE_DEPLOYMENT_STRATEGY_NOT_SUPPORTED = 'DEPLOYMENT_STRATEGY_NOT_SUPPORTED';
  /**
   * The render operation had a feature configured that is not supported.
   */
  public const FAILURE_CAUSE_RENDER_FEATURE_NOT_SUPPORTED = 'RENDER_FEATURE_NOT_SUPPORTED';
  /**
   * The render operation state is unspecified.
   */
  public const RENDERING_STATE_TARGET_RENDER_STATE_UNSPECIFIED = 'TARGET_RENDER_STATE_UNSPECIFIED';
  /**
   * The render operation has completed successfully.
   */
  public const RENDERING_STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The render operation has failed.
   */
  public const RENDERING_STATE_FAILED = 'FAILED';
  /**
   * The render operation is in progress.
   */
  public const RENDERING_STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Output only. Reason this render failed. This will always be unspecified
   * while the render in progress.
   *
   * @var string
   */
  public $failureCause;
  /**
   * Output only. Additional information about the render failure, if available.
   *
   * @var string
   */
  public $failureMessage;
  protected $metadataType = RenderMetadata::class;
  protected $metadataDataType = '';
  /**
   * Output only. The resource name of the Cloud Build `Build` object that is
   * used to render the manifest for this target. Format is
   * `projects/{project}/locations/{location}/builds/{build}`.
   *
   * @var string
   */
  public $renderingBuild;
  /**
   * Output only. Current state of the render operation for this Target.
   *
   * @var string
   */
  public $renderingState;

  /**
   * Output only. Reason this render failed. This will always be unspecified
   * while the render in progress.
   *
   * Accepted values: FAILURE_CAUSE_UNSPECIFIED, CLOUD_BUILD_UNAVAILABLE,
   * EXECUTION_FAILED, CLOUD_BUILD_REQUEST_FAILED,
   * VERIFICATION_CONFIG_NOT_FOUND, CUSTOM_ACTION_NOT_FOUND,
   * DEPLOYMENT_STRATEGY_NOT_SUPPORTED, RENDER_FEATURE_NOT_SUPPORTED
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
   * Output only. Additional information about the render failure, if available.
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
   * Output only. Metadata related to the `Release` render for this Target.
   *
   * @param RenderMetadata $metadata
   */
  public function setMetadata(RenderMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return RenderMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Output only. The resource name of the Cloud Build `Build` object that is
   * used to render the manifest for this target. Format is
   * `projects/{project}/locations/{location}/builds/{build}`.
   *
   * @param string $renderingBuild
   */
  public function setRenderingBuild($renderingBuild)
  {
    $this->renderingBuild = $renderingBuild;
  }
  /**
   * @return string
   */
  public function getRenderingBuild()
  {
    return $this->renderingBuild;
  }
  /**
   * Output only. Current state of the render operation for this Target.
   *
   * Accepted values: TARGET_RENDER_STATE_UNSPECIFIED, SUCCEEDED, FAILED,
   * IN_PROGRESS
   *
   * @param self::RENDERING_STATE_* $renderingState
   */
  public function setRenderingState($renderingState)
  {
    $this->renderingState = $renderingState;
  }
  /**
   * @return self::RENDERING_STATE_*
   */
  public function getRenderingState()
  {
    return $this->renderingState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TargetRender::class, 'Google_Service_CloudDeploy_TargetRender');
