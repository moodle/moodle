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

class GoogleCloudRetailV2SearchRequest extends \Google\Collection
{
  /**
   * Default value. In this case both product search and faceted search will be
   * performed. Both SearchResponse.SearchResult and SearchResponse.Facet will
   * be returned.
   */
  public const SEARCH_MODE_SEARCH_MODE_UNSPECIFIED = 'SEARCH_MODE_UNSPECIFIED';
  /**
   * Only product search will be performed. The faceted search will be disabled.
   * Only SearchResponse.SearchResult will be returned. SearchResponse.Facet
   * will not be returned, even if SearchRequest.facet_specs or
   * SearchRequest.dynamic_facet_spec is set.
   */
  public const SEARCH_MODE_PRODUCT_SEARCH_ONLY = 'PRODUCT_SEARCH_ONLY';
  /**
   * Only faceted search will be performed. The product search will be disabled.
   * When in this mode, one or both of SearchRequest.facet_specs and
   * SearchRequest.dynamic_facet_spec should be set. Otherwise, an
   * INVALID_ARGUMENT error is returned. Only SearchResponse.Facet will be
   * returned. SearchResponse.SearchResult will not be returned.
   */
  public const SEARCH_MODE_FACETED_SEARCH_ONLY = 'FACETED_SEARCH_ONLY';
  protected $collection_key = 'variantRollupKeys';
  protected $boostSpecType = GoogleCloudRetailV2SearchRequestBoostSpec::class;
  protected $boostSpecDataType = '';
  /**
   * The branch resource name, such as
   * `projects/locations/global/catalogs/default_catalog/branches/0`. Use
   * "default_branch" as the branch ID or leave this field empty, to search
   * products under the default branch.
   *
   * @var string
   */
  public $branch;
  /**
   * The default filter that is applied when a user performs a search without
   * checking any filters on the search page. The filter applied to every search
   * request when quality improvement such as query expansion is needed. In the
   * case a query does not have a sufficient amount of results this filter will
   * be used to determine whether or not to enable the query expansion flow. The
   * original filter will still be used for the query expanded search. This
   * field is strongly recommended to achieve high search quality. For more
   * information about filter syntax, see SearchRequest.filter.
   *
   * @var string
   */
  public $canonicalFilter;
  protected $conversationalSearchSpecType = GoogleCloudRetailV2SearchRequestConversationalSearchSpec::class;
  protected $conversationalSearchSpecDataType = '';
  protected $dynamicFacetSpecType = GoogleCloudRetailV2SearchRequestDynamicFacetSpec::class;
  protected $dynamicFacetSpecDataType = '';
  /**
   * The entity for customers that may run multiple different entities, domains,
   * sites or regions, for example, `Google US`, `Google Ads`, `Waymo`,
   * `google.com`, `youtube.com`, etc. If this is set, it should be exactly
   * matched with UserEvent.entity to get search results boosted by entity.
   *
   * @var string
   */
  public $entity;
  protected $facetSpecsType = GoogleCloudRetailV2SearchRequestFacetSpec::class;
  protected $facetSpecsDataType = 'array';
  /**
   * The filter syntax consists of an expression language for constructing a
   * predicate from one or more fields of the products being filtered. Filter
   * expression is case-sensitive. For more information, see
   * [Filter](https://cloud.google.com/retail/docs/filter-and-order#filter). If
   * this field is unrecognizable, an INVALID_ARGUMENT is returned.
   *
   * @var string
   */
  public $filter;
  /**
   * The labels applied to a resource must meet the following requirements: *
   * Each resource can have multiple labels, up to a maximum of 64. * Each label
   * must be a key-value pair. * Keys have a minimum length of 1 character and a
   * maximum length of 63 characters and cannot be empty. Values can be empty
   * and have a maximum length of 63 characters. * Keys and values can contain
   * only lowercase letters, numeric characters, underscores, and dashes. All
   * characters must use UTF-8 encoding, and international characters are
   * allowed. * The key portion of a label must be unique. However, you can use
   * the same key with multiple resources. * Keys must start with a lowercase
   * letter or international character. For more information, see [Requirements
   * for labels](https://cloud.google.com/resource-manager/docs/creating-
   * managing-labels#requirements) in the Resource Manager documentation.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. The BCP-47 language code, such as "en-US" or "sr-Latn"
   * [list](https://www.unicode.org/cldr/charts/46/summary/root.html). For more
   * information, see [Standardized codes](https://google.aip.dev/143). This
   * field helps to better interpret the query. If a value isn't specified, the
   * query language code is automatically detected, which may not be accurate.
   *
   * @var string
   */
  public $languageCode;
  /**
   * A 0-indexed integer that specifies the current offset (that is, starting
   * result location, amongst the Products deemed by the API as relevant) in
   * search results. This field is only considered if page_token is unset. If
   * this field is negative, an INVALID_ARGUMENT is returned.
   *
   * @var int
   */
  public $offset;
  /**
   * The order in which products are returned. Products can be ordered by a
   * field in an Product object. Leave it unset if ordered by relevance. OrderBy
   * expression is case-sensitive. For more information, see
   * [Order](https://cloud.google.com/retail/docs/filter-and-order#order). If
   * this field is unrecognizable, an INVALID_ARGUMENT is returned.
   *
   * @var string
   */
  public $orderBy;
  /**
   * The categories associated with a category page. Must be set for category
   * navigation queries to achieve good search quality. The format should be the
   * same as UserEvent.page_categories; To represent full path of category, use
   * '>' sign to separate different hierarchies. If '>' is part of the category
   * name, replace it with other character(s). Category pages include special
   * pages such as sales or promotions. For instance, a special sale page may
   * have the category hierarchy: "pageCategories" : ["Sales > 2017 Black Friday
   * Deals"].
   *
   * @var string[]
   */
  public $pageCategories;
  /**
   * Maximum number of Products to return. If unspecified, defaults to a
   * reasonable value. The maximum allowed value is 120. Values above 120 will
   * be coerced to 120. If this field is negative, an INVALID_ARGUMENT is
   * returned.
   *
   * @var int
   */
  public $pageSize;
  /**
   * A page token SearchResponse.next_page_token, received from a previous
   * SearchService.Search call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to SearchService.Search must
   * match the call that provided the page token. Otherwise, an INVALID_ARGUMENT
   * error is returned.
   *
   * @var string
   */
  public $pageToken;
  protected $personalizationSpecType = GoogleCloudRetailV2SearchRequestPersonalizationSpec::class;
  protected $personalizationSpecDataType = '';
  /**
   * Optional. An id corresponding to a place, such as a store id or region id.
   * When specified, we use the price from the local inventory with the matching
   * product's LocalInventory.place_id for revenue optimization.
   *
   * @var string
   */
  public $placeId;
  /**
   * Raw search query. If this field is empty, the request is considered a
   * category browsing request and returned results are based on filter and
   * page_categories.
   *
   * @var string
   */
  public $query;
  protected $queryExpansionSpecType = GoogleCloudRetailV2SearchRequestQueryExpansionSpec::class;
  protected $queryExpansionSpecDataType = '';
  /**
   * Optional. The Unicode country/region code (CLDR) of a location, such as
   * "US" and "419" [list](https://www.unicode.org/cldr/charts/46/supplemental/t
   * erritory_information.html). For more information, see [Standardized
   * codes](https://google.aip.dev/143). If set, then results will be boosted
   * based on the region_code provided.
   *
   * @var string
   */
  public $regionCode;
  /**
   * The search mode of the search request. If not specified, a single search
   * request triggers both product search and faceted search.
   *
   * @var string
   */
  public $searchMode;
  protected $spellCorrectionSpecType = GoogleCloudRetailV2SearchRequestSpellCorrectionSpec::class;
  protected $spellCorrectionSpecDataType = '';
  protected $tileNavigationSpecType = GoogleCloudRetailV2SearchRequestTileNavigationSpec::class;
  protected $tileNavigationSpecDataType = '';
  protected $userAttributesType = GoogleCloudRetailV2StringList::class;
  protected $userAttributesDataType = 'map';
  protected $userInfoType = GoogleCloudRetailV2UserInfo::class;
  protected $userInfoDataType = '';
  /**
   * The keys to fetch and rollup the matching variant Products attributes,
   * FulfillmentInfo or LocalInventorys attributes. The attributes from all the
   * matching variant Products or LocalInventorys are merged and de-duplicated.
   * Notice that rollup attributes will lead to extra query latency. Maximum
   * number of keys is 30. For FulfillmentInfo, a fulfillment type and a
   * fulfillment ID must be provided in the format of
   * "fulfillmentType.fulfillmentId". E.g., in "pickupInStore.store123",
   * "pickupInStore" is fulfillment type and "store123" is the store ID.
   * Supported keys are: * colorFamilies * price * originalPrice * discount *
   * variantId * inventory(place_id,price) * inventory(place_id,original_price)
   * * inventory(place_id,attributes.key), where key is any key in the
   * Product.local_inventories.attributes map. * attributes.key, where key is
   * any key in the Product.attributes map. * pickupInStore.id, where id is any
   * FulfillmentInfo.place_ids for FulfillmentInfo.type "pickup-in-store". *
   * shipToStore.id, where id is any FulfillmentInfo.place_ids for
   * FulfillmentInfo.type "ship-to-store". * sameDayDelivery.id, where id is any
   * FulfillmentInfo.place_ids for FulfillmentInfo.type "same-day-delivery". *
   * nextDayDelivery.id, where id is any FulfillmentInfo.place_ids for
   * FulfillmentInfo.type "next-day-delivery". * customFulfillment1.id, where id
   * is any FulfillmentInfo.place_ids for FulfillmentInfo.type "custom-type-1".
   * * customFulfillment2.id, where id is any FulfillmentInfo.place_ids for
   * FulfillmentInfo.type "custom-type-2". * customFulfillment3.id, where id is
   * any FulfillmentInfo.place_ids for FulfillmentInfo.type "custom-type-3". *
   * customFulfillment4.id, where id is any FulfillmentInfo.place_ids for
   * FulfillmentInfo.type "custom-type-4". * customFulfillment5.id, where id is
   * any FulfillmentInfo.place_ids for FulfillmentInfo.type "custom-type-5". If
   * this field is set to an invalid value other than these, an INVALID_ARGUMENT
   * error is returned.
   *
   * @var string[]
   */
  public $variantRollupKeys;
  /**
   * Required. A unique identifier for tracking visitors. For example, this
   * could be implemented with an HTTP cookie, which should be able to uniquely
   * identify a visitor on a single device. This unique identifier should not
   * change if the visitor logs in or out of the website. This should be the
   * same identifier as UserEvent.visitor_id. The field must be a UTF-8 encoded
   * string with a length limit of 128 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned.
   *
   * @var string
   */
  public $visitorId;

  /**
   * Boost specification to boost certain products. For more information, see
   * [Boost results](https://cloud.google.com/retail/docs/boosting). Notice that
   * if both ServingConfig.boost_control_ids and SearchRequest.boost_spec are
   * set, the boost conditions from both places are evaluated. If a search
   * request matches multiple boost conditions, the final boost score is equal
   * to the sum of the boost scores from all matched boost conditions.
   *
   * @param GoogleCloudRetailV2SearchRequestBoostSpec $boostSpec
   */
  public function setBoostSpec(GoogleCloudRetailV2SearchRequestBoostSpec $boostSpec)
  {
    $this->boostSpec = $boostSpec;
  }
  /**
   * @return GoogleCloudRetailV2SearchRequestBoostSpec
   */
  public function getBoostSpec()
  {
    return $this->boostSpec;
  }
  /**
   * The branch resource name, such as
   * `projects/locations/global/catalogs/default_catalog/branches/0`. Use
   * "default_branch" as the branch ID or leave this field empty, to search
   * products under the default branch.
   *
   * @param string $branch
   */
  public function setBranch($branch)
  {
    $this->branch = $branch;
  }
  /**
   * @return string
   */
  public function getBranch()
  {
    return $this->branch;
  }
  /**
   * The default filter that is applied when a user performs a search without
   * checking any filters on the search page. The filter applied to every search
   * request when quality improvement such as query expansion is needed. In the
   * case a query does not have a sufficient amount of results this filter will
   * be used to determine whether or not to enable the query expansion flow. The
   * original filter will still be used for the query expanded search. This
   * field is strongly recommended to achieve high search quality. For more
   * information about filter syntax, see SearchRequest.filter.
   *
   * @param string $canonicalFilter
   */
  public function setCanonicalFilter($canonicalFilter)
  {
    $this->canonicalFilter = $canonicalFilter;
  }
  /**
   * @return string
   */
  public function getCanonicalFilter()
  {
    return $this->canonicalFilter;
  }
  /**
   * Optional. This field specifies all conversational related parameters
   * addition to traditional retail search.
   *
   * @param GoogleCloudRetailV2SearchRequestConversationalSearchSpec $conversationalSearchSpec
   */
  public function setConversationalSearchSpec(GoogleCloudRetailV2SearchRequestConversationalSearchSpec $conversationalSearchSpec)
  {
    $this->conversationalSearchSpec = $conversationalSearchSpec;
  }
  /**
   * @return GoogleCloudRetailV2SearchRequestConversationalSearchSpec
   */
  public function getConversationalSearchSpec()
  {
    return $this->conversationalSearchSpec;
  }
  /**
   * Deprecated. Refer to https://cloud.google.com/retail/docs/configs#dynamic
   * to enable dynamic facets. Do not set this field. The specification for
   * dynamically generated facets. Notice that only textual facets can be
   * dynamically generated.
   *
   * @deprecated
   * @param GoogleCloudRetailV2SearchRequestDynamicFacetSpec $dynamicFacetSpec
   */
  public function setDynamicFacetSpec(GoogleCloudRetailV2SearchRequestDynamicFacetSpec $dynamicFacetSpec)
  {
    $this->dynamicFacetSpec = $dynamicFacetSpec;
  }
  /**
   * @deprecated
   * @return GoogleCloudRetailV2SearchRequestDynamicFacetSpec
   */
  public function getDynamicFacetSpec()
  {
    return $this->dynamicFacetSpec;
  }
  /**
   * The entity for customers that may run multiple different entities, domains,
   * sites or regions, for example, `Google US`, `Google Ads`, `Waymo`,
   * `google.com`, `youtube.com`, etc. If this is set, it should be exactly
   * matched with UserEvent.entity to get search results boosted by entity.
   *
   * @param string $entity
   */
  public function setEntity($entity)
  {
    $this->entity = $entity;
  }
  /**
   * @return string
   */
  public function getEntity()
  {
    return $this->entity;
  }
  /**
   * Facet specifications for faceted search. If empty, no facets are returned.
   * A maximum of 200 values are allowed. Otherwise, an INVALID_ARGUMENT error
   * is returned.
   *
   * @param GoogleCloudRetailV2SearchRequestFacetSpec[] $facetSpecs
   */
  public function setFacetSpecs($facetSpecs)
  {
    $this->facetSpecs = $facetSpecs;
  }
  /**
   * @return GoogleCloudRetailV2SearchRequestFacetSpec[]
   */
  public function getFacetSpecs()
  {
    return $this->facetSpecs;
  }
  /**
   * The filter syntax consists of an expression language for constructing a
   * predicate from one or more fields of the products being filtered. Filter
   * expression is case-sensitive. For more information, see
   * [Filter](https://cloud.google.com/retail/docs/filter-and-order#filter). If
   * this field is unrecognizable, an INVALID_ARGUMENT is returned.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * The labels applied to a resource must meet the following requirements: *
   * Each resource can have multiple labels, up to a maximum of 64. * Each label
   * must be a key-value pair. * Keys have a minimum length of 1 character and a
   * maximum length of 63 characters and cannot be empty. Values can be empty
   * and have a maximum length of 63 characters. * Keys and values can contain
   * only lowercase letters, numeric characters, underscores, and dashes. All
   * characters must use UTF-8 encoding, and international characters are
   * allowed. * The key portion of a label must be unique. However, you can use
   * the same key with multiple resources. * Keys must start with a lowercase
   * letter or international character. For more information, see [Requirements
   * for labels](https://cloud.google.com/resource-manager/docs/creating-
   * managing-labels#requirements) in the Resource Manager documentation.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. The BCP-47 language code, such as "en-US" or "sr-Latn"
   * [list](https://www.unicode.org/cldr/charts/46/summary/root.html). For more
   * information, see [Standardized codes](https://google.aip.dev/143). This
   * field helps to better interpret the query. If a value isn't specified, the
   * query language code is automatically detected, which may not be accurate.
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
   * A 0-indexed integer that specifies the current offset (that is, starting
   * result location, amongst the Products deemed by the API as relevant) in
   * search results. This field is only considered if page_token is unset. If
   * this field is negative, an INVALID_ARGUMENT is returned.
   *
   * @param int $offset
   */
  public function setOffset($offset)
  {
    $this->offset = $offset;
  }
  /**
   * @return int
   */
  public function getOffset()
  {
    return $this->offset;
  }
  /**
   * The order in which products are returned. Products can be ordered by a
   * field in an Product object. Leave it unset if ordered by relevance. OrderBy
   * expression is case-sensitive. For more information, see
   * [Order](https://cloud.google.com/retail/docs/filter-and-order#order). If
   * this field is unrecognizable, an INVALID_ARGUMENT is returned.
   *
   * @param string $orderBy
   */
  public function setOrderBy($orderBy)
  {
    $this->orderBy = $orderBy;
  }
  /**
   * @return string
   */
  public function getOrderBy()
  {
    return $this->orderBy;
  }
  /**
   * The categories associated with a category page. Must be set for category
   * navigation queries to achieve good search quality. The format should be the
   * same as UserEvent.page_categories; To represent full path of category, use
   * '>' sign to separate different hierarchies. If '>' is part of the category
   * name, replace it with other character(s). Category pages include special
   * pages such as sales or promotions. For instance, a special sale page may
   * have the category hierarchy: "pageCategories" : ["Sales > 2017 Black Friday
   * Deals"].
   *
   * @param string[] $pageCategories
   */
  public function setPageCategories($pageCategories)
  {
    $this->pageCategories = $pageCategories;
  }
  /**
   * @return string[]
   */
  public function getPageCategories()
  {
    return $this->pageCategories;
  }
  /**
   * Maximum number of Products to return. If unspecified, defaults to a
   * reasonable value. The maximum allowed value is 120. Values above 120 will
   * be coerced to 120. If this field is negative, an INVALID_ARGUMENT is
   * returned.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * A page token SearchResponse.next_page_token, received from a previous
   * SearchService.Search call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to SearchService.Search must
   * match the call that provided the page token. Otherwise, an INVALID_ARGUMENT
   * error is returned.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * The specification for personalization. Notice that if both
   * ServingConfig.personalization_spec and SearchRequest.personalization_spec
   * are set. SearchRequest.personalization_spec will override
   * ServingConfig.personalization_spec.
   *
   * @param GoogleCloudRetailV2SearchRequestPersonalizationSpec $personalizationSpec
   */
  public function setPersonalizationSpec(GoogleCloudRetailV2SearchRequestPersonalizationSpec $personalizationSpec)
  {
    $this->personalizationSpec = $personalizationSpec;
  }
  /**
   * @return GoogleCloudRetailV2SearchRequestPersonalizationSpec
   */
  public function getPersonalizationSpec()
  {
    return $this->personalizationSpec;
  }
  /**
   * Optional. An id corresponding to a place, such as a store id or region id.
   * When specified, we use the price from the local inventory with the matching
   * product's LocalInventory.place_id for revenue optimization.
   *
   * @param string $placeId
   */
  public function setPlaceId($placeId)
  {
    $this->placeId = $placeId;
  }
  /**
   * @return string
   */
  public function getPlaceId()
  {
    return $this->placeId;
  }
  /**
   * Raw search query. If this field is empty, the request is considered a
   * category browsing request and returned results are based on filter and
   * page_categories.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * The query expansion specification that specifies the conditions under which
   * query expansion occurs. For more information, see [Query
   * expansion](https://cloud.google.com/retail/docs/result-
   * size#query_expansion).
   *
   * @param GoogleCloudRetailV2SearchRequestQueryExpansionSpec $queryExpansionSpec
   */
  public function setQueryExpansionSpec(GoogleCloudRetailV2SearchRequestQueryExpansionSpec $queryExpansionSpec)
  {
    $this->queryExpansionSpec = $queryExpansionSpec;
  }
  /**
   * @return GoogleCloudRetailV2SearchRequestQueryExpansionSpec
   */
  public function getQueryExpansionSpec()
  {
    return $this->queryExpansionSpec;
  }
  /**
   * Optional. The Unicode country/region code (CLDR) of a location, such as
   * "US" and "419" [list](https://www.unicode.org/cldr/charts/46/supplemental/t
   * erritory_information.html). For more information, see [Standardized
   * codes](https://google.aip.dev/143). If set, then results will be boosted
   * based on the region_code provided.
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
  /**
   * The search mode of the search request. If not specified, a single search
   * request triggers both product search and faceted search.
   *
   * Accepted values: SEARCH_MODE_UNSPECIFIED, PRODUCT_SEARCH_ONLY,
   * FACETED_SEARCH_ONLY
   *
   * @param self::SEARCH_MODE_* $searchMode
   */
  public function setSearchMode($searchMode)
  {
    $this->searchMode = $searchMode;
  }
  /**
   * @return self::SEARCH_MODE_*
   */
  public function getSearchMode()
  {
    return $this->searchMode;
  }
  /**
   * The spell correction specification that specifies the mode under which
   * spell correction will take effect.
   *
   * @param GoogleCloudRetailV2SearchRequestSpellCorrectionSpec $spellCorrectionSpec
   */
  public function setSpellCorrectionSpec(GoogleCloudRetailV2SearchRequestSpellCorrectionSpec $spellCorrectionSpec)
  {
    $this->spellCorrectionSpec = $spellCorrectionSpec;
  }
  /**
   * @return GoogleCloudRetailV2SearchRequestSpellCorrectionSpec
   */
  public function getSpellCorrectionSpec()
  {
    return $this->spellCorrectionSpec;
  }
  /**
   * Optional. This field specifies tile navigation related parameters.
   *
   * @param GoogleCloudRetailV2SearchRequestTileNavigationSpec $tileNavigationSpec
   */
  public function setTileNavigationSpec(GoogleCloudRetailV2SearchRequestTileNavigationSpec $tileNavigationSpec)
  {
    $this->tileNavigationSpec = $tileNavigationSpec;
  }
  /**
   * @return GoogleCloudRetailV2SearchRequestTileNavigationSpec
   */
  public function getTileNavigationSpec()
  {
    return $this->tileNavigationSpec;
  }
  /**
   * Optional. The user attributes that could be used for personalization of
   * search results. * Populate at most 100 key-value pairs per query. * Only
   * supports string keys and repeated string values. * Duplicate keys are not
   * allowed within a single query. Example: user_attributes: [ { key: "pets"
   * value { values: "dog" values: "cat" } }, { key: "state" value { values:
   * "CA" } } ]
   *
   * @param GoogleCloudRetailV2StringList[] $userAttributes
   */
  public function setUserAttributes($userAttributes)
  {
    $this->userAttributes = $userAttributes;
  }
  /**
   * @return GoogleCloudRetailV2StringList[]
   */
  public function getUserAttributes()
  {
    return $this->userAttributes;
  }
  /**
   * User information.
   *
   * @param GoogleCloudRetailV2UserInfo $userInfo
   */
  public function setUserInfo(GoogleCloudRetailV2UserInfo $userInfo)
  {
    $this->userInfo = $userInfo;
  }
  /**
   * @return GoogleCloudRetailV2UserInfo
   */
  public function getUserInfo()
  {
    return $this->userInfo;
  }
  /**
   * The keys to fetch and rollup the matching variant Products attributes,
   * FulfillmentInfo or LocalInventorys attributes. The attributes from all the
   * matching variant Products or LocalInventorys are merged and de-duplicated.
   * Notice that rollup attributes will lead to extra query latency. Maximum
   * number of keys is 30. For FulfillmentInfo, a fulfillment type and a
   * fulfillment ID must be provided in the format of
   * "fulfillmentType.fulfillmentId". E.g., in "pickupInStore.store123",
   * "pickupInStore" is fulfillment type and "store123" is the store ID.
   * Supported keys are: * colorFamilies * price * originalPrice * discount *
   * variantId * inventory(place_id,price) * inventory(place_id,original_price)
   * * inventory(place_id,attributes.key), where key is any key in the
   * Product.local_inventories.attributes map. * attributes.key, where key is
   * any key in the Product.attributes map. * pickupInStore.id, where id is any
   * FulfillmentInfo.place_ids for FulfillmentInfo.type "pickup-in-store". *
   * shipToStore.id, where id is any FulfillmentInfo.place_ids for
   * FulfillmentInfo.type "ship-to-store". * sameDayDelivery.id, where id is any
   * FulfillmentInfo.place_ids for FulfillmentInfo.type "same-day-delivery". *
   * nextDayDelivery.id, where id is any FulfillmentInfo.place_ids for
   * FulfillmentInfo.type "next-day-delivery". * customFulfillment1.id, where id
   * is any FulfillmentInfo.place_ids for FulfillmentInfo.type "custom-type-1".
   * * customFulfillment2.id, where id is any FulfillmentInfo.place_ids for
   * FulfillmentInfo.type "custom-type-2". * customFulfillment3.id, where id is
   * any FulfillmentInfo.place_ids for FulfillmentInfo.type "custom-type-3". *
   * customFulfillment4.id, where id is any FulfillmentInfo.place_ids for
   * FulfillmentInfo.type "custom-type-4". * customFulfillment5.id, where id is
   * any FulfillmentInfo.place_ids for FulfillmentInfo.type "custom-type-5". If
   * this field is set to an invalid value other than these, an INVALID_ARGUMENT
   * error is returned.
   *
   * @param string[] $variantRollupKeys
   */
  public function setVariantRollupKeys($variantRollupKeys)
  {
    $this->variantRollupKeys = $variantRollupKeys;
  }
  /**
   * @return string[]
   */
  public function getVariantRollupKeys()
  {
    return $this->variantRollupKeys;
  }
  /**
   * Required. A unique identifier for tracking visitors. For example, this
   * could be implemented with an HTTP cookie, which should be able to uniquely
   * identify a visitor on a single device. This unique identifier should not
   * change if the visitor logs in or out of the website. This should be the
   * same identifier as UserEvent.visitor_id. The field must be a UTF-8 encoded
   * string with a length limit of 128 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned.
   *
   * @param string $visitorId
   */
  public function setVisitorId($visitorId)
  {
    $this->visitorId = $visitorId;
  }
  /**
   * @return string
   */
  public function getVisitorId()
  {
    return $this->visitorId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2SearchRequest::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2SearchRequest');
