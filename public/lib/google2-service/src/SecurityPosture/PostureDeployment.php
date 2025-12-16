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

class PostureDeployment extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The posture deployment is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The posture deployment is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The posture deployment is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The posture deployment is active and in use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The posture deployment could not be created.
   */
  public const STATE_CREATE_FAILED = 'CREATE_FAILED';
  /**
   * The posture deployment could not be updated.
   */
  public const STATE_UPDATE_FAILED = 'UPDATE_FAILED';
  /**
   * The posture deployment could not be deleted.
   */
  public const STATE_DELETE_FAILED = 'DELETE_FAILED';
  protected $collection_key = 'categories';
  /**
   * Optional. The user-specified annotations for the posture deployment. For
   * details about the values you can use in an annotation, see [AIP-148:
   * Standard fields](https://google.aip.dev/148#annotations).
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. The categories that the posture deployment belongs to, as
   * determined by the Security Posture API.
   *
   * @var string[]
   */
  public $categories;
  /**
   * Output only. The time at which the posture deployment was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A description of the posture deployment.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The posture ID that was specified for the deployment. Present
   * only if the posture deployment is in a failed state.
   *
   * @var string
   */
  public $desiredPostureId;
  /**
   * Output only. The revision ID of the posture that was specified for the
   * deployment. Present only if the deployment is in a failed state.
   *
   * @var string
   */
  public $desiredPostureRevisionId;
  /**
   * Optional. An opaque identifier for the current version of the posture
   * deployment. To prevent concurrent updates from overwriting each other,
   * always provide the `etag` when you update a posture deployment. You can
   * also provide the `etag` when you delete a posture deployment, to help
   * ensure that you're deleting the intended posture deployment.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. A description of why the posture deployment failed. Present
   * only if the deployment is in a failed state.
   *
   * @var string
   */
  public $failureMessage;
  /**
   * Required. Identifier. The name of the posture deployment, in the format `or
   * ganizations/{organization}/locations/global/postureDeployments/{deployment_
   * id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The posture used in the deployment, in the format
   * `organizations/{organization}/locations/global/postures/{posture_id}`.
   *
   * @var string
   */
  public $postureId;
  /**
   * Required. The revision ID of the posture used in the deployment.
   *
   * @var string
   */
  public $postureRevisionId;
  /**
   * Output only. Whether the posture deployment is in the process of being
   * updated.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. The state of the posture deployment.
   *
   * @var string
   */
  public $state;
  /**
   * Required. The organization, folder, or project where the posture is
   * deployed. Uses one of the following formats: *
   * `organizations/{organization_number}` * `folders/{folder_number}` *
   * `projects/{project_number}`
   *
   * @var string
   */
  public $targetResource;
  /**
   * Output only. The time at which the posture deployment was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The user-specified annotations for the posture deployment. For
   * details about the values you can use in an annotation, see [AIP-148:
   * Standard fields](https://google.aip.dev/148#annotations).
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. The categories that the posture deployment belongs to, as
   * determined by the Security Posture API.
   *
   * @param string[] $categories
   */
  public function setCategories($categories)
  {
    $this->categories = $categories;
  }
  /**
   * @return string[]
   */
  public function getCategories()
  {
    return $this->categories;
  }
  /**
   * Output only. The time at which the posture deployment was created.
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
   * Optional. A description of the posture deployment.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. The posture ID that was specified for the deployment. Present
   * only if the posture deployment is in a failed state.
   *
   * @param string $desiredPostureId
   */
  public function setDesiredPostureId($desiredPostureId)
  {
    $this->desiredPostureId = $desiredPostureId;
  }
  /**
   * @return string
   */
  public function getDesiredPostureId()
  {
    return $this->desiredPostureId;
  }
  /**
   * Output only. The revision ID of the posture that was specified for the
   * deployment. Present only if the deployment is in a failed state.
   *
   * @param string $desiredPostureRevisionId
   */
  public function setDesiredPostureRevisionId($desiredPostureRevisionId)
  {
    $this->desiredPostureRevisionId = $desiredPostureRevisionId;
  }
  /**
   * @return string
   */
  public function getDesiredPostureRevisionId()
  {
    return $this->desiredPostureRevisionId;
  }
  /**
   * Optional. An opaque identifier for the current version of the posture
   * deployment. To prevent concurrent updates from overwriting each other,
   * always provide the `etag` when you update a posture deployment. You can
   * also provide the `etag` when you delete a posture deployment, to help
   * ensure that you're deleting the intended posture deployment.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. A description of why the posture deployment failed. Present
   * only if the deployment is in a failed state.
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
   * Required. Identifier. The name of the posture deployment, in the format `or
   * ganizations/{organization}/locations/global/postureDeployments/{deployment_
   * id}`.
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
   * Required. The posture used in the deployment, in the format
   * `organizations/{organization}/locations/global/postures/{posture_id}`.
   *
   * @param string $postureId
   */
  public function setPostureId($postureId)
  {
    $this->postureId = $postureId;
  }
  /**
   * @return string
   */
  public function getPostureId()
  {
    return $this->postureId;
  }
  /**
   * Required. The revision ID of the posture used in the deployment.
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
  /**
   * Output only. Whether the posture deployment is in the process of being
   * updated.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Output only. The state of the posture deployment.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, DELETING, UPDATING, ACTIVE,
   * CREATE_FAILED, UPDATE_FAILED, DELETE_FAILED
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
   * Required. The organization, folder, or project where the posture is
   * deployed. Uses one of the following formats: *
   * `organizations/{organization_number}` * `folders/{folder_number}` *
   * `projects/{project_number}`
   *
   * @param string $targetResource
   */
  public function setTargetResource($targetResource)
  {
    $this->targetResource = $targetResource;
  }
  /**
   * @return string
   */
  public function getTargetResource()
  {
    return $this->targetResource;
  }
  /**
   * Output only. The time at which the posture deployment was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PostureDeployment::class, 'Google_Service_SecurityPosture_PostureDeployment');
