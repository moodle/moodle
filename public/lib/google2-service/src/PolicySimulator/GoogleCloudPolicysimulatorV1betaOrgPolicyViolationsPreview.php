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

class GoogleCloudPolicysimulatorV1betaOrgPolicyViolationsPreview extends \Google\Collection
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
  protected $collection_key = 'customConstraints';
  /**
   * Output only. Time when this `OrgPolicyViolationsPreview` was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The names of the constraints against which all
   * `OrgPolicyViolations` were evaluated. If `OrgPolicyOverlay` only contains
   * `PolicyOverlay` then it contains the name of the configured custom
   * constraint, applicable to the specified policies. Otherwise it contains the
   * name of the constraint specified in `CustomConstraintOverlay`. Format:
   * `organizations/{organization_id}/customConstraints/{custom_constraint_id}`
   * Example: `organizations/123/customConstraints/custom.createOnlyE2TypeVms`
   *
   * @var string[]
   */
  public $customConstraints;
  /**
   * Output only. The resource name of the `OrgPolicyViolationsPreview`. It has
   * the following format: `organizations/{organization}/locations/{location}/or
   * gPolicyViolationsPreviews/{orgPolicyViolationsPreview}` Example:
   * `organizations/my-example-
   * org/locations/global/orgPolicyViolationsPreviews/506a5f7f`
   *
   * @var string
   */
  public $name;
  protected $overlayType = GoogleCloudPolicysimulatorV1betaOrgPolicyOverlay::class;
  protected $overlayDataType = '';
  protected $resourceCountsType = GoogleCloudPolicysimulatorV1betaOrgPolicyViolationsPreviewResourceCounts::class;
  protected $resourceCountsDataType = '';
  /**
   * Output only. The state of the `OrgPolicyViolationsPreview`.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The number of OrgPolicyViolations in this
   * `OrgPolicyViolationsPreview`. This count may differ from
   * `resource_summary.noncompliant_count` because each OrgPolicyViolation is
   * specific to a resource **and** constraint. If there are multiple
   * constraints being evaluated (i.e. multiple policies in the overlay), a
   * single resource may violate multiple constraints.
   *
   * @var int
   */
  public $violationsCount;

  /**
   * Output only. Time when this `OrgPolicyViolationsPreview` was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The names of the constraints against which all
   * `OrgPolicyViolations` were evaluated. If `OrgPolicyOverlay` only contains
   * `PolicyOverlay` then it contains the name of the configured custom
   * constraint, applicable to the specified policies. Otherwise it contains the
   * name of the constraint specified in `CustomConstraintOverlay`. Format:
   * `organizations/{organization_id}/customConstraints/{custom_constraint_id}`
   * Example: `organizations/123/customConstraints/custom.createOnlyE2TypeVms`
   *
   * @param string[] $customConstraints
   */
  public function setCustomConstraints($customConstraints)
  {
    $this->customConstraints = $customConstraints;
  }
  /**
   * @return string[]
   */
  public function getCustomConstraints()
  {
    return $this->customConstraints;
  }
  /**
   * Output only. The resource name of the `OrgPolicyViolationsPreview`. It has
   * the following format: `organizations/{organization}/locations/{location}/or
   * gPolicyViolationsPreviews/{orgPolicyViolationsPreview}` Example:
   * `organizations/my-example-
   * org/locations/global/orgPolicyViolationsPreviews/506a5f7f`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. The proposed changes we are previewing violations for.
   *
   * @param GoogleCloudPolicysimulatorV1betaOrgPolicyOverlay $overlay
   */
  public function setOverlay(GoogleCloudPolicysimulatorV1betaOrgPolicyOverlay $overlay)
  {
    $this->overlay = $overlay;
  }
  /**
   * @return GoogleCloudPolicysimulatorV1betaOrgPolicyOverlay
   */
  public function getOverlay()
  {
    return $this->overlay;
  }
  /**
   * Output only. A summary of the state of all resources scanned for compliance
   * with the changed OrgPolicy.
   *
   * @param GoogleCloudPolicysimulatorV1betaOrgPolicyViolationsPreviewResourceCounts $resourceCounts
   */
  public function setResourceCounts(GoogleCloudPolicysimulatorV1betaOrgPolicyViolationsPreviewResourceCounts $resourceCounts)
  {
    $this->resourceCounts = $resourceCounts;
  }
  /**
   * @return GoogleCloudPolicysimulatorV1betaOrgPolicyViolationsPreviewResourceCounts
   */
  public function getResourceCounts()
  {
    return $this->resourceCounts;
  }
  /**
   * Output only. The state of the `OrgPolicyViolationsPreview`.
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
  /**
   * Output only. The number of OrgPolicyViolations in this
   * `OrgPolicyViolationsPreview`. This count may differ from
   * `resource_summary.noncompliant_count` because each OrgPolicyViolation is
   * specific to a resource **and** constraint. If there are multiple
   * constraints being evaluated (i.e. multiple policies in the overlay), a
   * single resource may violate multiple constraints.
   *
   * @param int $violationsCount
   */
  public function setViolationsCount($violationsCount)
  {
    $this->violationsCount = $violationsCount;
  }
  /**
   * @return int
   */
  public function getViolationsCount()
  {
    return $this->violationsCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicysimulatorV1betaOrgPolicyViolationsPreview::class, 'Google_Service_PolicySimulator_GoogleCloudPolicysimulatorV1betaOrgPolicyViolationsPreview');
