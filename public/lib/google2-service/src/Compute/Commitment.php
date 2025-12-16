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

class Commitment extends \Google\Collection
{
  public const CATEGORY_CATEGORY_UNSPECIFIED = 'CATEGORY_UNSPECIFIED';
  public const CATEGORY_LICENSE = 'LICENSE';
  public const CATEGORY_MACHINE = 'MACHINE';
  public const PLAN_INVALID = 'INVALID';
  public const PLAN_THIRTY_SIX_MONTH = 'THIRTY_SIX_MONTH';
  public const PLAN_TWELVE_MONTH = 'TWELVE_MONTH';
  public const STATUS_ACTIVE = 'ACTIVE';
  /**
   * Deprecate CANCELED status. Will use separate status to differentiate cancel
   * by mergeCud or manual cancellation.
   */
  public const STATUS_CANCELLED = 'CANCELLED';
  public const STATUS_CREATING = 'CREATING';
  public const STATUS_EXPIRED = 'EXPIRED';
  public const STATUS_NOT_YET_ACTIVE = 'NOT_YET_ACTIVE';
  public const TYPE_ACCELERATOR_OPTIMIZED = 'ACCELERATOR_OPTIMIZED';
  public const TYPE_ACCELERATOR_OPTIMIZED_A3 = 'ACCELERATOR_OPTIMIZED_A3';
  public const TYPE_ACCELERATOR_OPTIMIZED_A3_MEGA = 'ACCELERATOR_OPTIMIZED_A3_MEGA';
  public const TYPE_ACCELERATOR_OPTIMIZED_A3_ULTRA = 'ACCELERATOR_OPTIMIZED_A3_ULTRA';
  public const TYPE_ACCELERATOR_OPTIMIZED_A4 = 'ACCELERATOR_OPTIMIZED_A4';
  public const TYPE_COMPUTE_OPTIMIZED = 'COMPUTE_OPTIMIZED';
  public const TYPE_COMPUTE_OPTIMIZED_C2D = 'COMPUTE_OPTIMIZED_C2D';
  public const TYPE_COMPUTE_OPTIMIZED_C3 = 'COMPUTE_OPTIMIZED_C3';
  public const TYPE_COMPUTE_OPTIMIZED_C3D = 'COMPUTE_OPTIMIZED_C3D';
  public const TYPE_COMPUTE_OPTIMIZED_H3 = 'COMPUTE_OPTIMIZED_H3';
  public const TYPE_COMPUTE_OPTIMIZED_H4D = 'COMPUTE_OPTIMIZED_H4D';
  public const TYPE_GENERAL_PURPOSE = 'GENERAL_PURPOSE';
  public const TYPE_GENERAL_PURPOSE_C4 = 'GENERAL_PURPOSE_C4';
  public const TYPE_GENERAL_PURPOSE_C4A = 'GENERAL_PURPOSE_C4A';
  public const TYPE_GENERAL_PURPOSE_C4D = 'GENERAL_PURPOSE_C4D';
  public const TYPE_GENERAL_PURPOSE_E2 = 'GENERAL_PURPOSE_E2';
  public const TYPE_GENERAL_PURPOSE_N2 = 'GENERAL_PURPOSE_N2';
  public const TYPE_GENERAL_PURPOSE_N2D = 'GENERAL_PURPOSE_N2D';
  public const TYPE_GENERAL_PURPOSE_N4 = 'GENERAL_PURPOSE_N4';
  public const TYPE_GENERAL_PURPOSE_N4D = 'GENERAL_PURPOSE_N4D';
  public const TYPE_GENERAL_PURPOSE_T2D = 'GENERAL_PURPOSE_T2D';
  public const TYPE_GRAPHICS_OPTIMIZED = 'GRAPHICS_OPTIMIZED';
  public const TYPE_GRAPHICS_OPTIMIZED_G4 = 'GRAPHICS_OPTIMIZED_G4';
  public const TYPE_MEMORY_OPTIMIZED = 'MEMORY_OPTIMIZED';
  public const TYPE_MEMORY_OPTIMIZED_M3 = 'MEMORY_OPTIMIZED_M3';
  public const TYPE_MEMORY_OPTIMIZED_M4 = 'MEMORY_OPTIMIZED_M4';
  public const TYPE_MEMORY_OPTIMIZED_M4_6TB = 'MEMORY_OPTIMIZED_M4_6TB';
  public const TYPE_MEMORY_OPTIMIZED_X4_16TB = 'MEMORY_OPTIMIZED_X4_16TB';
  public const TYPE_MEMORY_OPTIMIZED_X4_24TB = 'MEMORY_OPTIMIZED_X4_24TB';
  public const TYPE_MEMORY_OPTIMIZED_X4_32TB = 'MEMORY_OPTIMIZED_X4_32TB';
  public const TYPE_STORAGE_OPTIMIZED_Z3 = 'STORAGE_OPTIMIZED_Z3';
  /**
   * Note for internal users: When adding a new enum Type for v1, make sure to
   * also add it in the comment for the `optional Type type` definition. This
   * ensures that the public documentation displays the new enum Type.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  protected $collection_key = 'resources';
  /**
   * Specifies whether to automatically renew the commitment at the end of its
   * current term. The default value is false. If you set the field to true,
   * each time your commitment reaches the end of its term, Compute Engine
   * automatically renews it for another term. You can update this field anytime
   * before the commitment expires. For example, if the commitment is set to
   * expire at 12 AM UTC-8 on January 3, 2027, you can update this field until
   * 11:59 PM UTC-8 on January 2, 2027.
   *
   * @var bool
   */
  public $autoRenew;
  /**
   * The category of the commitment; specifies whether the commitment is for
   * hardware or software resources. Category MACHINE specifies that you are
   * committing to hardware machine resources such asVCPU or MEMORY, listed in
   * resources. Category LICENSE specifies that you are committing to software
   * licenses, listed in licenseResources. Note that if you specify MACHINE
   * commitments, then you must also specify a type to indicate the machine
   * series of the hardware resource that you are committing to.
   *
   * @var string
   */
  public $category;
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * [Input Only] Optional, specifies the requested commitment end time
   * inRFC3339 text format. Use this option when the desired commitment's end
   * date is later than the start date + term duration.
   *
   * @var string
   */
  public $customEndTimestamp;
  /**
   * An optional description of the commitment. You can provide this property
   * when you create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. [Output Only] Commitment end time inRFC3339 text format.
   *
   * @var string
   */
  public $endTimestamp;
  /**
   * @var string[]
   */
  public $existingReservations;
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of the resource. Always compute#commitment
   * for commitments.
   *
   * @var string
   */
  public $kind;
  protected $licenseResourceType = LicenseResourceCommitment::class;
  protected $licenseResourceDataType = '';
  /**
   * The list of source commitments that you are merging to create the new
   * merged commitment. For more information, see Merging commitments.
   *
   * @var string[]
   */
  public $mergeSourceCommitments;
  /**
   * Name of the commitment. You must specify a name when you purchase the
   * commitment. The name must be 1-63 characters long, and comply withRFC1035.
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
   * The minimum time duration that you commit to purchasing resources. The plan
   * that you choose determines the preset term length of the commitment (which
   * is 1 year or 3 years) and affects the discount rate that you receive for
   * your resources. Committing to a longer time duration typically gives you a
   * higher discount rate. The supported values for this field are TWELVE_MONTH
   * (1 year), andTHIRTY_SIX_MONTH (3 years).
   *
   * @var string
   */
  public $plan;
  /**
   * Output only. [Output Only] URL of the region where the commitment and
   * committed resources are located.
   *
   * @var string
   */
  public $region;
  protected $reservationsType = Reservation::class;
  protected $reservationsDataType = 'array';
  protected $resourceStatusType = CommitmentResourceStatus::class;
  protected $resourceStatusDataType = '';
  protected $resourcesType = ResourceCommitment::class;
  protected $resourcesDataType = 'array';
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * The source commitment from which you are transferring resources to create
   * the new split commitment. For more information, see  Split commitments.
   *
   * @var string
   */
  public $splitSourceCommitment;
  /**
   * Output only. [Output Only] Commitment start time inRFC3339 text format.
   *
   * @var string
   */
  public $startTimestamp;
  /**
   * Output only. [Output Only] Status of the commitment with regards to
   * eventual expiration (each commitment has an end date defined). Status can
   * be one of the following values: NOT_YET_ACTIVE, ACTIVE, orEXPIRED.
   *
   * @var string
   */
  public $status;
  /**
   * Output only. [Output Only] An optional, human-readable explanation of the
   * status.
   *
   * @var string
   */
  public $statusMessage;
  /**
   * The type of commitment; specifies the machine series for which you want to
   * commit to purchasing resources. The choice of machine series affects the
   * discount rate and the eligible resource types.
   *
   *  The type must be one of the following:ACCELERATOR_OPTIMIZED,
   * ACCELERATOR_OPTIMIZED_A3,ACCELERATOR_OPTIMIZED_A3_MEGA,COMPUTE_OPTIMIZED,
   * COMPUTE_OPTIMIZED_C2D,  COMPUTE_OPTIMIZED_C3,
   * COMPUTE_OPTIMIZED_C3D,COMPUTE_OPTIMIZED_H3,
   * GENERAL_PURPOSE,GENERAL_PURPOSE_C4, GENERAL_PURPOSE_E2,GENERAL_PURPOSE_N2,
   * GENERAL_PURPOSE_N2D,GENERAL_PURPOSE_N4,
   * GENERAL_PURPOSE_T2D,GRAPHICS_OPTIMIZED,
   * GRAPHICS_OPTIMIZED_G4,MEMORY_OPTIMIZED,
   * MEMORY_OPTIMIZED_M3,MEMORY_OPTIMIZED_X4, STORAGE_OPTIMIZED_Z3. For example,
   * type MEMORY_OPTIMIZED specifies a commitment that applies only to eligible
   * resources of memory optimized M1 and M2 machine series. Type
   * GENERAL_PURPOSE specifies a commitment that applies only to eligible
   * resources of general purpose N1 machine series.
   *
   * @var string
   */
  public $type;

  /**
   * Specifies whether to automatically renew the commitment at the end of its
   * current term. The default value is false. If you set the field to true,
   * each time your commitment reaches the end of its term, Compute Engine
   * automatically renews it for another term. You can update this field anytime
   * before the commitment expires. For example, if the commitment is set to
   * expire at 12 AM UTC-8 on January 3, 2027, you can update this field until
   * 11:59 PM UTC-8 on January 2, 2027.
   *
   * @param bool $autoRenew
   */
  public function setAutoRenew($autoRenew)
  {
    $this->autoRenew = $autoRenew;
  }
  /**
   * @return bool
   */
  public function getAutoRenew()
  {
    return $this->autoRenew;
  }
  /**
   * The category of the commitment; specifies whether the commitment is for
   * hardware or software resources. Category MACHINE specifies that you are
   * committing to hardware machine resources such asVCPU or MEMORY, listed in
   * resources. Category LICENSE specifies that you are committing to software
   * licenses, listed in licenseResources. Note that if you specify MACHINE
   * commitments, then you must also specify a type to indicate the machine
   * series of the hardware resource that you are committing to.
   *
   * Accepted values: CATEGORY_UNSPECIFIED, LICENSE, MACHINE
   *
   * @param self::CATEGORY_* $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return self::CATEGORY_*
   */
  public function getCategory()
  {
    return $this->category;
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
   * [Input Only] Optional, specifies the requested commitment end time
   * inRFC3339 text format. Use this option when the desired commitment's end
   * date is later than the start date + term duration.
   *
   * @param string $customEndTimestamp
   */
  public function setCustomEndTimestamp($customEndTimestamp)
  {
    $this->customEndTimestamp = $customEndTimestamp;
  }
  /**
   * @return string
   */
  public function getCustomEndTimestamp()
  {
    return $this->customEndTimestamp;
  }
  /**
   * An optional description of the commitment. You can provide this property
   * when you create the resource.
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
   * Output only. [Output Only] Commitment end time inRFC3339 text format.
   *
   * @param string $endTimestamp
   */
  public function setEndTimestamp($endTimestamp)
  {
    $this->endTimestamp = $endTimestamp;
  }
  /**
   * @return string
   */
  public function getEndTimestamp()
  {
    return $this->endTimestamp;
  }
  /**
   * @param string[] $existingReservations
   */
  public function setExistingReservations($existingReservations)
  {
    $this->existingReservations = $existingReservations;
  }
  /**
   * @return string[]
   */
  public function getExistingReservations()
  {
    return $this->existingReservations;
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
   * Output only. [Output Only] Type of the resource. Always compute#commitment
   * for commitments.
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
   * The license specification required as part of a license commitment.
   *
   * @param LicenseResourceCommitment $licenseResource
   */
  public function setLicenseResource(LicenseResourceCommitment $licenseResource)
  {
    $this->licenseResource = $licenseResource;
  }
  /**
   * @return LicenseResourceCommitment
   */
  public function getLicenseResource()
  {
    return $this->licenseResource;
  }
  /**
   * The list of source commitments that you are merging to create the new
   * merged commitment. For more information, see Merging commitments.
   *
   * @param string[] $mergeSourceCommitments
   */
  public function setMergeSourceCommitments($mergeSourceCommitments)
  {
    $this->mergeSourceCommitments = $mergeSourceCommitments;
  }
  /**
   * @return string[]
   */
  public function getMergeSourceCommitments()
  {
    return $this->mergeSourceCommitments;
  }
  /**
   * Name of the commitment. You must specify a name when you purchase the
   * commitment. The name must be 1-63 characters long, and comply withRFC1035.
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
   * The minimum time duration that you commit to purchasing resources. The plan
   * that you choose determines the preset term length of the commitment (which
   * is 1 year or 3 years) and affects the discount rate that you receive for
   * your resources. Committing to a longer time duration typically gives you a
   * higher discount rate. The supported values for this field are TWELVE_MONTH
   * (1 year), andTHIRTY_SIX_MONTH (3 years).
   *
   * Accepted values: INVALID, THIRTY_SIX_MONTH, TWELVE_MONTH
   *
   * @param self::PLAN_* $plan
   */
  public function setPlan($plan)
  {
    $this->plan = $plan;
  }
  /**
   * @return self::PLAN_*
   */
  public function getPlan()
  {
    return $this->plan;
  }
  /**
   * Output only. [Output Only] URL of the region where the commitment and
   * committed resources are located.
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
   * The list of new reservations that you want to create and attach to this
   * commitment.
   *
   * You must attach reservations to your commitment if your commitment
   * specifies any GPUs or Local SSD disks. For more information, see  Attach
   * reservations to resource-based commitments.
   *
   * Specify this property only if you want to create new reservations to
   * attach. To attach existing reservations, specify theexistingReservations
   * property instead.
   *
   * @param Reservation[] $reservations
   */
  public function setReservations($reservations)
  {
    $this->reservations = $reservations;
  }
  /**
   * @return Reservation[]
   */
  public function getReservations()
  {
    return $this->reservations;
  }
  /**
   * Output only. [Output Only] Status information for Commitment resource.
   *
   * @param CommitmentResourceStatus $resourceStatus
   */
  public function setResourceStatus(CommitmentResourceStatus $resourceStatus)
  {
    $this->resourceStatus = $resourceStatus;
  }
  /**
   * @return CommitmentResourceStatus
   */
  public function getResourceStatus()
  {
    return $this->resourceStatus;
  }
  /**
   * The list of all the hardware resources, with their types and amounts, that
   * you want to commit to. Specify as a separate entry in the list for each
   * individual resource type.
   *
   * @param ResourceCommitment[] $resources
   */
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return ResourceCommitment[]
   */
  public function getResources()
  {
    return $this->resources;
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
   * The source commitment from which you are transferring resources to create
   * the new split commitment. For more information, see  Split commitments.
   *
   * @param string $splitSourceCommitment
   */
  public function setSplitSourceCommitment($splitSourceCommitment)
  {
    $this->splitSourceCommitment = $splitSourceCommitment;
  }
  /**
   * @return string
   */
  public function getSplitSourceCommitment()
  {
    return $this->splitSourceCommitment;
  }
  /**
   * Output only. [Output Only] Commitment start time inRFC3339 text format.
   *
   * @param string $startTimestamp
   */
  public function setStartTimestamp($startTimestamp)
  {
    $this->startTimestamp = $startTimestamp;
  }
  /**
   * @return string
   */
  public function getStartTimestamp()
  {
    return $this->startTimestamp;
  }
  /**
   * Output only. [Output Only] Status of the commitment with regards to
   * eventual expiration (each commitment has an end date defined). Status can
   * be one of the following values: NOT_YET_ACTIVE, ACTIVE, orEXPIRED.
   *
   * Accepted values: ACTIVE, CANCELLED, CREATING, EXPIRED, NOT_YET_ACTIVE
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
   * Output only. [Output Only] An optional, human-readable explanation of the
   * status.
   *
   * @param string $statusMessage
   */
  public function setStatusMessage($statusMessage)
  {
    $this->statusMessage = $statusMessage;
  }
  /**
   * @return string
   */
  public function getStatusMessage()
  {
    return $this->statusMessage;
  }
  /**
   * The type of commitment; specifies the machine series for which you want to
   * commit to purchasing resources. The choice of machine series affects the
   * discount rate and the eligible resource types.
   *
   *  The type must be one of the following:ACCELERATOR_OPTIMIZED,
   * ACCELERATOR_OPTIMIZED_A3,ACCELERATOR_OPTIMIZED_A3_MEGA,COMPUTE_OPTIMIZED,
   * COMPUTE_OPTIMIZED_C2D,  COMPUTE_OPTIMIZED_C3,
   * COMPUTE_OPTIMIZED_C3D,COMPUTE_OPTIMIZED_H3,
   * GENERAL_PURPOSE,GENERAL_PURPOSE_C4, GENERAL_PURPOSE_E2,GENERAL_PURPOSE_N2,
   * GENERAL_PURPOSE_N2D,GENERAL_PURPOSE_N4,
   * GENERAL_PURPOSE_T2D,GRAPHICS_OPTIMIZED,
   * GRAPHICS_OPTIMIZED_G4,MEMORY_OPTIMIZED,
   * MEMORY_OPTIMIZED_M3,MEMORY_OPTIMIZED_X4, STORAGE_OPTIMIZED_Z3. For example,
   * type MEMORY_OPTIMIZED specifies a commitment that applies only to eligible
   * resources of memory optimized M1 and M2 machine series. Type
   * GENERAL_PURPOSE specifies a commitment that applies only to eligible
   * resources of general purpose N1 machine series.
   *
   * Accepted values: ACCELERATOR_OPTIMIZED, ACCELERATOR_OPTIMIZED_A3,
   * ACCELERATOR_OPTIMIZED_A3_MEGA, ACCELERATOR_OPTIMIZED_A3_ULTRA,
   * ACCELERATOR_OPTIMIZED_A4, COMPUTE_OPTIMIZED, COMPUTE_OPTIMIZED_C2D,
   * COMPUTE_OPTIMIZED_C3, COMPUTE_OPTIMIZED_C3D, COMPUTE_OPTIMIZED_H3,
   * COMPUTE_OPTIMIZED_H4D, GENERAL_PURPOSE, GENERAL_PURPOSE_C4,
   * GENERAL_PURPOSE_C4A, GENERAL_PURPOSE_C4D, GENERAL_PURPOSE_E2,
   * GENERAL_PURPOSE_N2, GENERAL_PURPOSE_N2D, GENERAL_PURPOSE_N4,
   * GENERAL_PURPOSE_N4D, GENERAL_PURPOSE_T2D, GRAPHICS_OPTIMIZED,
   * GRAPHICS_OPTIMIZED_G4, MEMORY_OPTIMIZED, MEMORY_OPTIMIZED_M3,
   * MEMORY_OPTIMIZED_M4, MEMORY_OPTIMIZED_M4_6TB, MEMORY_OPTIMIZED_X4_16TB,
   * MEMORY_OPTIMIZED_X4_24TB, MEMORY_OPTIMIZED_X4_32TB, STORAGE_OPTIMIZED_Z3,
   * TYPE_UNSPECIFIED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Commitment::class, 'Google_Service_Compute_Commitment');
