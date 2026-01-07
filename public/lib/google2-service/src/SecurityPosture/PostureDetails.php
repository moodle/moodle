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

namespace Google\Service\SecurityPosture;

class PostureDetails extends \Google\Model
{
  /**
   * The identifier for the PolicySet that the relevant policy belongs to.
   *
   * @var string
   */
  public $policySet;
  /**
   * The posture used in the deployment, in the format
   * `organizations/{organization}/locations/global/postures/{posture_id}`.
   *
   * @var string
   */
  public $posture;
  /**
   * The name of the posture deployment, in the format `organizations/{organizat
   * ion}/locations/global/postureDeployments/{deployment_id}`.
   *
   * @var string
   */
  public $postureDeployment;
  /**
   * The organization, folder, or project where the posture is deployed. Uses
   * one of the following formats: * `organizations/{organization_number}` *
   * `folders/{folder_number}` * `projects/{project_number}`
   *
   * @var string
   */
  public $postureDeploymentTargetResource;
  /**
   * The revision ID of the posture used in the deployment.
   *
   * @var string
   */
  public $postureRevisionId;

  /**
   * The identifier for the PolicySet that the relevant policy belongs to.
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
   * The posture used in the deployment, in the format
   * `organizations/{organization}/locations/global/postures/{posture_id}`.
   *
   * @param string $posture
   */
  public function setPosture($posture)
  {
    $this->posture = $posture;
  }
  /**
   * @return string
   */
  public function getPosture()
  {
    return $this->posture;
  }
  /**
   * The name of the posture deployment, in the format `organizations/{organizat
   * ion}/locations/global/postureDeployments/{deployment_id}`.
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
   * The organization, folder, or project where the posture is deployed. Uses
   * one of the following formats: * `organizations/{organization_number}` *
   * `folders/{folder_number}` * `projects/{project_number}`
   *
   * @param string $postureDeploymentTargetResource
   */
  public function setPostureDeploymentTargetResource($postureDeploymentTargetResource)
  {
    $this->postureDeploymentTargetResource = $postureDeploymentTargetResource;
  }
  /**
   * @return string
   */
  public function getPostureDeploymentTargetResource()
  {
    return $this->postureDeploymentTargetResource;
  }
  /**
   * The revision ID of the posture used in the deployment.
   *
   * @param string $postureRevisionId
   */
  public function setPostureRevisionId($postureRevisionId)
  {
    $this->postureRevisionId = $postureRevisionId;
  }
  /**
   * @return string
   */
  public function getPostureRevisionId()
  {
    return $this->postureRevisionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PostureDetails::class, 'Google_Service_SecurityPosture_PostureDetails');
