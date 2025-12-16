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

namespace Google\Service\DisplayVideo;

class CustomBiddingAlgorithm extends \Google\Collection
{
  /**
   * Algorithm type is not specified or is unknown in this version.
   */
  public const CUSTOM_BIDDING_ALGORITHM_TYPE_CUSTOM_BIDDING_ALGORITHM_TYPE_UNSPECIFIED = 'CUSTOM_BIDDING_ALGORITHM_TYPE_UNSPECIFIED';
  /**
   * Algorithm generated through customer-uploaded custom bidding script files.
   */
  public const CUSTOM_BIDDING_ALGORITHM_TYPE_SCRIPT_BASED = 'SCRIPT_BASED';
  /**
   * Algorithm based in defined rules. These rules are defined in the API using
   * the AlgorithmRules object. This algorithm type is only available to
   * allowlisted customers. Other customers attempting to use this type will
   * receive an error.
   */
  public const CUSTOM_BIDDING_ALGORITHM_TYPE_RULE_BASED = 'RULE_BASED';
  /**
   * Default value when status is not specified or is unknown in this version.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_UNSPECIFIED = 'ENTITY_STATUS_UNSPECIFIED';
  /**
   * The entity is enabled to bid and spend budget.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_ACTIVE = 'ENTITY_STATUS_ACTIVE';
  /**
   * The entity is archived. Bidding and budget spending are disabled. An entity
   * can be deleted after archived. Deleted entities cannot be retrieved.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_ARCHIVED = 'ENTITY_STATUS_ARCHIVED';
  /**
   * The entity is under draft. Bidding and budget spending are disabled.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_DRAFT = 'ENTITY_STATUS_DRAFT';
  /**
   * Bidding and budget spending are paused for the entity.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_PAUSED = 'ENTITY_STATUS_PAUSED';
  /**
   * The entity is scheduled for deletion.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_SCHEDULED_FOR_DELETION = 'ENTITY_STATUS_SCHEDULED_FOR_DELETION';
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const THIRD_PARTY_OPTIMIZATION_PARTNER_UNKNOWN = 'UNKNOWN';
  /**
   * Third party data science service provider that DV3 partners/advertisers can
   * partner with.
   */
  public const THIRD_PARTY_OPTIMIZATION_PARTNER_SCIBIDS = 'SCIBIDS';
  /**
   * Third party attention measurement service provider that DV3
   * partners/advertisers can partner with.
   */
  public const THIRD_PARTY_OPTIMIZATION_PARTNER_ADELAIDE = 'ADELAIDE';
  protected $collection_key = 'sharedAdvertiserIds';
  /**
   * Immutable. The unique ID of the advertiser that owns the custom bidding
   * algorithm.
   *
   * @var string
   */
  public $advertiserId;
  /**
   * Output only. The unique ID of the custom bidding algorithm. Assigned by the
   * system.
   *
   * @var string
   */
  public $customBiddingAlgorithmId;
  /**
   * Required. Immutable. The type of custom bidding algorithm.
   *
   * @var string
   */
  public $customBiddingAlgorithmType;
  /**
   * Required. The display name of the custom bidding algorithm. Must be UTF-8
   * encoded with a maximum size of 240 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * Controls whether or not the custom bidding algorithm can be used as a
   * bidding strategy. Accepted values are: * `ENTITY_STATUS_ACTIVE` *
   * `ENTITY_STATUS_ARCHIVED`
   *
   * @var string
   */
  public $entityStatus;
  protected $modelDetailsType = CustomBiddingModelDetails::class;
  protected $modelDetailsDataType = 'array';
  /**
   * Output only. The resource name of the custom bidding algorithm.
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. The unique ID of the partner that owns the custom bidding
   * algorithm.
   *
   * @var string
   */
  public $partnerId;
  /**
   * The IDs of the advertisers who have access to this algorithm. If
   * advertiser_id is set, this field will only consist of that value. This
   * field will not be set if the algorithm [`owner`](/display-video/api/referen
   * ce/rest/v1/customBiddingAlgorithms#CustomBiddingAlgorithm.FIELDS.oneof_owne
   * r) is a partner and is being retrieved using an advertiser
   * [`accessor`](/display-video/api/reference/rest/v1/customBiddingAlgorithms/l
   * ist#body.QUERY_PARAMETERS.oneof_accessor).
   *
   * @var string[]
   */
  public $sharedAdvertiserIds;
  /**
   * Optional. Immutable. Designates the third party optimization partner that
   * manages this algorithm.
   *
   * @var string
   */
  public $thirdPartyOptimizationPartner;

  /**
   * Immutable. The unique ID of the advertiser that owns the custom bidding
   * algorithm.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * Output only. The unique ID of the custom bidding algorithm. Assigned by the
   * system.
   *
   * @param string $customBiddingAlgorithmId
   */
  public function setCustomBiddingAlgorithmId($customBiddingAlgorithmId)
  {
    $this->customBiddingAlgorithmId = $customBiddingAlgorithmId;
  }
  /**
   * @return string
   */
  public function getCustomBiddingAlgorithmId()
  {
    return $this->customBiddingAlgorithmId;
  }
  /**
   * Required. Immutable. The type of custom bidding algorithm.
   *
   * Accepted values: CUSTOM_BIDDING_ALGORITHM_TYPE_UNSPECIFIED, SCRIPT_BASED,
   * RULE_BASED
   *
   * @param self::CUSTOM_BIDDING_ALGORITHM_TYPE_* $customBiddingAlgorithmType
   */
  public function setCustomBiddingAlgorithmType($customBiddingAlgorithmType)
  {
    $this->customBiddingAlgorithmType = $customBiddingAlgorithmType;
  }
  /**
   * @return self::CUSTOM_BIDDING_ALGORITHM_TYPE_*
   */
  public function getCustomBiddingAlgorithmType()
  {
    return $this->customBiddingAlgorithmType;
  }
  /**
   * Required. The display name of the custom bidding algorithm. Must be UTF-8
   * encoded with a maximum size of 240 bytes.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Controls whether or not the custom bidding algorithm can be used as a
   * bidding strategy. Accepted values are: * `ENTITY_STATUS_ACTIVE` *
   * `ENTITY_STATUS_ARCHIVED`
   *
   * Accepted values: ENTITY_STATUS_UNSPECIFIED, ENTITY_STATUS_ACTIVE,
   * ENTITY_STATUS_ARCHIVED, ENTITY_STATUS_DRAFT, ENTITY_STATUS_PAUSED,
   * ENTITY_STATUS_SCHEDULED_FOR_DELETION
   *
   * @param self::ENTITY_STATUS_* $entityStatus
   */
  public function setEntityStatus($entityStatus)
  {
    $this->entityStatus = $entityStatus;
  }
  /**
   * @return self::ENTITY_STATUS_*
   */
  public function getEntityStatus()
  {
    return $this->entityStatus;
  }
  /**
   * Output only. The details of custom bidding models for each advertiser who
   * has access. This field may only include the details of the queried
   * advertiser if the algorithm [`owner`](/display-video/api/reference/rest/v1/
   * customBiddingAlgorithms#CustomBiddingAlgorithm.FIELDS.oneof_owner) is a
   * partner and is being retrieved using an advertiser [`accessor`](/display-vi
   * deo/api/reference/rest/v1/customBiddingAlgorithms/list#body.QUERY_PARAMETER
   * S.oneof_accessor).
   *
   * @param CustomBiddingModelDetails[] $modelDetails
   */
  public function setModelDetails($modelDetails)
  {
    $this->modelDetails = $modelDetails;
  }
  /**
   * @return CustomBiddingModelDetails[]
   */
  public function getModelDetails()
  {
    return $this->modelDetails;
  }
  /**
   * Output only. The resource name of the custom bidding algorithm.
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
   * Immutable. The unique ID of the partner that owns the custom bidding
   * algorithm.
   *
   * @param string $partnerId
   */
  public function setPartnerId($partnerId)
  {
    $this->partnerId = $partnerId;
  }
  /**
   * @return string
   */
  public function getPartnerId()
  {
    return $this->partnerId;
  }
  /**
   * The IDs of the advertisers who have access to this algorithm. If
   * advertiser_id is set, this field will only consist of that value. This
   * field will not be set if the algorithm [`owner`](/display-video/api/referen
   * ce/rest/v1/customBiddingAlgorithms#CustomBiddingAlgorithm.FIELDS.oneof_owne
   * r) is a partner and is being retrieved using an advertiser
   * [`accessor`](/display-video/api/reference/rest/v1/customBiddingAlgorithms/l
   * ist#body.QUERY_PARAMETERS.oneof_accessor).
   *
   * @param string[] $sharedAdvertiserIds
   */
  public function setSharedAdvertiserIds($sharedAdvertiserIds)
  {
    $this->sharedAdvertiserIds = $sharedAdvertiserIds;
  }
  /**
   * @return string[]
   */
  public function getSharedAdvertiserIds()
  {
    return $this->sharedAdvertiserIds;
  }
  /**
   * Optional. Immutable. Designates the third party optimization partner that
   * manages this algorithm.
   *
   * Accepted values: UNKNOWN, SCIBIDS, ADELAIDE
   *
   * @param self::THIRD_PARTY_OPTIMIZATION_PARTNER_* $thirdPartyOptimizationPartner
   */
  public function setThirdPartyOptimizationPartner($thirdPartyOptimizationPartner)
  {
    $this->thirdPartyOptimizationPartner = $thirdPartyOptimizationPartner;
  }
  /**
   * @return self::THIRD_PARTY_OPTIMIZATION_PARTNER_*
   */
  public function getThirdPartyOptimizationPartner()
  {
    return $this->thirdPartyOptimizationPartner;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomBiddingAlgorithm::class, 'Google_Service_DisplayVideo_CustomBiddingAlgorithm');
