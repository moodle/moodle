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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2alphaMerchantCenterAccountLink extends \Google\Collection
{
  /**
   * Default value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Link is created and LRO is not complete.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * Link is active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Link creation failed.
   */
  public const STATE_FAILED = 'FAILED';
  protected $collection_key = 'feedFilters';
  /**
   * Required. The branch ID (e.g. 0/1/2) within the catalog that products from
   * merchant_center_account_id are streamed to. When updating this field, an
   * empty value will use the currently configured default branch. However,
   * changing the default branch later on won't change the linked branch here. A
   * single branch ID can only have one linked Merchant Center account ID.
   *
   * @var string
   */
  public $branchId;
  protected $feedFiltersType = GoogleCloudRetailV2alphaMerchantCenterAccountLinkMerchantCenterFeedFilter::class;
  protected $feedFiltersDataType = 'array';
  /**
   * The FeedLabel used to perform filtering. Note: this replaces
   * [region_id](https://developers.google.com/shopping-
   * content/reference/rest/v2.1/products#Product.FIELDS.feed_label). Example
   * value: `US`. Example value: `FeedLabel1`.
   *
   * @var string
   */
  public $feedLabel;
  /**
   * Output only. Immutable. MerchantCenterAccountLink identifier, which is the
   * final component of name. This field is auto generated and follows the
   * convention: `BranchId_MerchantCenterAccountId`. `projects/locations/global/
   * catalogs/default_catalog/merchantCenterAccountLinks/id_1`.
   *
   * @var string
   */
  public $id;
  /**
   * Language of the title/description and other string attributes. Use language
   * tags defined by [BCP 47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt). ISO
   * 639-1. This specifies the language of offers in Merchant Center that will
   * be accepted. If empty, no language filtering will be performed. Example
   * value: `en`.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Required. The linked [Merchant center account
   * id](https://developers.google.com/shopping-content/guides/accountstatuses).
   * The account must be a standalone account or a sub-account of a MCA.
   *
   * @var string
   */
  public $merchantCenterAccountId;
  /**
   * Output only. Immutable. Full resource name of the Merchant Center Account
   * Link, such as `projects/locations/global/catalogs/default_catalog/merchantC
   * enterAccountLinks/merchant_center_account_link`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Google Cloud project ID.
   *
   * @var string
   */
  public $projectId;
  /**
   * Optional. An optional arbitrary string that could be used as a tag for
   * tracking link source.
   *
   * @var string
   */
  public $source;
  /**
   * Output only. Represents the state of the link.
   *
   * @var string
   */
  public $state;

  /**
   * Required. The branch ID (e.g. 0/1/2) within the catalog that products from
   * merchant_center_account_id are streamed to. When updating this field, an
   * empty value will use the currently configured default branch. However,
   * changing the default branch later on won't change the linked branch here. A
   * single branch ID can only have one linked Merchant Center account ID.
   *
   * @param string $branchId
   */
  public function setBranchId($branchId)
  {
    $this->branchId = $branchId;
  }
  /**
   * @return string
   */
  public function getBranchId()
  {
    return $this->branchId;
  }
  /**
   * Criteria for the Merchant Center feeds to be ingested via the link. All
   * offers will be ingested if the list is empty. Otherwise the offers will be
   * ingested from selected feeds.
   *
   * @param GoogleCloudRetailV2alphaMerchantCenterAccountLinkMerchantCenterFeedFilter[] $feedFilters
   */
  public function setFeedFilters($feedFilters)
  {
    $this->feedFilters = $feedFilters;
  }
  /**
   * @return GoogleCloudRetailV2alphaMerchantCenterAccountLinkMerchantCenterFeedFilter[]
   */
  public function getFeedFilters()
  {
    return $this->feedFilters;
  }
  /**
   * The FeedLabel used to perform filtering. Note: this replaces
   * [region_id](https://developers.google.com/shopping-
   * content/reference/rest/v2.1/products#Product.FIELDS.feed_label). Example
   * value: `US`. Example value: `FeedLabel1`.
   *
   * @param string $feedLabel
   */
  public function setFeedLabel($feedLabel)
  {
    $this->feedLabel = $feedLabel;
  }
  /**
   * @return string
   */
  public function getFeedLabel()
  {
    return $this->feedLabel;
  }
  /**
   * Output only. Immutable. MerchantCenterAccountLink identifier, which is the
   * final component of name. This field is auto generated and follows the
   * convention: `BranchId_MerchantCenterAccountId`. `projects/locations/global/
   * catalogs/default_catalog/merchantCenterAccountLinks/id_1`.
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
   * Language of the title/description and other string attributes. Use language
   * tags defined by [BCP 47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt). ISO
   * 639-1. This specifies the language of offers in Merchant Center that will
   * be accepted. If empty, no language filtering will be performed. Example
   * value: `en`.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Required. The linked [Merchant center account
   * id](https://developers.google.com/shopping-content/guides/accountstatuses).
   * The account must be a standalone account or a sub-account of a MCA.
   *
   * @param string $merchantCenterAccountId
   */
  public function setMerchantCenterAccountId($merchantCenterAccountId)
  {
    $this->merchantCenterAccountId = $merchantCenterAccountId;
  }
  /**
   * @return string
   */
  public function getMerchantCenterAccountId()
  {
    return $this->merchantCenterAccountId;
  }
  /**
   * Output only. Immutable. Full resource name of the Merchant Center Account
   * Link, such as `projects/locations/global/catalogs/default_catalog/merchantC
   * enterAccountLinks/merchant_center_account_link`.
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
   * Output only. Google Cloud project ID.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Optional. An optional arbitrary string that could be used as a tag for
   * tracking link source.
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Output only. Represents the state of the link.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, ACTIVE, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2alphaMerchantCenterAccountLink::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2alphaMerchantCenterAccountLink');
