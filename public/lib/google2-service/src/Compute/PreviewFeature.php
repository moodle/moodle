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

namespace Google\Service\Compute;

class PreviewFeature extends \Google\Model
{
  public const ACTIVATION_STATUS_ACTIVATION_STATE_UNSPECIFIED = 'ACTIVATION_STATE_UNSPECIFIED';
  public const ACTIVATION_STATUS_DISABLED = 'DISABLED';
  public const ACTIVATION_STATUS_ENABLED = 'ENABLED';
  /**
   * Specifies whether the feature is enabled or disabled.
   *
   * @var string
   */
  public $activationStatus;
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * Output only. [Output Only] Description of the feature.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output only] The type of the feature. Always
   * "compute#previewFeature" for preview features.
   *
   * @var string
   */
  public $kind;
  /**
   * Name of the feature.
   *
   * @var string
   */
  public $name;
  protected $rolloutOperationType = PreviewFeatureRolloutOperation::class;
  protected $rolloutOperationDataType = '';
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  protected $statusType = PreviewFeatureStatus::class;
  protected $statusDataType = '';

  /**
   * Specifies whether the feature is enabled or disabled.
   *
   * Accepted values: ACTIVATION_STATE_UNSPECIFIED, DISABLED, ENABLED
   *
   * @param self::ACTIVATION_STATUS_* $activationStatus
   */
  public function setActivationStatus($activationStatus)
  {
    $this->activationStatus = $activationStatus;
  }
  /**
   * @return self::ACTIVATION_STATUS_*
   */
  public function getActivationStatus()
  {
    return $this->activationStatus;
  }
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * Output only. [Output Only] Description of the feature.
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
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. [Output only] The type of the feature. Always
   * "compute#previewFeature" for preview features.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Name of the feature.
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
   * Rollout operation of the feature.
   *
   * @param PreviewFeatureRolloutOperation $rolloutOperation
   */
  public function setRolloutOperation(PreviewFeatureRolloutOperation $rolloutOperation)
  {
    $this->rolloutOperation = $rolloutOperation;
  }
  /**
   * @return PreviewFeatureRolloutOperation
   */
  public function getRolloutOperation()
  {
    return $this->rolloutOperation;
  }
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Output only. [Output only] Status of the feature.
   *
   * @param PreviewFeatureStatus $status
   */
  public function setStatus(PreviewFeatureStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return PreviewFeatureStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PreviewFeature::class, 'Google_Service_Compute_PreviewFeature');
