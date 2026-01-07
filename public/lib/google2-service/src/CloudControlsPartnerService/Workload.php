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

namespace Google\Service\CloudControlsPartnerService;

class Workload extends \Google\Model
{
  /**
   * Unknown Partner.
   */
  public const PARTNER_PARTNER_UNSPECIFIED = 'PARTNER_UNSPECIFIED';
  /**
   * Enum representing S3NS (Thales) partner.
   */
  public const PARTNER_PARTNER_LOCAL_CONTROLS_BY_S3NS = 'PARTNER_LOCAL_CONTROLS_BY_S3NS';
  /**
   * Enum representing T_SYSTEM (TSI) partner.
   */
  public const PARTNER_PARTNER_SOVEREIGN_CONTROLS_BY_T_SYSTEMS = 'PARTNER_SOVEREIGN_CONTROLS_BY_T_SYSTEMS';
  /**
   * Enum representing SIA_MINSAIT (Indra) partner.
   */
  public const PARTNER_PARTNER_SOVEREIGN_CONTROLS_BY_SIA_MINSAIT = 'PARTNER_SOVEREIGN_CONTROLS_BY_SIA_MINSAIT';
  /**
   * Enum representing PSN (TIM) partner.
   */
  public const PARTNER_PARTNER_SOVEREIGN_CONTROLS_BY_PSN = 'PARTNER_SOVEREIGN_CONTROLS_BY_PSN';
  /**
   * Enum representing CNTXT (Kingdom of Saudi Arabia) partner.
   */
  public const PARTNER_PARTNER_SOVEREIGN_CONTROLS_BY_CNTXT = 'PARTNER_SOVEREIGN_CONTROLS_BY_CNTXT';
  /**
   * Enum representing CNXT (Kingdom of Saudi Arabia) partner offering without
   * EKM provisioning.
   */
  public const PARTNER_PARTNER_SOVEREIGN_CONTROLS_BY_CNTXT_NO_EKM = 'PARTNER_SOVEREIGN_CONTROLS_BY_CNTXT_NO_EKM';
  /**
   * Output only. Time the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The name of container folder of the assured workload
   *
   * @var string
   */
  public $folder;
  /**
   * Output only. Folder id this workload is associated with
   *
   * @var string
   */
  public $folderId;
  /**
   * Indicates whether a workload is fully onboarded.
   *
   * @var bool
   */
  public $isOnboarded;
  /**
   * The project id of the key management project for the workload
   *
   * @var string
   */
  public $keyManagementProjectId;
  /**
   * The Google Cloud location of the workload
   *
   * @var string
   */
  public $location;
  /**
   * Identifier. Format: `organizations/{organization}/locations/{location}/cust
   * omers/{customer}/workloads/{workload}`
   *
   * @var string
   */
  public $name;
  /**
   * Partner associated with this workload.
   *
   * @var string
   */
  public $partner;
  protected $workloadOnboardingStateType = WorkloadOnboardingState::class;
  protected $workloadOnboardingStateDataType = '';

  /**
   * Output only. Time the resource was created.
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
   * Output only. The name of container folder of the assured workload
   *
   * @param string $folder
   */
  public function setFolder($folder)
  {
    $this->folder = $folder;
  }
  /**
   * @return string
   */
  public function getFolder()
  {
    return $this->folder;
  }
  /**
   * Output only. Folder id this workload is associated with
   *
   * @param string $folderId
   */
  public function setFolderId($folderId)
  {
    $this->folderId = $folderId;
  }
  /**
   * @return string
   */
  public function getFolderId()
  {
    return $this->folderId;
  }
  /**
   * Indicates whether a workload is fully onboarded.
   *
   * @param bool $isOnboarded
   */
  public function setIsOnboarded($isOnboarded)
  {
    $this->isOnboarded = $isOnboarded;
  }
  /**
   * @return bool
   */
  public function getIsOnboarded()
  {
    return $this->isOnboarded;
  }
  /**
   * The project id of the key management project for the workload
   *
   * @param string $keyManagementProjectId
   */
  public function setKeyManagementProjectId($keyManagementProjectId)
  {
    $this->keyManagementProjectId = $keyManagementProjectId;
  }
  /**
   * @return string
   */
  public function getKeyManagementProjectId()
  {
    return $this->keyManagementProjectId;
  }
  /**
   * The Google Cloud location of the workload
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Identifier. Format: `organizations/{organization}/locations/{location}/cust
   * omers/{customer}/workloads/{workload}`
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
   * Partner associated with this workload.
   *
   * Accepted values: PARTNER_UNSPECIFIED, PARTNER_LOCAL_CONTROLS_BY_S3NS,
   * PARTNER_SOVEREIGN_CONTROLS_BY_T_SYSTEMS,
   * PARTNER_SOVEREIGN_CONTROLS_BY_SIA_MINSAIT,
   * PARTNER_SOVEREIGN_CONTROLS_BY_PSN, PARTNER_SOVEREIGN_CONTROLS_BY_CNTXT,
   * PARTNER_SOVEREIGN_CONTROLS_BY_CNTXT_NO_EKM
   *
   * @param self::PARTNER_* $partner
   */
  public function setPartner($partner)
  {
    $this->partner = $partner;
  }
  /**
   * @return self::PARTNER_*
   */
  public function getPartner()
  {
    return $this->partner;
  }
  /**
   * Container for workload onboarding steps.
   *
   * @param WorkloadOnboardingState $workloadOnboardingState
   */
  public function setWorkloadOnboardingState(WorkloadOnboardingState $workloadOnboardingState)
  {
    $this->workloadOnboardingState = $workloadOnboardingState;
  }
  /**
   * @return WorkloadOnboardingState
   */
  public function getWorkloadOnboardingState()
  {
    return $this->workloadOnboardingState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Workload::class, 'Google_Service_CloudControlsPartnerService_Workload');
