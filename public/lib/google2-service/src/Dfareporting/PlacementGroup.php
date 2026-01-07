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

namespace Google\Service\Dfareporting;

class PlacementGroup extends \Google\Collection
{
  public const ACTIVE_STATUS_PLACEMENT_STATUS_UNKNOWN = 'PLACEMENT_STATUS_UNKNOWN';
  public const ACTIVE_STATUS_PLACEMENT_STATUS_ACTIVE = 'PLACEMENT_STATUS_ACTIVE';
  public const ACTIVE_STATUS_PLACEMENT_STATUS_INACTIVE = 'PLACEMENT_STATUS_INACTIVE';
  public const ACTIVE_STATUS_PLACEMENT_STATUS_ARCHIVED = 'PLACEMENT_STATUS_ARCHIVED';
  public const ACTIVE_STATUS_PLACEMENT_STATUS_PERMANENTLY_ARCHIVED = 'PLACEMENT_STATUS_PERMANENTLY_ARCHIVED';
  /**
   * A simple group of site-placements (tags). Basically acts as a single
   * pricing point for a group of tags.
   */
  public const PLACEMENT_GROUP_TYPE_PLACEMENT_PACKAGE = 'PLACEMENT_PACKAGE';
  /**
   * A group of site-placements (tags) that not only acts as a single pricing
   * point but also assumes that all the tags in it will be served at the same
   * time. This kind of group requires one of its assigned site-placements to be
   * marked as primary for reporting purposes.
   */
  public const PLACEMENT_GROUP_TYPE_PLACEMENT_ROADBLOCK = 'PLACEMENT_ROADBLOCK';
  protected $collection_key = 'childPlacementIds';
  /**
   * Account ID of this placement group. This is a read-only field that can be
   * left blank.
   *
   * @var string
   */
  public $accountId;
  /**
   * Whether this placement group is active, inactive, archived or permanently
   * archived.
   *
   * @var string
   */
  public $activeStatus;
  /**
   * Advertiser ID of this placement group. This is a required field on
   * insertion.
   *
   * @var string
   */
  public $advertiserId;
  protected $advertiserIdDimensionValueType = DimensionValue::class;
  protected $advertiserIdDimensionValueDataType = '';
  /**
   * Campaign ID of this placement group. This field is required on insertion.
   *
   * @var string
   */
  public $campaignId;
  protected $campaignIdDimensionValueType = DimensionValue::class;
  protected $campaignIdDimensionValueDataType = '';
  /**
   * IDs of placements which are assigned to this placement group. This is a
   * read-only, auto-generated field.
   *
   * @var string[]
   */
  public $childPlacementIds;
  /**
   * Comments for this placement group.
   *
   * @var string
   */
  public $comment;
  /**
   * ID of the content category assigned to this placement group.
   *
   * @var string
   */
  public $contentCategoryId;
  protected $createInfoType = LastModifiedInfo::class;
  protected $createInfoDataType = '';
  /**
   * Directory site ID associated with this placement group. On insert, you must
   * set either this field or the site_id field to specify the site associated
   * with this placement group. This is a required field that is read-only after
   * insertion.
   *
   * @var string
   */
  public $directorySiteId;
  protected $directorySiteIdDimensionValueType = DimensionValue::class;
  protected $directorySiteIdDimensionValueDataType = '';
  /**
   * External ID for this placement.
   *
   * @var string
   */
  public $externalId;
  /**
   * ID of this placement group. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  protected $idDimensionValueType = DimensionValue::class;
  protected $idDimensionValueDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#placementGroup".
   *
   * @var string
   */
  public $kind;
  protected $lastModifiedInfoType = LastModifiedInfo::class;
  protected $lastModifiedInfoDataType = '';
  /**
   * Name of this placement group. This is a required field and must be less
   * than 256 characters long.
   *
   * @var string
   */
  public $name;
  /**
   * Type of this placement group. A package is a simple group of placements
   * that acts as a single pricing point for a group of tags. A roadblock is a
   * group of placements that not only acts as a single pricing point, but also
   * assumes that all the tags in it will be served at the same time. A
   * roadblock requires one of its assigned placements to be marked as primary
   * for reporting. This field is required on insertion.
   *
   * @var string
   */
  public $placementGroupType;
  /**
   * ID of the placement strategy assigned to this placement group.
   *
   * @var string
   */
  public $placementStrategyId;
  protected $pricingScheduleType = PricingSchedule::class;
  protected $pricingScheduleDataType = '';
  /**
   * ID of the primary placement, used to calculate the media cost of a
   * roadblock (placement group). Modifying this field will automatically modify
   * the primary field on all affected roadblock child placements.
   *
   * @var string
   */
  public $primaryPlacementId;
  protected $primaryPlacementIdDimensionValueType = DimensionValue::class;
  protected $primaryPlacementIdDimensionValueDataType = '';
  /**
   * Site ID associated with this placement group. On insert, you must set
   * either this field or the directorySiteId field to specify the site
   * associated with this placement group. This is a required field that is
   * read-only after insertion.
   *
   * @var string
   */
  public $siteId;
  protected $siteIdDimensionValueType = DimensionValue::class;
  protected $siteIdDimensionValueDataType = '';
  /**
   * Subaccount ID of this placement group. This is a read-only field that can
   * be left blank.
   *
   * @var string
   */
  public $subaccountId;

  /**
   * Account ID of this placement group. This is a read-only field that can be
   * left blank.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * Whether this placement group is active, inactive, archived or permanently
   * archived.
   *
   * Accepted values: PLACEMENT_STATUS_UNKNOWN, PLACEMENT_STATUS_ACTIVE,
   * PLACEMENT_STATUS_INACTIVE, PLACEMENT_STATUS_ARCHIVED,
   * PLACEMENT_STATUS_PERMANENTLY_ARCHIVED
   *
   * @param self::ACTIVE_STATUS_* $activeStatus
   */
  public function setActiveStatus($activeStatus)
  {
    $this->activeStatus = $activeStatus;
  }
  /**
   * @return self::ACTIVE_STATUS_*
   */
  public function getActiveStatus()
  {
    return $this->activeStatus;
  }
  /**
   * Advertiser ID of this placement group. This is a required field on
   * insertion.
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
   * Dimension value for the ID of the advertiser. This is a read-only, auto-
   * generated field.
   *
   * @param DimensionValue $advertiserIdDimensionValue
   */
  public function setAdvertiserIdDimensionValue(DimensionValue $advertiserIdDimensionValue)
  {
    $this->advertiserIdDimensionValue = $advertiserIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getAdvertiserIdDimensionValue()
  {
    return $this->advertiserIdDimensionValue;
  }
  /**
   * Campaign ID of this placement group. This field is required on insertion.
   *
   * @param string $campaignId
   */
  public function setCampaignId($campaignId)
  {
    $this->campaignId = $campaignId;
  }
  /**
   * @return string
   */
  public function getCampaignId()
  {
    return $this->campaignId;
  }
  /**
   * Dimension value for the ID of the campaign. This is a read-only, auto-
   * generated field.
   *
   * @param DimensionValue $campaignIdDimensionValue
   */
  public function setCampaignIdDimensionValue(DimensionValue $campaignIdDimensionValue)
  {
    $this->campaignIdDimensionValue = $campaignIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getCampaignIdDimensionValue()
  {
    return $this->campaignIdDimensionValue;
  }
  /**
   * IDs of placements which are assigned to this placement group. This is a
   * read-only, auto-generated field.
   *
   * @param string[] $childPlacementIds
   */
  public function setChildPlacementIds($childPlacementIds)
  {
    $this->childPlacementIds = $childPlacementIds;
  }
  /**
   * @return string[]
   */
  public function getChildPlacementIds()
  {
    return $this->childPlacementIds;
  }
  /**
   * Comments for this placement group.
   *
   * @param string $comment
   */
  public function setComment($comment)
  {
    $this->comment = $comment;
  }
  /**
   * @return string
   */
  public function getComment()
  {
    return $this->comment;
  }
  /**
   * ID of the content category assigned to this placement group.
   *
   * @param string $contentCategoryId
   */
  public function setContentCategoryId($contentCategoryId)
  {
    $this->contentCategoryId = $contentCategoryId;
  }
  /**
   * @return string
   */
  public function getContentCategoryId()
  {
    return $this->contentCategoryId;
  }
  /**
   * Information about the creation of this placement group. This is a read-only
   * field.
   *
   * @param LastModifiedInfo $createInfo
   */
  public function setCreateInfo(LastModifiedInfo $createInfo)
  {
    $this->createInfo = $createInfo;
  }
  /**
   * @return LastModifiedInfo
   */
  public function getCreateInfo()
  {
    return $this->createInfo;
  }
  /**
   * Directory site ID associated with this placement group. On insert, you must
   * set either this field or the site_id field to specify the site associated
   * with this placement group. This is a required field that is read-only after
   * insertion.
   *
   * @param string $directorySiteId
   */
  public function setDirectorySiteId($directorySiteId)
  {
    $this->directorySiteId = $directorySiteId;
  }
  /**
   * @return string
   */
  public function getDirectorySiteId()
  {
    return $this->directorySiteId;
  }
  /**
   * Dimension value for the ID of the directory site. This is a read-only,
   * auto-generated field.
   *
   * @param DimensionValue $directorySiteIdDimensionValue
   */
  public function setDirectorySiteIdDimensionValue(DimensionValue $directorySiteIdDimensionValue)
  {
    $this->directorySiteIdDimensionValue = $directorySiteIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getDirectorySiteIdDimensionValue()
  {
    return $this->directorySiteIdDimensionValue;
  }
  /**
   * External ID for this placement.
   *
   * @param string $externalId
   */
  public function setExternalId($externalId)
  {
    $this->externalId = $externalId;
  }
  /**
   * @return string
   */
  public function getExternalId()
  {
    return $this->externalId;
  }
  /**
   * ID of this placement group. This is a read-only, auto-generated field.
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
   * Dimension value for the ID of this placement group. This is a read-only,
   * auto-generated field.
   *
   * @param DimensionValue $idDimensionValue
   */
  public function setIdDimensionValue(DimensionValue $idDimensionValue)
  {
    $this->idDimensionValue = $idDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getIdDimensionValue()
  {
    return $this->idDimensionValue;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#placementGroup".
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
   * Information about the most recent modification of this placement group.
   * This is a read-only field.
   *
   * @param LastModifiedInfo $lastModifiedInfo
   */
  public function setLastModifiedInfo(LastModifiedInfo $lastModifiedInfo)
  {
    $this->lastModifiedInfo = $lastModifiedInfo;
  }
  /**
   * @return LastModifiedInfo
   */
  public function getLastModifiedInfo()
  {
    return $this->lastModifiedInfo;
  }
  /**
   * Name of this placement group. This is a required field and must be less
   * than 256 characters long.
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
   * Type of this placement group. A package is a simple group of placements
   * that acts as a single pricing point for a group of tags. A roadblock is a
   * group of placements that not only acts as a single pricing point, but also
   * assumes that all the tags in it will be served at the same time. A
   * roadblock requires one of its assigned placements to be marked as primary
   * for reporting. This field is required on insertion.
   *
   * Accepted values: PLACEMENT_PACKAGE, PLACEMENT_ROADBLOCK
   *
   * @param self::PLACEMENT_GROUP_TYPE_* $placementGroupType
   */
  public function setPlacementGroupType($placementGroupType)
  {
    $this->placementGroupType = $placementGroupType;
  }
  /**
   * @return self::PLACEMENT_GROUP_TYPE_*
   */
  public function getPlacementGroupType()
  {
    return $this->placementGroupType;
  }
  /**
   * ID of the placement strategy assigned to this placement group.
   *
   * @param string $placementStrategyId
   */
  public function setPlacementStrategyId($placementStrategyId)
  {
    $this->placementStrategyId = $placementStrategyId;
  }
  /**
   * @return string
   */
  public function getPlacementStrategyId()
  {
    return $this->placementStrategyId;
  }
  /**
   * Pricing schedule of this placement group. This field is required on
   * insertion.
   *
   * @param PricingSchedule $pricingSchedule
   */
  public function setPricingSchedule(PricingSchedule $pricingSchedule)
  {
    $this->pricingSchedule = $pricingSchedule;
  }
  /**
   * @return PricingSchedule
   */
  public function getPricingSchedule()
  {
    return $this->pricingSchedule;
  }
  /**
   * ID of the primary placement, used to calculate the media cost of a
   * roadblock (placement group). Modifying this field will automatically modify
   * the primary field on all affected roadblock child placements.
   *
   * @param string $primaryPlacementId
   */
  public function setPrimaryPlacementId($primaryPlacementId)
  {
    $this->primaryPlacementId = $primaryPlacementId;
  }
  /**
   * @return string
   */
  public function getPrimaryPlacementId()
  {
    return $this->primaryPlacementId;
  }
  /**
   * Dimension value for the ID of the primary placement. This is a read-only,
   * auto-generated field.
   *
   * @param DimensionValue $primaryPlacementIdDimensionValue
   */
  public function setPrimaryPlacementIdDimensionValue(DimensionValue $primaryPlacementIdDimensionValue)
  {
    $this->primaryPlacementIdDimensionValue = $primaryPlacementIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getPrimaryPlacementIdDimensionValue()
  {
    return $this->primaryPlacementIdDimensionValue;
  }
  /**
   * Site ID associated with this placement group. On insert, you must set
   * either this field or the directorySiteId field to specify the site
   * associated with this placement group. This is a required field that is
   * read-only after insertion.
   *
   * @param string $siteId
   */
  public function setSiteId($siteId)
  {
    $this->siteId = $siteId;
  }
  /**
   * @return string
   */
  public function getSiteId()
  {
    return $this->siteId;
  }
  /**
   * Dimension value for the ID of the site. This is a read-only, auto-generated
   * field.
   *
   * @param DimensionValue $siteIdDimensionValue
   */
  public function setSiteIdDimensionValue(DimensionValue $siteIdDimensionValue)
  {
    $this->siteIdDimensionValue = $siteIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getSiteIdDimensionValue()
  {
    return $this->siteIdDimensionValue;
  }
  /**
   * Subaccount ID of this placement group. This is a read-only field that can
   * be left blank.
   *
   * @param string $subaccountId
   */
  public function setSubaccountId($subaccountId)
  {
    $this->subaccountId = $subaccountId;
  }
  /**
   * @return string
   */
  public function getSubaccountId()
  {
    return $this->subaccountId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlacementGroup::class, 'Google_Service_Dfareporting_PlacementGroup');
