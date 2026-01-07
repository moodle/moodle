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

class Product extends \Google\Collection
{
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
   * The proposed end time for the deal. The field will be truncated to the
   * order of seconds during serving.
   *
   * @var string
   */
  public $availableEndTime;
  /**
   * Inventory availability dates. The start time will be truncated to seconds
   * during serving. Thus, a field specified as 3:23:34.456 (HH:mm:ss.SSS) will
   * be truncated to 3:23:34 when serving.
   *
   * @var string
   */
  public $availableStartTime;
  /**
   * Creation time.
   *
   * @var string
   */
  public $createTime;
  protected $creatorContactsType = ContactInformation::class;
  protected $creatorContactsDataType = 'array';
  /**
   * The display name for this product as set by the seller.
   *
   * @var string
   */
  public $displayName;
  /**
   * If the creator has already signed off on the product, then the buyer can
   * finalize the deal by accepting the product as is. When copying to a
   * proposal, if any of the terms are changed, then auto_finalize is
   * automatically set to false.
   *
   * @var bool
   */
  public $hasCreatorSignedOff;
  /**
   * The unique ID for the product.
   *
   * @var string
   */
  public $productId;
  /**
   * The revision number of the product (auto-assigned by Marketplace).
   *
   * @var string
   */
  public $productRevision;
  /**
   * An ID which can be used by the Publisher Profile API to get more
   * information about the seller that created this product.
   *
   * @var string
   */
  public $publisherProfileId;
  protected $sellerType = Seller::class;
  protected $sellerDataType = '';
  /**
   * The syndication product associated with the deal.
   *
   * @var string
   */
  public $syndicationProduct;
  protected $targetingCriterionType = TargetingCriteria::class;
  protected $targetingCriterionDataType = 'array';
  protected $termsType = DealTerms::class;
  protected $termsDataType = '';
  /**
   * Time of last update.
   *
   * @var string
   */
  public $updateTime;
  /**
   * The web-property code for the seller. This needs to be copied as is when
   * adding a new deal to a proposal.
   *
   * @var string
   */
  public $webPropertyCode;

  /**
   * The proposed end time for the deal. The field will be truncated to the
   * order of seconds during serving.
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
   * Inventory availability dates. The start time will be truncated to seconds
   * during serving. Thus, a field specified as 3:23:34.456 (HH:mm:ss.SSS) will
   * be truncated to 3:23:34 when serving.
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
   * Creation time.
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
   * Optional contact information for the creator of this product.
   *
   * @param ContactInformation[] $creatorContacts
   */
  public function setCreatorContacts($creatorContacts)
  {
    $this->creatorContacts = $creatorContacts;
  }
  /**
   * @return ContactInformation[]
   */
  public function getCreatorContacts()
  {
    return $this->creatorContacts;
  }
  /**
   * The display name for this product as set by the seller.
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
   * If the creator has already signed off on the product, then the buyer can
   * finalize the deal by accepting the product as is. When copying to a
   * proposal, if any of the terms are changed, then auto_finalize is
   * automatically set to false.
   *
   * @param bool $hasCreatorSignedOff
   */
  public function setHasCreatorSignedOff($hasCreatorSignedOff)
  {
    $this->hasCreatorSignedOff = $hasCreatorSignedOff;
  }
  /**
   * @return bool
   */
  public function getHasCreatorSignedOff()
  {
    return $this->hasCreatorSignedOff;
  }
  /**
   * The unique ID for the product.
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
  /**
   * The revision number of the product (auto-assigned by Marketplace).
   *
   * @param string $productRevision
   */
  public function setProductRevision($productRevision)
  {
    $this->productRevision = $productRevision;
  }
  /**
   * @return string
   */
  public function getProductRevision()
  {
    return $this->productRevision;
  }
  /**
   * An ID which can be used by the Publisher Profile API to get more
   * information about the seller that created this product.
   *
   * @param string $publisherProfileId
   */
  public function setPublisherProfileId($publisherProfileId)
  {
    $this->publisherProfileId = $publisherProfileId;
  }
  /**
   * @return string
   */
  public function getPublisherProfileId()
  {
    return $this->publisherProfileId;
  }
  /**
   * Information about the seller that created this product.
   *
   * @param Seller $seller
   */
  public function setSeller(Seller $seller)
  {
    $this->seller = $seller;
  }
  /**
   * @return Seller
   */
  public function getSeller()
  {
    return $this->seller;
  }
  /**
   * The syndication product associated with the deal.
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
   * Targeting that is shared between the buyer and the seller. Each targeting
   * criterion has a specified key and for each key there is a list of inclusion
   * value or exclusion values.
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
   * The negotiable terms of the deal.
   *
   * @param DealTerms $terms
   */
  public function setTerms(DealTerms $terms)
  {
    $this->terms = $terms;
  }
  /**
   * @return DealTerms
   */
  public function getTerms()
  {
    return $this->terms;
  }
  /**
   * Time of last update.
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
   * The web-property code for the seller. This needs to be copied as is when
   * adding a new deal to a proposal.
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
class_alias(Product::class, 'Google_Service_AdExchangeBuyerII_Product');
