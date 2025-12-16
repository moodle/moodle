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

namespace Google\Service\PolicySimulator;

class GoogleCloudPolicysimulatorV1betaCreateOrgPolicyViolationsPreviewOperationMetadata extends \Google\Model
{
  /**
   * The state is unspecified.
   */
  public const STATE_PREVIEW_STATE_UNSPECIFIED = 'PREVIEW_STATE_UNSPECIFIED';
  /**
   * The OrgPolicyViolationsPreview has not been created yet.
   */
  public const STATE_PREVIEW_PENDING = 'PREVIEW_PENDING';
  /**
   * The OrgPolicyViolationsPreview is currently being created.
   */
  public const STATE_PREVIEW_RUNNING = 'PREVIEW_RUNNING';
  /**
   * The OrgPolicyViolationsPreview creation finished successfully.
   */
  public const STATE_PREVIEW_SUCCEEDED = 'PREVIEW_SUCCEEDED';
  /**
   * The OrgPolicyViolationsPreview creation failed with an error.
   */
  public const STATE_PREVIEW_FAILED = 'PREVIEW_FAILED';
  /**
   * Time when the request was received.
   *
   * @var string
   */
  public $requestTime;
  /**
   * Total number of resources that need scanning. Should equal resource_scanned
   * + resources_pending
   *
   * @var int
   */
  public $resourcesFound;
  /**
   * Number of resources still to scan.
   *
   * @var int
   */
  public $resourcesPending;
  /**
   * Number of resources already scanned.
   *
   * @var int
   */
  public $resourcesScanned;
  /**
   * Time when the request started processing, i.e., when the state was set to
   * RUNNING.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The current state of the operation.
   *
   * @var string
   */
  public $state;

  /**
   * Time when the request was received.
   *
   * @param string $requestTime
   */
  public function setRequestTime($requestTime)
  {
    $this->requestTime = $requestTime;
  }
  /**
   * @return string
   */
  public function getRequestTime()
  {
    return $this->requestTime;
  }
  /**
   * Total number of resources that need scanning. Should equal resource_scanned
   * + resources_pending
   *
   * @param int $resourcesFound
   */
  public function setResourcesFound($resourcesFound)
  {
    $this->resourcesFound = $resourcesFound;
  }
  /**
   * @return int
   */
  public function getResourcesFound()
  {
    return $this->resourcesFound;
  }
  /**
   * Number of resources still to scan.
   *
   * @param int $resourcesPending
   */
  public function setResourcesPending($resourcesPending)
  {
    $this->resourcesPending = $resourcesPending;
  }
  /**
   * @return int
   */
  public function getResourcesPending()
  {
    return $this->resourcesPending;
  }
  /**
   * Number of resources already scanned.
   *
   * @param int $resourcesScanned
   */
  public function setResourcesScanned($resourcesScanned)
  {
    $this->resourcesScanned = $resourcesScanned;
  }
  /**
   * @return int
   */
  public function getResourcesScanned()
  {
    return $this->resourcesScanned;
  }
  /**
   * Time when the request started processing, i.e., when the state was set to
   * RUNNING.
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
   * Output only. The current state of the operation.
   *
   * Accepted values: PREVIEW_STATE_UNSPECIFIED, PREVIEW_PENDING,
   * PREVIEW_RUNNING, PREVIEW_SUCCEEDED, PREVIEW_FAILED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicysimulatorV1betaCreateOrgPolicyViolationsPreviewOperationMetadata::class, 'Google_Service_PolicySimulator_GoogleCloudPolicysimulatorV1betaCreateOrgPolicyViolationsPreviewOperationMetadata');
