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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2SecurityPosture extends \Google\Collection
{
  protected $collection_key = 'policyDriftDetails';
  /**
   * The name of the updated policy, for example,
   * `projects/{project_id}/policies/{constraint_name}`.
   *
   * @var string
   */
  public $changedPolicy;
  /**
   * Name of the posture, for example, `CIS-Posture`.
   *
   * @var string
   */
  public $name;
  /**
   * The ID of the updated policy, for example, `compute-policy-1`.
   *
   * @var string
   */
  public $policy;
  protected $policyDriftDetailsType = GoogleCloudSecuritycenterV2PolicyDriftDetails::class;
  protected $policyDriftDetailsDataType = 'array';
  /**
   * The name of the updated policy set, for example, `cis-policyset`.
   *
   * @var string
   */
  public $policySet;
  /**
   * The name of the posture deployment, for example,
   * `organizations/{org_id}/posturedeployments/{posture_deployment_id}`.
   *
   * @var string
   */
  public $postureDeployment;
  /**
   * The project, folder, or organization on which the posture is deployed, for
   * example, `projects/{project_number}`.
   *
   * @var string
   */
  public $postureDeploymentResource;
  /**
   * The version of the posture, for example, `c7cfa2a8`.
   *
   * @var string
   */
  public $revisionId;

  /**
   * The name of the updated policy, for example,
   * `projects/{project_id}/policies/{constraint_name}`.
   *
   * @param string $changedPolicy
   */
  public function setChangedPolicy($changedPolicy)
  {
    $this->changedPolicy = $changedPolicy;
  }
  /**
   * @return string
   */
  public function getChangedPolicy()
  {
    return $this->changedPolicy;
  }
  /**
   * Name of the posture, for example, `CIS-Posture`.
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
   * The ID of the updated policy, for example, `compute-policy-1`.
   *
   * @param string $policy
   */
  public function setPolicy($policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return string
   */
  public function getPolicy()
  {
    return $this->policy;
  }
  /**
   * The details about a change in an updated policy that violates the deployed
   * posture.
   *
   * @param GoogleCloudSecuritycenterV2PolicyDriftDetails[] $policyDriftDetails
   */
  public function setPolicyDriftDetails($policyDriftDetails)
  {
    $this->policyDriftDetails = $policyDriftDetails;
  }
  /**
   * @return GoogleCloudSecuritycenterV2PolicyDriftDetails[]
   */
  public function getPolicyDriftDetails()
  {
    return $this->policyDriftDetails;
  }
  /**
   * The name of the updated policy set, for example, `cis-policyset`.
   *
   * @param string $policySet
   */
  public function setPolicySet($policySet)
  {
    $this->policySet = $policySet;
  }
  /**
   * @return string
   */
  public function getPolicySet()
  {
    return $this->policySet;
  }
  /**
   * The name of the posture deployment, for example,
   * `organizations/{org_id}/posturedeployments/{posture_deployment_id}`.
   *
   * @param string $postureDeployment
   */
  public function setPostureDeployment($postureDeployment)
  {
    $this->postureDeployment = $postureDeployment;
  }
  /**
   * @return string
   */
  public function getPostureDeployment()
  {
    return $this->postureDeployment;
  }
  /**
   * The project, folder, or organization on which the posture is deployed, for
   * example, `projects/{project_number}`.
   *
   * @param string $postureDeploymentResource
   */
  public function setPostureDeploymentResource($postureDeploymentResource)
  {
    $this->postureDeploymentResource = $postureDeploymentResource;
  }
  /**
   * @return string
   */
  public function getPostureDeploymentResource()
  {
    return $this->postureDeploymentResource;
  }
  /**
   * The version of the posture, for example, `c7cfa2a8`.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2SecurityPosture::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2SecurityPosture');
