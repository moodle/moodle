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

namespace Google\Service\AdExchangeBuyerII;

class Deal extends \Google\Collection
{
  /**
   * A placeholder for an undefined creative pre-approval policy.
   */
  public const CREATIVE_PRE_APPROVAL_POLICY_CREATIVE_PRE_APPROVAL_POLICY_UNSPECIFIED = 'CREATIVE_PRE_APPROVAL_POLICY_UNSPECIFIED';
  /**
   * The seller needs to approve each creative before it can serve.
   */
  public const CREATIVE_PRE_APPROVAL_POLICY_SELLER_PRE_APPROVAL_REQUIRED = 'SELLER_PRE_APPROVAL_REQUIRED';
  /**
   * The seller does not need to approve each creative before it can serve.
   */
  public const CREATIVE_PRE_APPROVAL_POLICY_SELLER_PRE_APPROVAL_NOT_REQUIRED = 'SELLER_PRE_APPROVAL_NOT_REQUIRED';
  /**
   * A placeholder for an undefined creative safe-frame compatibility.
   */
  public const CREATIVE_SAFE_FRAME_COMPATIBILITY_CREATIVE_SAFE_FRAME_COMPATIBILITY_UNSPECIFIED = 'CREATIVE_SAFE_FRAME_COMPATIBILITY_UNSPECIFIED';
  /**
   * The creatives need to be compatible with the safe frame option.
   */
  public const CREATIVE_SAFE_FRAME_COMPATIBILITY_COMPATIBLE = 'COMPATIBLE';
  /**
   * The creatives can be incompatible with the safe frame option.
   */
  public const CREATIVE_SAFE_FRAME_COMPATIBILITY_INCOMPATIBLE = 'INCOMPATIBLE';
  /**
   * A placeholder for an undefined programmatic creative source.
   */
  public const PROGRAMMATIC_CREATIVE_SOURCE_PROGRAMMATIC_CREATIVE_SOURCE_UNSPECIFIED = 'PROGRAMMATIC_CREATIVE_SOURCE_UNSPECIFIED';
  /**
   * The advertiser provides the creatives.
   */
  public const PROGRAMMATIC_CREATIVE_SOURCE_ADVERTISER = 'ADVERTISER';
  /**
   * The publisher provides the creatives to be served.
   */
  public const PROGRAMMATIC_CREATIVE_SOURCE_PUBLISHER = 'PUBLISHER';
  /**
   * A placeholder for an undefined syndication product.
   */
  public const SYNDICATION_PRODUCT_SYNDICATION_PRODUCT_UNSPECIFIED = 'SYNDICATION_PRODUCT_UNSPECIFIED';
  /**
   * This typically represents a web page.
   */
  public const SYNDICATION_PRODUCT_CONTENT = 'CONTENT';
  /**
   * This represents a mobile property.
   */
  public const SYNDICATION_PRODUCT_MOBILE = 'MOBILE';
  /**
   * This represents video ad formats.
   */
  public const SYNDICATION_PRODUCT_VIDEO = 'VIDEO';
  /**
   * This represents ads shown within games.
   */
  public const SYNDICATION_PRODUCT_GAMES = 'GAMES';
  protected $collection_key = 'targetingCriterion';
  /**
   * Proposed flight end time of the deal. This will generally be stored in a
   * granularity of a second. A value is not required for Private Auction deals
   * or Preferred Deals.
   *
   * @var string
   */
  public $availableEndTime;
  /**
   * Optional. Proposed flight start time of the deal. This will generally be
   * stored in the granularity of one second since deal serving starts at
   * seconds boundary. Any time specified with more granularity (for example, in
   * milliseconds) will be truncated towards the start of time in seconds.
   *
   * @var string
   */
  public $availableStartTime;
  protected $buyerPrivateDataType = PrivateData::class;
  protected $buyerPrivateDataDataType = '';
  /**
   * The product ID from which this deal was created. Note: This field may be
   * set only when creating the resource. Modifying this field while updating
   * the resource will result in an error.
   *
   * @var string
   */
  public $createProductId;
  /**
   * Optional. Revision number of the product that the deal was created from. If
   * present on create, and the server `product_revision` has advanced since the
   * passed-in `create_product_revision`, an `ABORTED` error will be returned.
   * Note: This field may be set only when creating the resource. Modifying this
   * field while updating the resource will result in an error.
   *
   * @var string
   */
  public $createProductRevision;
  /**
   * Output only. The time of the deal creation.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Specifies the creative pre-approval policy.
   *
   * @var string
   */
  public $creativePreApprovalPolicy;
  protected $creativeRestrictionsType = CreativeRestrictions::class;
  protected $creativeRestrictionsDataType = '';
  /**
   * Output only. Specifies whether the creative is safeFrame compatible.
   *
   * @var string
   */
  public $creativeSafeFrameCompatibility;
  /**
   * Output only. A unique deal ID for the deal (server-assigned).
   *
   * @var string
   */
  public $dealId;
  protected $dealServingMetadataType = DealServingMetadata::class;
  protected $dealServingMetadataDataType = '';
  protected $dealTermsType = DealTerms::class;
  protected $dealTermsDataType = '';
  protected $deliveryControlType = DeliveryControl::class;
  protected $deliveryControlDataType = '';
  /**
   * Description for the deal terms.
   *
   * @var string
   */
  public $description;
  /**
   * The name of the deal.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The external deal ID assigned to this deal once the deal is
   * finalized. This is the deal ID that shows up in serving/reporting etc.
   *
   * @var string
   */
  public $externalDealId;
  /**
   * Output only. True, if the buyside inventory setup is complete for this
   * deal.
   *
   * @var bool
   */
  public $isSetupComplete;
  /**
   * Output only. Specifies the creative source for programmatic deals.
   * PUBLISHER means creative is provided by seller and ADVERTISER means
   * creative is provided by buyer.
   *
   * @var string
   */
  public $programmaticCreativeSource;
  /**
   * Output only. ID of the proposal that this deal is part of.
   *
   * @var string
   */
  public $proposalId;
  protected $sellerContactsType = ContactInformation::class;
  protected $sellerContactsDataType = 'array';
  /**
   * The syndication product associated with the deal. Note: This field may be
   * set only when creating the resource. Modifying this field while updating
   * the resource will result in an error.
   *
   * @var string
   */
  public $syndicationProduct;
  protected $targetingType = MarketplaceTargeting::class;
  protected $targetingDataType = '';
  protected $targetingCriterionType = TargetingCriteria::class;
  protected $targetingCriterionDataType = 'array';
  /**
   * Output only. The time when the deal was last updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * The web property code for the seller copied over from the product.
   *
   * @var string
   */
  public $webPropertyCode;

  /**
   * Proposed flight end time of the deal. This will generally be stored in a
   * granularity of a second. A value is not required for Private Auction deals
   * or Preferred Deals.
   *
   * @param string $availableEndTime
   */
  public function setAvailableEndTime($availableEndTime)
  {
    $this->availableEndTime = $availableEndTime;
  }
  /**
   * @return string
   */
  public function getAvailableEndTime()
  {
    return $this->availableEndTime;
  }
  /**
   * Optional. Proposed flight start time of the deal. This will generally be
   * stored in the granularity of one second since deal serving starts at
   * seconds boundary. Any time specified with more granularity (for example, in
   * milliseconds) will be truncated towards the start of time in seconds.
   *
   * @param string $availableStartTime
   */
  public function setAvailableStartTime($availableStartTime)
  {
    $this->availableStartTime = $availableStartTime;
  }
  /**
   * @return string
   */
  public function getAvailableStartTime()
  {
    return $this->availableStartTime;
  }
  /**
   * Buyer private data (hidden from seller).
   *
   * @param PrivateData $buyerPrivateData
   */
  public function setBuyerPrivateData(PrivateData $buyerPrivateData)
  {
    $this->buyerPrivateData = $buyerPrivateData;
  }
  /**
   * @return PrivateData
   */
  public function getBuyerPrivateData()
  {
    return $this->buyerPrivateData;
  }
  /**
   * The product ID from which this deal was created. Note: This field may be
   * set only when creating the resource. Modifying this field while updating
   * the resource will result in an error.
   *
   * @param string $createProductId
   */
  public function setCreateProductId($createProductId)
  {
    $this->createProductId = $createProductId;
  }
  /**
   * @return string
   */
  public function getCreateProductId()
  {
    return $this->createProductId;
  }
  /**
   * Optional. Revision number of the product that the deal was created from. If
   * present on create, and the server `product_revision` has advanced since the
   * passed-in `create_product_revision`, an `ABORTED` error will be returned.
   * Note: This field may be set only when creating the resource. Modifying this
   * field while updating the resource will result in an error.
   *
   * @param string $createProductRevision
   */
  public function setCreateProductRevision($createProductRevision)
  {
    $this->createProductRevision = $createProductRevision;
  }
  /**
   * @return string
   */
  public function getCreateProductRevision()
  {
    return $this->createProductRevision;
  }
  /**
   * Output only. The time of the deal creation.
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
   * Output only. Specifies the creative pre-approval policy.
   *
   * Accepted values: CREATIVE_PRE_APPROVAL_POLICY_UNSPECIFIED,
   * SELLER_PRE_APPROVAL_REQUIRED, SELLER_PRE_APPROVAL_NOT_REQUIRED
   *
   * @param self::CREATIVE_PRE_APPROVAL_POLICY_* $creativePreApprovalPolicy
   */
  public function setCreativePreApprovalPolicy($creativePreApprovalPolicy)
  {
    $this->creativePreApprovalPolicy = $creativePreApprovalPolicy;
  }
  /**
   * @return self::CREATIVE_PRE_APPROVAL_POLICY_*
   */
  public function getCreativePreApprovalPolicy()
  {
    return $this->creativePreApprovalPolicy;
  }
  /**
   * Output only. Restricitions about the creatives associated with the deal
   * (for example, size) This is available for Programmatic Guaranteed/Preferred
   * Deals in Ad Manager.
   *
   * @param CreativeRestrictions $creativeRestrictions
   */
  public function setCreativeRestrictions(CreativeRestrictions $creativeRestrictions)
  {
    $this->creativeRestrictions = $creativeRestrictions;
  }
  /**
   * @return CreativeRestrictions
   */
  public function getCreativeRestrictions()
  {
    return $this->creativeRestrictions;
  }
  /**
   * Output only. Specifies whether the creative is safeFrame compatible.
   *
   * Accepted values: CREATIVE_SAFE_FRAME_COMPATIBILITY_UNSPECIFIED, COMPATIBLE,
   * INCOMPATIBLE
   *
   * @param self::CREATIVE_SAFE_FRAME_COMPATIBILITY_* $creativeSafeFrameCompatibility
   */
  public function setCreativeSafeFrameCompatibility($creativeSafeFrameCompatibility)
  {
    $this->creativeSafeFrameCompatibility = $creativeSafeFrameCompatibility;
  }
  /**
   * @return self::CREATIVE_SAFE_FRAME_COMPATIBILITY_*
   */
  public function getCreativeSafeFrameCompatibility()
  {
    return $this->creativeSafeFrameCompatibility;
  }
  /**
   * Output only. A unique deal ID for the deal (server-assigned).
   *
   * @param string $dealId
   */
  public function setDealId($dealId)
  {
    $this->dealId = $dealId;
  }
  /**
   * @return string
   */
  public function getDealId()
  {
    return $this->dealId;
  }
  /**
   * Output only. Metadata about the serving status of this deal.
   *
   * @param DealServingMetadata $dealServingMetadata
   */
  public function setDealServingMetadata(DealServingMetadata $dealServingMetadata)
  {
    $this->dealServingMetadata = $dealServingMetadata;
  }
  /**
   * @return DealServingMetadata
   */
  public function getDealServingMetadata()
  {
    return $this->dealServingMetadata;
  }
  /**
   * The negotiable terms of the deal.
   *
   * @param DealTerms $dealTerms
   */
  public function setDealTerms(DealTerms $dealTerms)
  {
    $this->dealTerms = $dealTerms;
  }
  /**
   * @return DealTerms
   */
  public function getDealTerms()
  {
    return $this->dealTerms;
  }
  /**
   * The set of fields around delivery control that are interesting for a buyer
   * to see but are non-negotiable. These are set by the publisher.
   *
   * @param DeliveryControl $deliveryControl
   */
  public function setDeliveryControl(DeliveryControl $deliveryControl)
  {
    $this->deliveryControl = $deliveryControl;
  }
  /**
   * @return DeliveryControl
   */
  public function getDeliveryControl()
  {
    return $this->deliveryControl;
  }
  /**
   * Description for the deal terms.
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
   * The name of the deal.
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
   * Output only. The external deal ID assigned to this deal once the deal is
   * finalized. This is the deal ID that shows up in serving/reporting etc.
   *
   * @param string $externalDealId
   */
  public function setExternalDealId($externalDealId)
  {
    $this->externalDealId = $externalDealId;
  }
  /**
   * @return string
   */
  public function getExternalDealId()
  {
    return $this->externalDealId;
  }
  /**
   * Output only. True, if the buyside inventory setup is complete for this
   * deal.
   *
   * @param bool $isSetupComplete
   */
  public function setIsSetupComplete($isSetupComplete)
  {
    $this->isSetupComplete = $isSetupComplete;
  }
  /**
   * @return bool
   */
  public function getIsSetupComplete()
  {
    return $this->isSetupComplete;
  }
  /**
   * Output only. Specifies the creative source for programmatic deals.
   * PUBLISHER means creative is provided by seller and ADVERTISER means
   * creative is provided by buyer.
   *
   * Accepted values: PROGRAMMATIC_CREATIVE_SOURCE_UNSPECIFIED, ADVERTISER,
   * PUBLISHER
   *
   * @param self::PROGRAMMATIC_CREATIVE_SOURCE_* $programmaticCreativeSource
   */
  public function setProgrammaticCreativeSource($programmaticCreativeSource)
  {
    $this->programmaticCreativeSource = $programmaticCreativeSource;
  }
  /**
   * @return self::PROGRAMMATIC_CREATIVE_SOURCE_*
   */
  public function getProgrammaticCreativeSource()
  {
    return $this->programmaticCreativeSource;
  }
  /**
   * Output only. ID of the proposal that this deal is part of.
   *
   * @param string $proposalId
   */
  public function setProposalId($proposalId)
  {
    $this->proposalId = $proposalId;
  }
  /**
   * @return string
   */
  public function getProposalId()
  {
    return $this->proposalId;
  }
  /**
   * Output only. Seller contact information for the deal.
   *
   * @param ContactInformation[] $sellerContacts
   */
  public function setSellerContacts($sellerContacts)
  {
    $this->sellerContacts = $sellerContacts;
  }
  /**
   * @return ContactInformation[]
   */
  public function getSellerContacts()
  {
    return $this->sellerContacts;
  }
  /**
   * The syndication product associated with the deal. Note: This field may be
   * set only when creating the resource. Modifying this field while updating
   * the resource will result in an error.
   *
   * Accepted values: SYNDICATION_PRODUCT_UNSPECIFIED, CONTENT, MOBILE, VIDEO,
   * GAMES
   *
   * @param self::SYNDICATION_PRODUCT_* $syndicationProduct
   */
  public function setSyndicationProduct($syndicationProduct)
  {
    $this->syndicationProduct = $syndicationProduct;
  }
  /**
   * @return self::SYNDICATION_PRODUCT_*
   */
  public function getSyndicationProduct()
  {
    return $this->syndicationProduct;
  }
  /**
   * Output only. Specifies the subset of inventory targeted by the deal.
   *
   * @param MarketplaceTargeting $targeting
   */
  public function setTargeting(MarketplaceTargeting $targeting)
  {
    $this->targeting = $targeting;
  }
  /**
   * @return MarketplaceTargeting
   */
  public function getTargeting()
  {
    return $this->targeting;
  }
  /**
   * The shared targeting visible to buyers and sellers. Each shared targeting
   * entity is AND'd together.
   *
   * @param TargetingCriteria[] $targetingCriterion
   */
  public function setTargetingCriterion($targetingCriterion)
  {
    $this->targetingCriterion = $targetingCriterion;
  }
  /**
   * @return TargetingCriteria[]
   */
  public function getTargetingCriterion()
  {
    return $this->targetingCriterion;
  }
  /**
   * Output only. The time when the deal was last updated.
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
  /**
   * The web property code for the seller copied over from the product.
   *
   * @param string $webPropertyCode
   */
  public function setWebPropertyCode($webPropertyCode)
  {
    $this->webPropertyCode = $webPropertyCode;
  }
  /**
   * @return string
   */
  public function getWebPropertyCode()
  {
    return $this->webPropertyCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Deal::class, 'Google_Service_AdExchangeBuyerII_Deal');
