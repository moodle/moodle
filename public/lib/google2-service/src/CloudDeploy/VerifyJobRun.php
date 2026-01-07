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

class VerifyJobRun extends \Google\Model
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
   * The verify operation did not complete successfully; check Cloud Build logs.
   */
  public const FAILURE_CAUSE_EXECUTION_FAILED = 'EXECUTION_FAILED';
  /**
   * The verify job run did not complete within the allotted time.
   */
  public const FAILURE_CAUSE_DEADLINE_EXCEEDED = 'DEADLINE_EXCEEDED';
  /**
   * No Skaffold verify configuration was found.
   */
  public const FAILURE_CAUSE_VERIFICATION_CONFIG_NOT_FOUND = 'VERIFICATION_CONFIG_NOT_FOUND';
  /**
   * Cloud Build failed to fulfill Cloud Deploy's request. See failure_message
   * for additional details.
   */
  public const FAILURE_CAUSE_CLOUD_BUILD_REQUEST_FAILED = 'CLOUD_BUILD_REQUEST_FAILED';
  /**
   * Output only. URI of a directory containing the verify artifacts. This
   * contains the Skaffold event log.
   *
   * @var string
   */
  public $artifactUri;
  /**
   * Output only. The resource name of the Cloud Build `Build` object that is
   * used to verify. Format is
   * `projects/{project}/locations/{location}/builds/{build}`.
   *
   * @var string
   */
  public $build;
  /**
   * Output only. File path of the Skaffold event log relative to the artifact
   * URI.
   *
   * @var string
   */
  public $eventLogPath;
  /**
   * Output only. The reason the verify failed. This will always be unspecified
   * while the verify is in progress or if it succeeded.
   *
   * @var string
   */
  public $failureCause;
  /**
   * Output only. Additional information about the verify failure, if available.
   *
   * @var string
   */
  public $failureMessage;

  /**
   * Output only. URI of a directory containing the verify artifacts. This
   * contains the Skaffold event log.
   *
   * @param string $artifactUri
   */
  public function setArtifactUri($artifactUri)
  {
    $this->artifactUri = $artifactUri;
  }
  /**
   * @return string
   */
  public function getArtifactUri()
  {
    return $this->artifactUri;
  }
  /**
   * Output only. The resource name of the Cloud Build `Build` object that is
   * used to verify. Format is
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
   * Output only. File path of the Skaffold event log relative to the artifact
   * URI.
   *
   * @param string $eventLogPath
   */
  public function setEventLogPath($eventLogPath)
  {
    $this->eventLogPath = $eventLogPath;
  }
  /**
   * @return string
   */
  public function getEventLogPath()
  {
    return $this->eventLogPath;
  }
  /**
   * Output only. The reason the verify failed. This will always be unspecified
   * while the verify is in progress or if it succeeded.
   *
   * Accepted values: FAILURE_CAUSE_UNSPECIFIED, CLOUD_BUILD_UNAVAILABLE,
   * EXECUTION_FAILED, DEADLINE_EXCEEDED, VERIFICATION_CONFIG_NOT_FOUND,
   * CLOUD_BUILD_REQUEST_FAILED
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
   * Output only. Additional information about the verify failure, if available.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VerifyJobRun::class, 'Google_Service_CloudDeploy_VerifyJobRun');
