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

class GoogleCloudRetailV2UserEvent extends \Google\Collection
{
  protected $collection_key = 'productDetails';
  protected $attributesType = GoogleCloudRetailV2CustomAttribute::class;
  protected $attributesDataType = 'map';
  /**
   * Highly recommended for user events that are the result of
   * PredictionService.Predict. This field enables accurate attribution of
   * recommendation model performance. The value must be a valid
   * PredictResponse.attribution_token for user events that are the result of
   * PredictionService.Predict. The value must be a valid
   * SearchResponse.attribution_token for user events that are the result of
   * SearchService.Search. This token enables us to accurately attribute page
   * view or purchase back to the event and the particular predict response
   * containing this clicked/purchased product. If user clicks on product K in
   * the recommendation results, pass PredictResponse.attribution_token as a URL
   * parameter to product K's page. When recording events on product K's page,
   * log the PredictResponse.attribution_token to this field.
   *
   * @var string
   */
  public $attributionToken;
  /**
   * The ID or name of the associated shopping cart. This ID is used to
   * associate multiple items added or present in the cart before purchase. This
   * can only be set for `add-to-cart`, `purchase-complete`, or `shopping-cart-
   * page-view` events.
   *
   * @var string
   */
  public $cartId;
  protected $completionDetailType = GoogleCloudRetailV2CompletionDetail::class;
  protected $completionDetailDataType = '';
  /**
   * The entity for customers that may run multiple different entities, domains,
   * sites or regions, for example, `Google US`, `Google Ads`, `Waymo`,
   * `google.com`, `youtube.com`, etc. We recommend that you set this field to
   * get better per-entity search, completion, and prediction results.
   *
   * @var string
   */
  public $entity;
  /**
   * Only required for UserEventService.ImportUserEvents method. Timestamp of
   * when the user event happened.
   *
   * @var string
   */
  public $eventTime;
  /**
   * Required. User event type. Allowed values are: * `add-to-cart`: Products
   * being added to cart. * `remove-from-cart`: Products being removed from
   * cart. * `category-page-view`: Special pages such as sale or promotion pages
   * viewed. * `detail-page-view`: Products detail page viewed. * `home-page-
   * view`: Homepage viewed. * `purchase-complete`: User finishing a purchase. *
   * `search`: Product search. * `shopping-cart-page-view`: User viewing a
   * shopping cart.
   *
   * @var string
   */
  public $eventType;
  /**
   * A list of identifiers for the independent experiment groups this user event
   * belongs to. This is used to distinguish between user events associated with
   * different experiment setups (e.g. using Retail API, using different
   * recommendation models).
   *
   * @var string[]
   */
  public $experimentIds;
  /**
   * The filter syntax consists of an expression language for constructing a
   * predicate from one or more fields of the products being filtered. See
   * SearchRequest.filter for definition and syntax. The value must be a UTF-8
   * encoded string with a length limit of 1,000 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned.
   *
   * @var string
   */
  public $filter;
  /**
   * An integer that specifies the current offset for pagination (the 0-indexed
   * starting location, amongst the products deemed by the API as relevant). See
   * SearchRequest.offset for definition. If this field is negative, an
   * INVALID_ARGUMENT is returned. This can only be set for `search` events.
   * Other event types should not set this field. Otherwise, an INVALID_ARGUMENT
   * error is returned.
   *
   * @var int
   */
  public $offset;
  /**
   * The order in which products are returned. See SearchRequest.order_by for
   * definition and syntax. The value must be a UTF-8 encoded string with a
   * length limit of 1,000 characters. Otherwise, an INVALID_ARGUMENT error is
   * returned. This can only be set for `search` events. Other event types
   * should not set this field. Otherwise, an INVALID_ARGUMENT error is
   * returned.
   *
   * @var string
   */
  public $orderBy;
  /**
   * The categories associated with a category page. To represent full path of
   * category, use '>' sign to separate different hierarchies. If '>' is part of
   * the category name, replace it with other character(s). Category pages
   * include special pages such as sales or promotions. For instance, a special
   * sale page may have the category hierarchy: "pageCategories" : ["Sales >
   * 2017 Black Friday Deals"]. Required for `category-page-view` events. At
   * least one of search_query or page_categories is required for `search`
   * events. Other event types should not set this field. Otherwise, an
   * INVALID_ARGUMENT error is returned.
   *
   * @var string[]
   */
  public $pageCategories;
  /**
   * A unique ID of a web page view. This should be kept the same for all user
   * events triggered from the same pageview. For example, an item detail page
   * view could trigger multiple events as the user is browsing the page. The
   * `pageViewId` property should be kept the same for all these events so that
   * they can be grouped together properly. When using the client side event
   * reporting with JavaScript pixel and Google Tag Manager, this value is
   * filled in automatically.
   *
   * @var string
   */
  public $pageViewId;
  protected $panelsType = GoogleCloudRetailV2PanelInfo::class;
  protected $panelsDataType = 'array';
  protected $productDetailsType = GoogleCloudRetailV2ProductDetail::class;
  protected $productDetailsDataType = 'array';
  protected $purchaseTransactionType = GoogleCloudRetailV2PurchaseTransaction::class;
  protected $purchaseTransactionDataType = '';
  /**
   * The referrer URL of the current page. When using the client side event
   * reporting with JavaScript pixel and Google Tag Manager, this value is
   * filled in automatically.
   *
   * @var string
   */
  public $referrerUri;
  /**
   * The user's search query. See SearchRequest.query for definition. The value
   * must be a UTF-8 encoded string with a length limit of 5,000 characters.
   * Otherwise, an INVALID_ARGUMENT error is returned. At least one of
   * search_query or page_categories is required for `search` events. Other
   * event types should not set this field. Otherwise, an INVALID_ARGUMENT error
   * is returned.
   *
   * @var string
   */
  public $searchQuery;
  /**
   * A unique identifier for tracking a visitor session with a length limit of
   * 128 bytes. A session is an aggregation of an end user behavior in a time
   * span. A general guideline to populate the session_id: 1. If user has no
   * activity for 30 min, a new session_id should be assigned. 2. The session_id
   * should be unique across users, suggest use uuid or add visitor_id as
   * prefix.
   *
   * @var string
   */
  public $sessionId;
  /**
   * Complete URL (window.location.href) of the user's current page. When using
   * the client side event reporting with JavaScript pixel and Google Tag
   * Manager, this value is filled in automatically. Maximum length 5,000
   * characters.
   *
   * @var string
   */
  public $uri;
  protected $userInfoType = GoogleCloudRetailV2UserInfo::class;
  protected $userInfoDataType = '';
  /**
   * Required. A unique identifier for tracking visitors. For example, this
   * could be implemented with an HTTP cookie, which should be able to uniquely
   * identify a visitor on a single device. This unique identifier should not
   * change if the visitor log in/out of the website. Don't set the field to the
   * same fixed ID for different users. This mixes the event history of those
   * users together, which results in degraded model quality. The field must be
   * a UTF-8 encoded string with a length limit of 128 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned. The field should not contain PII or
   * user-data. We recommend to use Google Analytics [Client ID](https://develop
   * ers.google.com/analytics/devguides/collection/analyticsjs/field-
   * reference#clientId) for this field.
   *
   * @var string
   */
  public $visitorId;

  /**
   * Extra user event features to include in the recommendation model. If you
   * provide custom attributes for ingested user events, also include them in
   * the user events that you associate with prediction requests. Custom
   * attribute formatting must be consistent between imported events and events
   * provided with prediction requests. This lets the Retail API use those
   * custom attributes when training models and serving predictions, which helps
   * improve recommendation quality. This field needs to pass all below
   * criteria, otherwise an INVALID_ARGUMENT error is returned: * The key must
   * be a UTF-8 encoded string with a length limit of 5,000 characters. * For
   * text attributes, at most 400 values are allowed. Empty values are not
   * allowed. Each value must be a UTF-8 encoded string with a length limit of
   * 256 characters. * For number attributes, at most 400 values are allowed.
   * For product recommendations, an example of extra user information is
   * traffic_channel, which is how a user arrives at the site. Users can arrive
   * at the site by coming to the site directly, coming through Google search,
   * or in other ways.
   *
   * @param GoogleCloudRetailV2CustomAttribute[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return GoogleCloudRetailV2CustomAttribute[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Highly recommended for user events that are the result of
   * PredictionService.Predict. This field enables accurate attribution of
   * recommendation model performance. The value must be a valid
   * PredictResponse.attribution_token for user events that are the result of
   * PredictionService.Predict. The value must be a valid
   * SearchResponse.attribution_token for user events that are the result of
   * SearchService.Search. This token enables us to accurately attribute page
   * view or purchase back to the event and the particular predict response
   * containing this clicked/purchased product. If user clicks on product K in
   * the recommendation results, pass PredictResponse.attribution_token as a URL
   * parameter to product K's page. When recording events on product K's page,
   * log the PredictResponse.attribution_token to this field.
   *
   * @param string $attributionToken
   */
  public function setAttributionToken($attributionToken)
  {
    $this->attributionToken = $attributionToken;
  }
  /**
   * @return string
   */
  public function getAttributionToken()
  {
    return $this->attributionToken;
  }
  /**
   * The ID or name of the associated shopping cart. This ID is used to
   * associate multiple items added or present in the cart before purchase. This
   * can only be set for `add-to-cart`, `purchase-complete`, or `shopping-cart-
   * page-view` events.
   *
   * @param string $cartId
   */
  public function setCartId($cartId)
  {
    $this->cartId = $cartId;
  }
  /**
   * @return string
   */
  public function getCartId()
  {
    return $this->cartId;
  }
  /**
   * The main auto-completion details related to the event. This field should be
   * set for `search` event when autocomplete function is enabled and the user
   * clicks a suggestion for search.
   *
   * @param GoogleCloudRetailV2CompletionDetail $completionDetail
   */
  public function setCompletionDetail(GoogleCloudRetailV2CompletionDetail $completionDetail)
  {
    $this->completionDetail = $completionDetail;
  }
  /**
   * @return GoogleCloudRetailV2CompletionDetail
   */
  public function getCompletionDetail()
  {
    return $this->completionDetail;
  }
  /**
   * The entity for customers that may run multiple different entities, domains,
   * sites or regions, for example, `Google US`, `Google Ads`, `Waymo`,
   * `google.com`, `youtube.com`, etc. We recommend that you set this field to
   * get better per-entity search, completion, and prediction results.
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
   * Only required for UserEventService.ImportUserEvents method. Timestamp of
   * when the user event happened.
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * Required. User event type. Allowed values are: * `add-to-cart`: Products
   * being added to cart. * `remove-from-cart`: Products being removed from
   * cart. * `category-page-view`: Special pages such as sale or promotion pages
   * viewed. * `detail-page-view`: Products detail page viewed. * `home-page-
   * view`: Homepage viewed. * `purchase-complete`: User finishing a purchase. *
   * `search`: Product search. * `shopping-cart-page-view`: User viewing a
   * shopping cart.
   *
   * @param string $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return string
   */
  public function getEventType()
  {
    return $this->eventType;
  }
  /**
   * A list of identifiers for the independent experiment groups this user event
   * belongs to. This is used to distinguish between user events associated with
   * different experiment setups (e.g. using Retail API, using different
   * recommendation models).
   *
   * @param string[] $experimentIds
   */
  public function setExperimentIds($experimentIds)
  {
    $this->experimentIds = $experimentIds;
  }
  /**
   * @return string[]
   */
  public function getExperimentIds()
  {
    return $this->experimentIds;
  }
  /**
   * The filter syntax consists of an expression language for constructing a
   * predicate from one or more fields of the products being filtered. See
   * SearchRequest.filter for definition and syntax. The value must be a UTF-8
   * encoded string with a length limit of 1,000 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned.
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
   * An integer that specifies the current offset for pagination (the 0-indexed
   * starting location, amongst the products deemed by the API as relevant). See
   * SearchRequest.offset for definition. If this field is negative, an
   * INVALID_ARGUMENT is returned. This can only be set for `search` events.
   * Other event types should not set this field. Otherwise, an INVALID_ARGUMENT
   * error is returned.
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
   * The order in which products are returned. See SearchRequest.order_by for
   * definition and syntax. The value must be a UTF-8 encoded string with a
   * length limit of 1,000 characters. Otherwise, an INVALID_ARGUMENT error is
   * returned. This can only be set for `search` events. Other event types
   * should not set this field. Otherwise, an INVALID_ARGUMENT error is
   * returned.
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
   * The categories associated with a category page. To represent full path of
   * category, use '>' sign to separate different hierarchies. If '>' is part of
   * the category name, replace it with other character(s). Category pages
   * include special pages such as sales or promotions. For instance, a special
   * sale page may have the category hierarchy: "pageCategories" : ["Sales >
   * 2017 Black Friday Deals"]. Required for `category-page-view` events. At
   * least one of search_query or page_categories is required for `search`
   * events. Other event types should not set this field. Otherwise, an
   * INVALID_ARGUMENT error is returned.
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
   * A unique ID of a web page view. This should be kept the same for all user
   * events triggered from the same pageview. For example, an item detail page
   * view could trigger multiple events as the user is browsing the page. The
   * `pageViewId` property should be kept the same for all these events so that
   * they can be grouped together properly. When using the client side event
   * reporting with JavaScript pixel and Google Tag Manager, this value is
   * filled in automatically.
   *
   * @param string $pageViewId
   */
  public function setPageViewId($pageViewId)
  {
    $this->pageViewId = $pageViewId;
  }
  /**
   * @return string
   */
  public function getPageViewId()
  {
    return $this->pageViewId;
  }
  /**
   * Optional. List of panels associated with this event. Used for panel-level
   * impression data.
   *
   * @param GoogleCloudRetailV2PanelInfo[] $panels
   */
  public function setPanels($panels)
  {
    $this->panels = $panels;
  }
  /**
   * @return GoogleCloudRetailV2PanelInfo[]
   */
  public function getPanels()
  {
    return $this->panels;
  }
  /**
   * The main product details related to the event. This field is optional
   * except for the following event types: * `add-to-cart` * `detail-page-view`
   * * `purchase-complete` In a `search` event, this field represents the
   * products returned to the end user on the current page (the end user may
   * have not finished browsing the whole page yet). When a new page is returned
   * to the end user, after pagination/filtering/ordering even for the same
   * query, a new `search` event with different product_details is desired. The
   * end user may have not finished browsing the whole page yet.
   *
   * @param GoogleCloudRetailV2ProductDetail[] $productDetails
   */
  public function setProductDetails($productDetails)
  {
    $this->productDetails = $productDetails;
  }
  /**
   * @return GoogleCloudRetailV2ProductDetail[]
   */
  public function getProductDetails()
  {
    return $this->productDetails;
  }
  /**
   * A transaction represents the entire purchase transaction. Required for
   * `purchase-complete` events. Other event types should not set this field.
   * Otherwise, an INVALID_ARGUMENT error is returned.
   *
   * @param GoogleCloudRetailV2PurchaseTransaction $purchaseTransaction
   */
  public function setPurchaseTransaction(GoogleCloudRetailV2PurchaseTransaction $purchaseTransaction)
  {
    $this->purchaseTransaction = $purchaseTransaction;
  }
  /**
   * @return GoogleCloudRetailV2PurchaseTransaction
   */
  public function getPurchaseTransaction()
  {
    return $this->purchaseTransaction;
  }
  /**
   * The referrer URL of the current page. When using the client side event
   * reporting with JavaScript pixel and Google Tag Manager, this value is
   * filled in automatically.
   *
   * @param string $referrerUri
   */
  public function setReferrerUri($referrerUri)
  {
    $this->referrerUri = $referrerUri;
  }
  /**
   * @return string
   */
  public function getReferrerUri()
  {
    return $this->referrerUri;
  }
  /**
   * The user's search query. See SearchRequest.query for definition. The value
   * must be a UTF-8 encoded string with a length limit of 5,000 characters.
   * Otherwise, an INVALID_ARGUMENT error is returned. At least one of
   * search_query or page_categories is required for `search` events. Other
   * event types should not set this field. Otherwise, an INVALID_ARGUMENT error
   * is returned.
   *
   * @param string $searchQuery
   */
  public function setSearchQuery($searchQuery)
  {
    $this->searchQuery = $searchQuery;
  }
  /**
   * @return string
   */
  public function getSearchQuery()
  {
    return $this->searchQuery;
  }
  /**
   * A unique identifier for tracking a visitor session with a length limit of
   * 128 bytes. A session is an aggregation of an end user behavior in a time
   * span. A general guideline to populate the session_id: 1. If user has no
   * activity for 30 min, a new session_id should be assigned. 2. The session_id
   * should be unique across users, suggest use uuid or add visitor_id as
   * prefix.
   *
   * @param string $sessionId
   */
  public function setSessionId($sessionId)
  {
    $this->sessionId = $sessionId;
  }
  /**
   * @return string
   */
  public function getSessionId()
  {
    return $this->sessionId;
  }
  /**
   * Complete URL (window.location.href) of the user's current page. When using
   * the client side event reporting with JavaScript pixel and Google Tag
   * Manager, this value is filled in automatically. Maximum length 5,000
   * characters.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
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
   * Required. A unique identifier for tracking visitors. For example, this
   * could be implemented with an HTTP cookie, which should be able to uniquely
   * identify a visitor on a single device. This unique identifier should not
   * change if the visitor log in/out of the website. Don't set the field to the
   * same fixed ID for different users. This mixes the event history of those
   * users together, which results in degraded model quality. The field must be
   * a UTF-8 encoded string with a length limit of 128 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned. The field should not contain PII or
   * user-data. We recommend to use Google Analytics [Client ID](https://develop
   * ers.google.com/analytics/devguides/collection/analyticsjs/field-
   * reference#clientId) for this field.
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
class_alias(GoogleCloudRetailV2UserEvent::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2UserEvent');
