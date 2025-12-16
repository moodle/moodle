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

class Reservation extends \Google\Collection
{
  /**
   * The reserved capacity is made up of densely deployed reservation blocks.
   */
  public const DEPLOYMENT_TYPE_DENSE = 'DENSE';
  public const DEPLOYMENT_TYPE_DEPLOYMENT_TYPE_UNSPECIFIED = 'DEPLOYMENT_TYPE_UNSPECIFIED';
  /**
   * CAPACITY_OPTIMIZED capacity leverages redundancies (e.g. power, cooling) at
   * the data center during normal operating conditions. In the event of
   * infrastructure failures at data center (e.g. power and/or cooling
   * failures), this workload may be disrupted. As a consequence, it has a
   * weaker availability SLO than STANDARD.
   */
  public const PROTECTION_TIER_CAPACITY_OPTIMIZED = 'CAPACITY_OPTIMIZED';
  /**
   * Unspecified protection tier.
   */
  public const PROTECTION_TIER_PROTECTION_TIER_UNSPECIFIED = 'PROTECTION_TIER_UNSPECIFIED';
  /**
   * STANDARD protection for workload that should be protected by redundancies
   * (e.g. power, cooling) at the data center level. In the event of
   * infrastructure failures at data center (e.g. power and/or cooling
   * failures), this workload is expected to continue as normal using the
   * redundancies.
   */
  public const PROTECTION_TIER_STANDARD = 'STANDARD';
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
  /**
   * Reservation resources are being allocated.
   */
  public const STATUS_CREATING = 'CREATING';
  /**
   * Reservation deletion is in progress.
   */
  public const STATUS_DELETING = 'DELETING';
  public const STATUS_INVALID = 'INVALID';
  /**
   * Reservation resources have been allocated, and the reservation is ready for
   * use.
   */
  public const STATUS_READY = 'READY';
  /**
   * Reservation update is in progress.
   */
  public const STATUS_UPDATING = 'UPDATING';
  protected $collection_key = 'linkedCommitments';
  protected $advancedDeploymentControlType = ReservationAdvancedDeploymentControl::class;
  protected $advancedDeploymentControlDataType = '';
  protected $aggregateReservationType = AllocationAggregateReservation::class;
  protected $aggregateReservationDataType = '';
  /**
   * Output only. [Output Only] Full or partial URL to a parent commitment. This
   * field displays for reservations that are tied to a commitment.
   *
   * @var string
   */
  public $commitment;
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  protected $deleteAfterDurationType = Duration::class;
  protected $deleteAfterDurationDataType = '';
  /**
   * Absolute time in future when the reservation will be  auto-deleted by
   * Compute Engine. Timestamp is represented inRFC3339 text format.
   *
   * @var string
   */
  public $deleteAtTime;
  /**
   * Specifies the deployment strategy for this reservation.
   *
   * @var string
   */
  public $deploymentType;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Indicates whether Compute Engine allows unplanned maintenance for your VMs;
   * for example, to fix hardware errors.
   *
   * @var bool
   */
  public $enableEmergentMaintenance;
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of the resource. Alwayscompute#reservations
   * for reservations.
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. [Output Only] Full or partial URL to parent commitments. This
   * field displays for reservations that are tied to multiple commitments.
   *
   * @var string[]
   */
  public $linkedCommitments;
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
   * Protection tier for the workload which specifies the workload expectations
   * in the event of infrastructure failures at data center (e.g. power and/or
   * cooling failures).
   *
   * @var string
   */
  public $protectionTier;
  protected $reservationSharingPolicyType = AllocationReservationSharingPolicy::class;
  protected $reservationSharingPolicyDataType = '';
  /**
   * Resource policies to be added to this reservation. The key is defined by
   * user, and the value is resource policy url. This is to define placement
   * policy with reservation.
   *
   * @var string[]
   */
  public $resourcePolicies;
  protected $resourceStatusType = AllocationResourceStatus::class;
  protected $resourceStatusDataType = '';
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * The type of maintenance for the reservation.
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
  protected $shareSettingsType = ShareSettings::class;
  protected $shareSettingsDataType = '';
  protected $specificReservationType = AllocationSpecificSKUReservation::class;
  protected $specificReservationDataType = '';
  /**
   * Indicates whether the reservation can be consumed by VMs with affinity for
   * "any" reservation. If the field is set, then only VMs that target the
   * reservation by name can consume from this reservation.
   *
   * @var bool
   */
  public $specificReservationRequired;
  /**
   * Output only. [Output Only] The status of the reservation.              -
   * CREATING: Reservation resources are being        allocated.      - READY:
   * Reservation resources have been allocated,        and the reservation is
   * ready for use.      - DELETING: Reservation deletion is in progress.      -
   * UPDATING: Reservation update is in progress.
   *
   * @var string
   */
  public $status;
  /**
   * Zone in which the reservation resides. A zone must be provided if the
   * reservation is created within a commitment.
   *
   * @var string
   */
  public $zone;

  /**
   * Advanced control for cluster management, applicable only to DENSE
   * deployment type reservations.
   *
   * @param ReservationAdvancedDeploymentControl $advancedDeploymentControl
   */
  public function setAdvancedDeploymentControl(ReservationAdvancedDeploymentControl $advancedDeploymentControl)
  {
    $this->advancedDeploymentControl = $advancedDeploymentControl;
  }
  /**
   * @return ReservationAdvancedDeploymentControl
   */
  public function getAdvancedDeploymentControl()
  {
    return $this->advancedDeploymentControl;
  }
  /**
   * Reservation for aggregated resources, providing shape flexibility.
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
   * Output only. [Output Only] Full or partial URL to a parent commitment. This
   * field displays for reservations that are tied to a commitment.
   *
   * @param string $commitment
   */
  public function setCommitment($commitment)
  {
    $this->commitment = $commitment;
  }
  /**
   * @return string
   */
  public function getCommitment()
  {
    return $this->commitment;
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
   * Duration time relative to reservation creation when Compute Engine will
   * automatically delete this resource.
   *
   * @param Duration $deleteAfterDuration
   */
  public function setDeleteAfterDuration(Duration $deleteAfterDuration)
  {
    $this->deleteAfterDuration = $deleteAfterDuration;
  }
  /**
   * @return Duration
   */
  public function getDeleteAfterDuration()
  {
    return $this->deleteAfterDuration;
  }
  /**
   * Absolute time in future when the reservation will be  auto-deleted by
   * Compute Engine. Timestamp is represented inRFC3339 text format.
   *
   * @param string $deleteAtTime
   */
  public function setDeleteAtTime($deleteAtTime)
  {
    $this->deleteAtTime = $deleteAtTime;
  }
  /**
   * @return string
   */
  public function getDeleteAtTime()
  {
    return $this->deleteAtTime;
  }
  /**
   * Specifies the deployment strategy for this reservation.
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
   * Indicates whether Compute Engine allows unplanned maintenance for your VMs;
   * for example, to fix hardware errors.
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
   * Output only. [Output Only] Type of the resource. Alwayscompute#reservations
   * for reservations.
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
   * Output only. [Output Only] Full or partial URL to parent commitments. This
   * field displays for reservations that are tied to multiple commitments.
   *
   * @param string[] $linkedCommitments
   */
  public function setLinkedCommitments($linkedCommitments)
  {
    $this->linkedCommitments = $linkedCommitments;
  }
  /**
   * @return string[]
   */
  public function getLinkedCommitments()
  {
    return $this->linkedCommitments;
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
   * Protection tier for the workload which specifies the workload expectations
   * in the event of infrastructure failures at data center (e.g. power and/or
   * cooling failures).
   *
   * Accepted values: CAPACITY_OPTIMIZED, PROTECTION_TIER_UNSPECIFIED, STANDARD
   *
   * @param self::PROTECTION_TIER_* $protectionTier
   */
  public function setProtectionTier($protectionTier)
  {
    $this->protectionTier = $protectionTier;
  }
  /**
   * @return self::PROTECTION_TIER_*
   */
  public function getProtectionTier()
  {
    return $this->protectionTier;
  }
  /**
   * Specify the reservation sharing policy. If unspecified, the reservation
   * will not be shared with Google Cloud managed services.
   *
   * @param AllocationReservationSharingPolicy $reservationSharingPolicy
   */
  public function setReservationSharingPolicy(AllocationReservationSharingPolicy $reservationSharingPolicy)
  {
    $this->reservationSharingPolicy = $reservationSharingPolicy;
  }
  /**
   * @return AllocationReservationSharingPolicy
   */
  public function getReservationSharingPolicy()
  {
    return $this->reservationSharingPolicy;
  }
  /**
   * Resource policies to be added to this reservation. The key is defined by
   * user, and the value is resource policy url. This is to define placement
   * policy with reservation.
   *
   * @param string[] $resourcePolicies
   */
  public function setResourcePolicies($resourcePolicies)
  {
    $this->resourcePolicies = $resourcePolicies;
  }
  /**
   * @return string[]
   */
  public function getResourcePolicies()
  {
    return $this->resourcePolicies;
  }
  /**
   * Output only. [Output Only] Status information for Reservation resource.
   *
   * @param AllocationResourceStatus $resourceStatus
   */
  public function setResourceStatus(AllocationResourceStatus $resourceStatus)
  {
    $this->resourceStatus = $resourceStatus;
  }
  /**
   * @return AllocationResourceStatus
   */
  public function getResourceStatus()
  {
    return $this->resourceStatus;
  }
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * The type of maintenance for the reservation.
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
   * Specify share-settings to create a shared reservation. This property is
   * optional. For more information about the syntax and options for this field
   * and its subfields, see the guide for creating a shared reservation.
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
   * Reservation for instances with specific machine shapes.
   *
   * @param AllocationSpecificSKUReservation $specificReservation
   */
  public function setSpecificReservation(AllocationSpecificSKUReservation $specificReservation)
  {
    $this->specificReservation = $specificReservation;
  }
  /**
   * @return AllocationSpecificSKUReservation
   */
  public function getSpecificReservation()
  {
    return $this->specificReservation;
  }
  /**
   * Indicates whether the reservation can be consumed by VMs with affinity for
   * "any" reservation. If the field is set, then only VMs that target the
   * reservation by name can consume from this reservation.
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
   * Output only. [Output Only] The status of the reservation.              -
   * CREATING: Reservation resources are being        allocated.      - READY:
   * Reservation resources have been allocated,        and the reservation is
   * ready for use.      - DELETING: Reservation deletion is in progress.      -
   * UPDATING: Reservation update is in progress.
   *
   * Accepted values: CREATING, DELETING, INVALID, READY, UPDATING
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
   * Zone in which the reservation resides. A zone must be provided if the
   * reservation is created within a commitment.
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
class_alias(Reservation::class, 'Google_Service_Compute_Reservation');
