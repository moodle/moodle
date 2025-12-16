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

class FutureReservation extends \Google\Model
{
  /**
   * The reserved capacity is made up of densely deployed reservation blocks.
   */
  public const DEPLOYMENT_TYPE_DENSE = 'DENSE';
  public const DEPLOYMENT_TYPE_DEPLOYMENT_TYPE_UNSPECIFIED = 'DEPLOYMENT_TYPE_UNSPECIFIED';
  /**
   * Future Reservation is being drafted.
   */
  public const PLANNING_STATUS_DRAFT = 'DRAFT';
  public const PLANNING_STATUS_PLANNING_STATUS_UNSPECIFIED = 'PLANNING_STATUS_UNSPECIFIED';
  /**
   * Future Reservation has been submitted for evaluation by GCP.
   */
  public const PLANNING_STATUS_SUBMITTED = 'SUBMITTED';
  /**
   * The delivered reservations will delivered at specified start time and
   * terminated at specified end time along with terminating the VMs running on
   * it.
   */
  public const RESERVATION_MODE_CALENDAR = 'CALENDAR';
  /**
   * The delivered reservations do not terminate VMs at the end of reservations.
   * This is default mode.
   */
  public const RESERVATION_MODE_DEFAULT = 'DEFAULT';
  public const RESERVATION_MODE_RESERVATION_MODE_UNSPECIFIED = 'RESERVATION_MODE_UNSPECIFIED';
  /**
   * Maintenance on all reserved instances in the reservation is synchronized.
   */
  public const SCHEDULING_TYPE_GROUPED = 'GROUPED';
  /**
   * Unknown maintenance type.
   */
  public const SCHEDULING_TYPE_GROUP_MAINTENANCE_TYPE_UNSPECIFIED = 'GROUP_MAINTENANCE_TYPE_UNSPECIFIED';
  /**
   * Maintenance is not synchronized for this reservation. Instead, each
   * instance has its own maintenance window.
   */
  public const SCHEDULING_TYPE_INDEPENDENT = 'INDEPENDENT';
  protected $aggregateReservationType = AllocationAggregateReservation::class;
  protected $aggregateReservationDataType = '';
  /**
   * Future timestamp when the FR auto-created reservations will be deleted by
   * Compute Engine. Format of this field must be a valid
   * href="https://www.ietf.org/rfc/rfc3339.txt">RFC3339 value.
   *
   * @var string
   */
  public $autoCreatedReservationsDeleteTime;
  protected $autoCreatedReservationsDurationType = Duration::class;
  protected $autoCreatedReservationsDurationDataType = '';
  /**
   * Setting for enabling or disabling automatic deletion for auto-created
   * reservation. If set to true, auto-created reservations will be deleted at
   * Future Reservation's end time (default) or at user's defined timestamp if
   * any of the [auto_created_reservations_delete_time,
   * auto_created_reservations_duration] values is specified. For keeping auto-
   * created reservation indefinitely, this value should be set to false.
   *
   * @var bool
   */
  public $autoDeleteAutoCreatedReservations;
  protected $commitmentInfoType = FutureReservationCommitmentInfo::class;
  protected $commitmentInfoDataType = '';
  /**
   * Output only. [Output Only] The creation timestamp for this future
   * reservation inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * Type of the deployment requested as part of future reservation.
   *
   * @var string
   */
  public $deploymentType;
  /**
   * An optional description of this resource. Provide this property when you
   * create the future reservation.
   *
   * @var string
   */
  public $description;
  /**
   * Indicates if this group of VMs have emergent maintenance enabled.
   *
   * @var bool
   */
  public $enableEmergentMaintenance;
  /**
   * Output only. [Output Only] A unique identifier for this future reservation.
   * The server defines this identifier.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#futureReservation for future reservations.
   *
   * @var string
   */
  public $kind;
  /**
   * The name of the resource, provided by the client when initially creating
   * the resource. The resource name must be 1-63 characters long, and comply
   * withRFC1035. Specifically, the name must be 1-63 characters long and match
   * the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first
   * character must be a lowercase letter, and all following characters must be
   * a dash, lowercase letter, or digit, except the last character, which cannot
   * be a dash.
   *
   * @var string
   */
  public $name;
  /**
   * Name prefix for the reservations to be created at the time of delivery. The
   * name prefix must comply with RFC1035. Maximum allowed length for name
   * prefix is 20. Automatically created reservations name format will be
   * -date-####.
   *
   * @var string
   */
  public $namePrefix;
  /**
   * Planning state before being submitted for evaluation
   *
   * @var string
   */
  public $planningStatus;
  /**
   * The reservation mode which determines reservation-termination behavior and
   * expected pricing.
   *
   * @var string
   */
  public $reservationMode;
  /**
   * Name of reservations where the capacity is provisioned at the time of
   * delivery of  future reservations. If the reservation with the given name
   * does not exist already, it is created automatically at the time of Approval
   * with INACTIVE state till specified start-time. Either provide the
   * reservation_name or a name_prefix.
   *
   * @var string
   */
  public $reservationName;
  /**
   * Maintenance information for this reservation
   *
   * @var string
   */
  public $schedulingType;
  /**
   * Output only. [Output Only] Server-defined fully-qualified URL for this
   * resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. [Output Only] Server-defined URL for this resource with the
   * resource id.
   *
   * @var string
   */
  public $selfLinkWithId;
  protected $shareSettingsType = ShareSettings::class;
  protected $shareSettingsDataType = '';
  /**
   * Indicates whether the auto-created reservation can be consumed by VMs with
   * affinity for "any" reservation. If the field is set, then only VMs that
   * target the reservation by name can consume from the delivered reservation.
   *
   * @var bool
   */
  public $specificReservationRequired;
  protected $specificSkuPropertiesType = FutureReservationSpecificSKUProperties::class;
  protected $specificSkuPropertiesDataType = '';
  protected $statusType = FutureReservationStatus::class;
  protected $statusDataType = '';
  protected $timeWindowType = FutureReservationTimeWindow::class;
  protected $timeWindowDataType = '';
  /**
   * Output only. [Output Only] URL of the Zone where this future reservation
   * resides.
   *
   * @var string
   */
  public $zone;

  /**
   * Aggregate reservation details for the future reservation.
   *
   * @param AllocationAggregateReservation $aggregateReservation
   */
  public function setAggregateReservation(AllocationAggregateReservation $aggregateReservation)
  {
    $this->aggregateReservation = $aggregateReservation;
  }
  /**
   * @return AllocationAggregateReservation
   */
  public function getAggregateReservation()
  {
    return $this->aggregateReservation;
  }
  /**
   * Future timestamp when the FR auto-created reservations will be deleted by
   * Compute Engine. Format of this field must be a valid
   * href="https://www.ietf.org/rfc/rfc3339.txt">RFC3339 value.
   *
   * @param string $autoCreatedReservationsDeleteTime
   */
  public function setAutoCreatedReservationsDeleteTime($autoCreatedReservationsDeleteTime)
  {
    $this->autoCreatedReservationsDeleteTime = $autoCreatedReservationsDeleteTime;
  }
  /**
   * @return string
   */
  public function getAutoCreatedReservationsDeleteTime()
  {
    return $this->autoCreatedReservationsDeleteTime;
  }
  /**
   * Specifies the duration of auto-created reservations. It represents relative
   * time to future reservation start_time when auto-created reservations will
   * be automatically deleted by Compute Engine. Duration time unit is
   * represented as a count of seconds and fractions of seconds at nanosecond
   * resolution.
   *
   * @param Duration $autoCreatedReservationsDuration
   */
  public function setAutoCreatedReservationsDuration(Duration $autoCreatedReservationsDuration)
  {
    $this->autoCreatedReservationsDuration = $autoCreatedReservationsDuration;
  }
  /**
   * @return Duration
   */
  public function getAutoCreatedReservationsDuration()
  {
    return $this->autoCreatedReservationsDuration;
  }
  /**
   * Setting for enabling or disabling automatic deletion for auto-created
   * reservation. If set to true, auto-created reservations will be deleted at
   * Future Reservation's end time (default) or at user's defined timestamp if
   * any of the [auto_created_reservations_delete_time,
   * auto_created_reservations_duration] values is specified. For keeping auto-
   * created reservation indefinitely, this value should be set to false.
   *
   * @param bool $autoDeleteAutoCreatedReservations
   */
  public function setAutoDeleteAutoCreatedReservations($autoDeleteAutoCreatedReservations)
  {
    $this->autoDeleteAutoCreatedReservations = $autoDeleteAutoCreatedReservations;
  }
  /**
   * @return bool
   */
  public function getAutoDeleteAutoCreatedReservations()
  {
    return $this->autoDeleteAutoCreatedReservations;
  }
  /**
   * If not present, then FR will not deliver a new commitment or update an
   * existing commitment.
   *
   * @param FutureReservationCommitmentInfo $commitmentInfo
   */
  public function setCommitmentInfo(FutureReservationCommitmentInfo $commitmentInfo)
  {
    $this->commitmentInfo = $commitmentInfo;
  }
  /**
   * @return FutureReservationCommitmentInfo
   */
  public function getCommitmentInfo()
  {
    return $this->commitmentInfo;
  }
  /**
   * Output only. [Output Only] The creation timestamp for this future
   * reservation inRFC3339 text format.
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
   * Type of the deployment requested as part of future reservation.
   *
   * Accepted values: DENSE, DEPLOYMENT_TYPE_UNSPECIFIED
   *
   * @param self::DEPLOYMENT_TYPE_* $deploymentType
   */
  public function setDeploymentType($deploymentType)
  {
    $this->deploymentType = $deploymentType;
  }
  /**
   * @return self::DEPLOYMENT_TYPE_*
   */
  public function getDeploymentType()
  {
    return $this->deploymentType;
  }
  /**
   * An optional description of this resource. Provide this property when you
   * create the future reservation.
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
   * Indicates if this group of VMs have emergent maintenance enabled.
   *
   * @param bool $enableEmergentMaintenance
   */
  public function setEnableEmergentMaintenance($enableEmergentMaintenance)
  {
    $this->enableEmergentMaintenance = $enableEmergentMaintenance;
  }
  /**
   * @return bool
   */
  public function getEnableEmergentMaintenance()
  {
    return $this->enableEmergentMaintenance;
  }
  /**
   * Output only. [Output Only] A unique identifier for this future reservation.
   * The server defines this identifier.
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
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#futureReservation for future reservations.
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
   * The name of the resource, provided by the client when initially creating
   * the resource. The resource name must be 1-63 characters long, and comply
   * withRFC1035. Specifically, the name must be 1-63 characters long and match
   * the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first
   * character must be a lowercase letter, and all following characters must be
   * a dash, lowercase letter, or digit, except the last character, which cannot
   * be a dash.
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
   * Name prefix for the reservations to be created at the time of delivery. The
   * name prefix must comply with RFC1035. Maximum allowed length for name
   * prefix is 20. Automatically created reservations name format will be
   * -date-####.
   *
   * @param string $namePrefix
   */
  public function setNamePrefix($namePrefix)
  {
    $this->namePrefix = $namePrefix;
  }
  /**
   * @return string
   */
  public function getNamePrefix()
  {
    return $this->namePrefix;
  }
  /**
   * Planning state before being submitted for evaluation
   *
   * Accepted values: DRAFT, PLANNING_STATUS_UNSPECIFIED, SUBMITTED
   *
   * @param self::PLANNING_STATUS_* $planningStatus
   */
  public function setPlanningStatus($planningStatus)
  {
    $this->planningStatus = $planningStatus;
  }
  /**
   * @return self::PLANNING_STATUS_*
   */
  public function getPlanningStatus()
  {
    return $this->planningStatus;
  }
  /**
   * The reservation mode which determines reservation-termination behavior and
   * expected pricing.
   *
   * Accepted values: CALENDAR, DEFAULT, RESERVATION_MODE_UNSPECIFIED
   *
   * @param self::RESERVATION_MODE_* $reservationMode
   */
  public function setReservationMode($reservationMode)
  {
    $this->reservationMode = $reservationMode;
  }
  /**
   * @return self::RESERVATION_MODE_*
   */
  public function getReservationMode()
  {
    return $this->reservationMode;
  }
  /**
   * Name of reservations where the capacity is provisioned at the time of
   * delivery of  future reservations. If the reservation with the given name
   * does not exist already, it is created automatically at the time of Approval
   * with INACTIVE state till specified start-time. Either provide the
   * reservation_name or a name_prefix.
   *
   * @param string $reservationName
   */
  public function setReservationName($reservationName)
  {
    $this->reservationName = $reservationName;
  }
  /**
   * @return string
   */
  public function getReservationName()
  {
    return $this->reservationName;
  }
  /**
   * Maintenance information for this reservation
   *
   * Accepted values: GROUPED, GROUP_MAINTENANCE_TYPE_UNSPECIFIED, INDEPENDENT
   *
   * @param self::SCHEDULING_TYPE_* $schedulingType
   */
  public function setSchedulingType($schedulingType)
  {
    $this->schedulingType = $schedulingType;
  }
  /**
   * @return self::SCHEDULING_TYPE_*
   */
  public function getSchedulingType()
  {
    return $this->schedulingType;
  }
  /**
   * Output only. [Output Only] Server-defined fully-qualified URL for this
   * resource.
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
   * Output only. [Output Only] Server-defined URL for this resource with the
   * resource id.
   *
   * @param string $selfLinkWithId
   */
  public function setSelfLinkWithId($selfLinkWithId)
  {
    $this->selfLinkWithId = $selfLinkWithId;
  }
  /**
   * @return string
   */
  public function getSelfLinkWithId()
  {
    return $this->selfLinkWithId;
  }
  /**
   * List of Projects/Folders to share with.
   *
   * @param ShareSettings $shareSettings
   */
  public function setShareSettings(ShareSettings $shareSettings)
  {
    $this->shareSettings = $shareSettings;
  }
  /**
   * @return ShareSettings
   */
  public function getShareSettings()
  {
    return $this->shareSettings;
  }
  /**
   * Indicates whether the auto-created reservation can be consumed by VMs with
   * affinity for "any" reservation. If the field is set, then only VMs that
   * target the reservation by name can consume from the delivered reservation.
   *
   * @param bool $specificReservationRequired
   */
  public function setSpecificReservationRequired($specificReservationRequired)
  {
    $this->specificReservationRequired = $specificReservationRequired;
  }
  /**
   * @return bool
   */
  public function getSpecificReservationRequired()
  {
    return $this->specificReservationRequired;
  }
  /**
   * Future Reservation configuration to indicate instance properties and total
   * count.
   *
   * @param FutureReservationSpecificSKUProperties $specificSkuProperties
   */
  public function setSpecificSkuProperties(FutureReservationSpecificSKUProperties $specificSkuProperties)
  {
    $this->specificSkuProperties = $specificSkuProperties;
  }
  /**
   * @return FutureReservationSpecificSKUProperties
   */
  public function getSpecificSkuProperties()
  {
    return $this->specificSkuProperties;
  }
  /**
   * Output only. [Output only] Status of the Future Reservation
   *
   * @param FutureReservationStatus $status
   */
  public function setStatus(FutureReservationStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return FutureReservationStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Time window for this Future Reservation.
   *
   * @param FutureReservationTimeWindow $timeWindow
   */
  public function setTimeWindow(FutureReservationTimeWindow $timeWindow)
  {
    $this->timeWindow = $timeWindow;
  }
  /**
   * @return FutureReservationTimeWindow
   */
  public function getTimeWindow()
  {
    return $this->timeWindow;
  }
  /**
   * Output only. [Output Only] URL of the Zone where this future reservation
   * resides.
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
class_alias(FutureReservation::class, 'Google_Service_Compute_FutureReservation');
