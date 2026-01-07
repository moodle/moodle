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

class Autoscaler extends \Google\Collection
{
  /**
   * Configuration is acknowledged to be effective
   */
  public const STATUS_ACTIVE = 'ACTIVE';
  /**
   * Configuration is being deleted
   */
  public const STATUS_DELETING = 'DELETING';
  /**
   * Configuration has errors. Actionable for users.
   */
  public const STATUS_ERROR = 'ERROR';
  /**
   * Autoscaler backend hasn't read new/updated configuration
   */
  public const STATUS_PENDING = 'PENDING';
  protected $collection_key = 'statusDetails';
  protected $autoscalingPolicyType = AutoscalingPolicy::class;
  protected $autoscalingPolicyDataType = '';
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
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
   * Output only. [Output Only] Type of the resource. Always compute#autoscaler
   * for autoscalers.
   *
   * @var string
   */
  public $kind;
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. [Output Only] Target recommended MIG size (number of
   * instances) computed by autoscaler. Autoscaler calculates the recommended
   * MIG size even when the autoscaling policy mode is different from ON. This
   * field is empty when autoscaler is not connected to an existing managed
   * instance group or autoscaler did not generate its prediction.
   *
   * @var int
   */
  public $recommendedSize;
  /**
   * Output only. [Output Only] URL of theregion where the instance group
   * resides (for autoscalers living in regional scope).
   *
   * @var string
   */
  public $region;
  protected $scalingScheduleStatusType = ScalingScheduleStatus::class;
  protected $scalingScheduleStatusDataType = 'map';
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * [Output Only] The status of the autoscaler configuration. Current set of
   * possible values:        - PENDING:      Autoscaler backend hasn't read
   * new/updated configuration.    - DELETING:      Configuration is being
   * deleted.    - ACTIVE:      Configuration is acknowledged to be effective.
   * Some warnings might      be present in the statusDetails field.    - ERROR:
   * Configuration has errors. Actionable for users. Details are present in
   * the statusDetails field.
   *
   * New values might be added in the future.
   *
   * @var string
   */
  public $status;
  protected $statusDetailsType = AutoscalerStatusDetails::class;
  protected $statusDetailsDataType = 'array';
  /**
   * URL of the managed instance group that this autoscaler will scale. This
   * field is required when creating an autoscaler.
   *
   * @var string
   */
  public $target;
  /**
   * Output only. [Output Only] URL of thezone where the instance group resides
   * (for autoscalers living in zonal scope).
   *
   * @var string
   */
  public $zone;

  /**
   * The configuration parameters for the autoscaling algorithm. You can define
   * one or more signals for an autoscaler:
   * cpuUtilization,customMetricUtilizations, andloadBalancingUtilization.
   *
   * If none of these are specified, the default will be to autoscale based
   * oncpuUtilization to 0.6 or 60%.
   *
   * @param AutoscalingPolicy $autoscalingPolicy
   */
  public function setAutoscalingPolicy(AutoscalingPolicy $autoscalingPolicy)
  {
    $this->autoscalingPolicy = $autoscalingPolicy;
  }
  /**
   * @return AutoscalingPolicy
   */
  public function getAutoscalingPolicy()
  {
    return $this->autoscalingPolicy;
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
   * An optional description of this resource. Provide this property when you
   * create the resource.
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
   * Output only. [Output Only] Type of the resource. Always compute#autoscaler
   * for autoscalers.
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
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
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
   * Output only. [Output Only] Target recommended MIG size (number of
   * instances) computed by autoscaler. Autoscaler calculates the recommended
   * MIG size even when the autoscaling policy mode is different from ON. This
   * field is empty when autoscaler is not connected to an existing managed
   * instance group or autoscaler did not generate its prediction.
   *
   * @param int $recommendedSize
   */
  public function setRecommendedSize($recommendedSize)
  {
    $this->recommendedSize = $recommendedSize;
  }
  /**
   * @return int
   */
  public function getRecommendedSize()
  {
    return $this->recommendedSize;
  }
  /**
   * Output only. [Output Only] URL of theregion where the instance group
   * resides (for autoscalers living in regional scope).
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Output only. [Output Only] Status information of existing scaling
   * schedules.
   *
   * @param ScalingScheduleStatus[] $scalingScheduleStatus
   */
  public function setScalingScheduleStatus($scalingScheduleStatus)
  {
    $this->scalingScheduleStatus = $scalingScheduleStatus;
  }
  /**
   * @return ScalingScheduleStatus[]
   */
  public function getScalingScheduleStatus()
  {
    return $this->scalingScheduleStatus;
  }
  /**
   * [Output Only] Server-defined URL for the resource.
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
   * [Output Only] The status of the autoscaler configuration. Current set of
   * possible values:        - PENDING:      Autoscaler backend hasn't read
   * new/updated configuration.    - DELETING:      Configuration is being
   * deleted.    - ACTIVE:      Configuration is acknowledged to be effective.
   * Some warnings might      be present in the statusDetails field.    - ERROR:
   * Configuration has errors. Actionable for users. Details are present in
   * the statusDetails field.
   *
   * New values might be added in the future.
   *
   * Accepted values: ACTIVE, DELETING, ERROR, PENDING
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * [Output Only] Human-readable details about the current state of the
   * autoscaler. Read the documentation forCommonly returned status messages for
   * examples of status messages you might encounter.
   *
   * @param AutoscalerStatusDetails[] $statusDetails
   */
  public function setStatusDetails($statusDetails)
  {
    $this->statusDetails = $statusDetails;
  }
  /**
   * @return AutoscalerStatusDetails[]
   */
  public function getStatusDetails()
  {
    return $this->statusDetails;
  }
  /**
   * URL of the managed instance group that this autoscaler will scale. This
   * field is required when creating an autoscaler.
   *
   * @param string $target
   */
  public function setTarget($target)
  {
    $this->target = $target;
  }
  /**
   * @return string
   */
  public function getTarget()
  {
    return $this->target;
  }
  /**
   * Output only. [Output Only] URL of thezone where the instance group resides
   * (for autoscalers living in zonal scope).
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Autoscaler::class, 'Google_Service_Compute_Autoscaler');
