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

class Release extends \Google\Collection
{
  /**
   * The render state is unspecified.
   */
  public const RENDER_STATE_RENDER_STATE_UNSPECIFIED = 'RENDER_STATE_UNSPECIFIED';
  /**
   * All rendering operations have completed successfully.
   */
  public const RENDER_STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * All rendering operations have completed, and one or more have failed.
   */
  public const RENDER_STATE_FAILED = 'FAILED';
  /**
   * Rendering has started and is not complete.
   */
  public const RENDER_STATE_IN_PROGRESS = 'IN_PROGRESS';
  protected $collection_key = 'targetSnapshots';
  /**
   * Output only. Indicates whether this is an abandoned release.
   *
   * @var bool
   */
  public $abandoned;
  /**
   * Optional. User annotations. These attributes can only be set and used by
   * the user, and not by Cloud Deploy. See
   * https://google.aip.dev/128#annotations for more details such as format and
   * size limitations.
   *
   * @var string[]
   */
  public $annotations;
  protected $buildArtifactsType = BuildArtifact::class;
  protected $buildArtifactsDataType = 'array';
  protected $conditionType = ReleaseCondition::class;
  protected $conditionDataType = '';
  /**
   * Output only. Time at which the `Release` was created.
   *
   * @var string
   */
  public $createTime;
  protected $customTargetTypeSnapshotsType = CustomTargetType::class;
  protected $customTargetTypeSnapshotsDataType = 'array';
  protected $deliveryPipelineSnapshotType = DeliveryPipeline::class;
  protected $deliveryPipelineSnapshotDataType = '';
  /**
   * Optional. The deploy parameters to use for all targets in this release.
   *
   * @var string[]
   */
  public $deployParameters;
  /**
   * Optional. Description of the `Release`. Max length is 255 characters.
   *
   * @var string
   */
  public $description;
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Labels are attributes that can be set and used by both the user and by
   * Cloud Deploy. Labels must meet the following constraints: * Keys and values
   * can contain only lowercase letters, numeric characters, underscores, and
   * dashes. * All characters must use UTF-8 encoding, and international
   * characters are allowed. * Keys must start with a lowercase letter or
   * international character. * Each resource is limited to a maximum of 64
   * labels. Both keys and values are additionally constrained to be <= 128
   * bytes.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Name of the `Release`. Format is `projects/{project}/locations/
   * {location}/deliveryPipelines/{deliveryPipeline}/releases/{release}`. The
   * `release` component must match `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Time at which the render completed.
   *
   * @var string
   */
  public $renderEndTime;
  /**
   * Output only. Time at which the render began.
   *
   * @var string
   */
  public $renderStartTime;
  /**
   * Output only. Current state of the render operation.
   *
   * @var string
   */
  public $renderState;
  /**
   * Optional. Filepath of the Skaffold config inside of the config URI.
   *
   * @var string
   */
  public $skaffoldConfigPath;
  /**
   * Optional. Cloud Storage URI of tar.gz archive containing Skaffold
   * configuration.
   *
   * @var string
   */
  public $skaffoldConfigUri;
  /**
   * Optional. The Skaffold version to use when operating on this release, such
   * as "1.20.0". Not all versions are valid; Cloud Deploy supports a specific
   * set of versions. If unset, the most recent supported Skaffold version will
   * be used.
   *
   * @var string
   */
  public $skaffoldVersion;
  protected $targetArtifactsType = TargetArtifact::class;
  protected $targetArtifactsDataType = 'map';
  protected $targetRendersType = TargetRender::class;
  protected $targetRendersDataType = 'map';
  protected $targetSnapshotsType = Target::class;
  protected $targetSnapshotsDataType = 'array';
  protected $toolVersionsType = ToolVersions::class;
  protected $toolVersionsDataType = '';
  /**
   * Output only. Unique identifier of the `Release`.
   *
   * @var string
   */
  public $uid;

  /**
   * Output only. Indicates whether this is an abandoned release.
   *
   * @param bool $abandoned
   */
  public function setAbandoned($abandoned)
  {
    $this->abandoned = $abandoned;
  }
  /**
   * @return bool
   */
  public function getAbandoned()
  {
    return $this->abandoned;
  }
  /**
   * Optional. User annotations. These attributes can only be set and used by
   * the user, and not by Cloud Deploy. See
   * https://google.aip.dev/128#annotations for more details such as format and
   * size limitations.
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
   * Optional. List of artifacts to pass through to Skaffold command.
   *
   * @param BuildArtifact[] $buildArtifacts
   */
  public function setBuildArtifacts($buildArtifacts)
  {
    $this->buildArtifacts = $buildArtifacts;
  }
  /**
   * @return BuildArtifact[]
   */
  public function getBuildArtifacts()
  {
    return $this->buildArtifacts;
  }
  /**
   * Output only. Information around the state of the Release.
   *
   * @param ReleaseCondition $condition
   */
  public function setCondition(ReleaseCondition $condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return ReleaseCondition
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Output only. Time at which the `Release` was created.
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
   * Output only. Snapshot of the custom target types referenced by the targets
   * taken at release creation time.
   *
   * @param CustomTargetType[] $customTargetTypeSnapshots
   */
  public function setCustomTargetTypeSnapshots($customTargetTypeSnapshots)
  {
    $this->customTargetTypeSnapshots = $customTargetTypeSnapshots;
  }
  /**
   * @return CustomTargetType[]
   */
  public function getCustomTargetTypeSnapshots()
  {
    return $this->customTargetTypeSnapshots;
  }
  /**
   * Output only. Snapshot of the parent pipeline taken at release creation
   * time.
   *
   * @param DeliveryPipeline $deliveryPipelineSnapshot
   */
  public function setDeliveryPipelineSnapshot(DeliveryPipeline $deliveryPipelineSnapshot)
  {
    $this->deliveryPipelineSnapshot = $deliveryPipelineSnapshot;
  }
  /**
   * @return DeliveryPipeline
   */
  public function getDeliveryPipelineSnapshot()
  {
    return $this->deliveryPipelineSnapshot;
  }
  /**
   * Optional. The deploy parameters to use for all targets in this release.
   *
   * @param string[] $deployParameters
   */
  public function setDeployParameters($deployParameters)
  {
    $this->deployParameters = $deployParameters;
  }
  /**
   * @return string[]
   */
  public function getDeployParameters()
  {
    return $this->deployParameters;
  }
  /**
   * Optional. Description of the `Release`. Max length is 255 characters.
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
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
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
   * Labels are attributes that can be set and used by both the user and by
   * Cloud Deploy. Labels must meet the following constraints: * Keys and values
   * can contain only lowercase letters, numeric characters, underscores, and
   * dashes. * All characters must use UTF-8 encoding, and international
   * characters are allowed. * Keys must start with a lowercase letter or
   * international character. * Each resource is limited to a maximum of 64
   * labels. Both keys and values are additionally constrained to be <= 128
   * bytes.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Identifier. Name of the `Release`. Format is `projects/{project}/locations/
   * {location}/deliveryPipelines/{deliveryPipeline}/releases/{release}`. The
   * `release` component must match `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`
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
   * Output only. Time at which the render completed.
   *
   * @param string $renderEndTime
   */
  public function setRenderEndTime($renderEndTime)
  {
    $this->renderEndTime = $renderEndTime;
  }
  /**
   * @return string
   */
  public function getRenderEndTime()
  {
    return $this->renderEndTime;
  }
  /**
   * Output only. Time at which the render began.
   *
   * @param string $renderStartTime
   */
  public function setRenderStartTime($renderStartTime)
  {
    $this->renderStartTime = $renderStartTime;
  }
  /**
   * @return string
   */
  public function getRenderStartTime()
  {
    return $this->renderStartTime;
  }
  /**
   * Output only. Current state of the render operation.
   *
   * Accepted values: RENDER_STATE_UNSPECIFIED, SUCCEEDED, FAILED, IN_PROGRESS
   *
   * @param self::RENDER_STATE_* $renderState
   */
  public function setRenderState($renderState)
  {
    $this->renderState = $renderState;
  }
  /**
   * @return self::RENDER_STATE_*
   */
  public function getRenderState()
  {
    return $this->renderState;
  }
  /**
   * Optional. Filepath of the Skaffold config inside of the config URI.
   *
   * @param string $skaffoldConfigPath
   */
  public function setSkaffoldConfigPath($skaffoldConfigPath)
  {
    $this->skaffoldConfigPath = $skaffoldConfigPath;
  }
  /**
   * @return string
   */
  public function getSkaffoldConfigPath()
  {
    return $this->skaffoldConfigPath;
  }
  /**
   * Optional. Cloud Storage URI of tar.gz archive containing Skaffold
   * configuration.
   *
   * @param string $skaffoldConfigUri
   */
  public function setSkaffoldConfigUri($skaffoldConfigUri)
  {
    $this->skaffoldConfigUri = $skaffoldConfigUri;
  }
  /**
   * @return string
   */
  public function getSkaffoldConfigUri()
  {
    return $this->skaffoldConfigUri;
  }
  /**
   * Optional. The Skaffold version to use when operating on this release, such
   * as "1.20.0". Not all versions are valid; Cloud Deploy supports a specific
   * set of versions. If unset, the most recent supported Skaffold version will
   * be used.
   *
   * @param string $skaffoldVersion
   */
  public function setSkaffoldVersion($skaffoldVersion)
  {
    $this->skaffoldVersion = $skaffoldVersion;
  }
  /**
   * @return string
   */
  public function getSkaffoldVersion()
  {
    return $this->skaffoldVersion;
  }
  /**
   * Output only. Map from target ID to the target artifacts created during the
   * render operation.
   *
   * @param TargetArtifact[] $targetArtifacts
   */
  public function setTargetArtifacts($targetArtifacts)
  {
    $this->targetArtifacts = $targetArtifacts;
  }
  /**
   * @return TargetArtifact[]
   */
  public function getTargetArtifacts()
  {
    return $this->targetArtifacts;
  }
  /**
   * Output only. Map from target ID to details of the render operation for that
   * target.
   *
   * @param TargetRender[] $targetRenders
   */
  public function setTargetRenders($targetRenders)
  {
    $this->targetRenders = $targetRenders;
  }
  /**
   * @return TargetRender[]
   */
  public function getTargetRenders()
  {
    return $this->targetRenders;
  }
  /**
   * Output only. Snapshot of the targets taken at release creation time.
   *
   * @param Target[] $targetSnapshots
   */
  public function setTargetSnapshots($targetSnapshots)
  {
    $this->targetSnapshots = $targetSnapshots;
  }
  /**
   * @return Target[]
   */
  public function getTargetSnapshots()
  {
    return $this->targetSnapshots;
  }
  /**
   * Optional. The tool versions to use for this release and all subsequent
   * operations involving this release. If unset, then it will freeze the tool
   * versions at the time of release creation.
   *
   * @param ToolVersions $toolVersions
   */
  public function setToolVersions(ToolVersions $toolVersions)
  {
    $this->toolVersions = $toolVersions;
  }
  /**
   * @return ToolVersions
   */
  public function getToolVersions()
  {
    return $this->toolVersions;
  }
  /**
   * Output only. Unique identifier of the `Release`.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Release::class, 'Google_Service_CloudDeploy_Release');
