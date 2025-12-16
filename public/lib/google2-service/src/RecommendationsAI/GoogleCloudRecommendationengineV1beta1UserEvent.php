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

namespace Google\Service\RecommendationsAI;

class GoogleCloudRecommendationengineV1beta1UserEvent extends \Google\Model
{
  /**
   * Unspecified event source.
   */
  public const EVENT_SOURCE_EVENT_SOURCE_UNSPECIFIED = 'EVENT_SOURCE_UNSPECIFIED';
  /**
   * The event is ingested via a javascript pixel or Recommendations AI Tag
   * through automl datalayer or JS Macros.
   */
  public const EVENT_SOURCE_AUTOML = 'AUTOML';
  /**
   * The event is ingested via Recommendations AI Tag through Enhanced Ecommerce
   * datalayer.
   */
  public const EVENT_SOURCE_ECOMMERCE = 'ECOMMERCE';
  /**
   * The event is ingested via Import user events API.
   */
  public const EVENT_SOURCE_BATCH_UPLOAD = 'BATCH_UPLOAD';
  protected $eventDetailType = GoogleCloudRecommendationengineV1beta1EventDetail::class;
  protected $eventDetailDataType = '';
  /**
   * Optional. This field should *not* be set when using JavaScript pixel or the
   * Recommendations AI Tag. Defaults to `EVENT_SOURCE_UNSPECIFIED`.
   *
   * @var string
   */
  public $eventSource;
  /**
   * Optional. Only required for ImportUserEvents method. Timestamp of user
   * event created.
   *
   * @var string
   */
  public $eventTime;
  /**
   * Required. User event type. Allowed values are: * `add-to-cart` Products
   * being added to cart. * `add-to-list` Items being added to a list (shopping
   * list, favorites etc). * `category-page-view` Special pages such as sale or
   * promotion pages viewed. * `checkout-start` User starting a checkout
   * process. * `detail-page-view` Products detail page viewed. * `home-page-
   * view` Homepage viewed. * `page-visit` Generic page visits not included in
   * the event types above. * `purchase-complete` User finishing a purchase. *
   * `refund` Purchased items being refunded or returned. * `remove-from-cart`
   * Products being removed from cart. * `remove-from-list` Items being removed
   * from a list. * `search` Product search. * `shopping-cart-page-view` User
   * viewing a shopping cart. * `impression` List of items displayed. Used by
   * Google Tag Manager.
   *
   * @var string
   */
  public $eventType;
  protected $productEventDetailType = GoogleCloudRecommendationengineV1beta1ProductEventDetail::class;
  protected $productEventDetailDataType = '';
  protected $userInfoType = GoogleCloudRecommendationengineV1beta1UserInfo::class;
  protected $userInfoDataType = '';

  /**
   * Optional. User event detailed information common across different
   * recommendation types.
   *
   * @param GoogleCloudRecommendationengineV1beta1EventDetail $eventDetail
   */
  public function setEventDetail(GoogleCloudRecommendationengineV1beta1EventDetail $eventDetail)
  {
    $this->eventDetail = $eventDetail;
  }
  /**
   * @return GoogleCloudRecommendationengineV1beta1EventDetail
   */
  public function getEventDetail()
  {
    return $this->eventDetail;
  }
  /**
   * Optional. This field should *not* be set when using JavaScript pixel or the
   * Recommendations AI Tag. Defaults to `EVENT_SOURCE_UNSPECIFIED`.
   *
   * Accepted values: EVENT_SOURCE_UNSPECIFIED, AUTOML, ECOMMERCE, BATCH_UPLOAD
   *
   * @param self::EVENT_SOURCE_* $eventSource
   */
  public function setEventSource($eventSource)
  {
    $this->eventSource = $eventSource;
  }
  /**
   * @return self::EVENT_SOURCE_*
   */
  public function getEventSource()
  {
    return $this->eventSource;
  }
  /**
   * Optional. Only required for ImportUserEvents method. Timestamp of user
   * event created.
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
   * Required. User event type. Allowed values are: * `add-to-cart` Products
   * being added to cart. * `add-to-list` Items being added to a list (shopping
   * list, favorites etc). * `category-page-view` Special pages such as sale or
   * promotion pages viewed. * `checkout-start` User starting a checkout
   * process. * `detail-page-view` Products detail page viewed. * `home-page-
   * view` Homepage viewed. * `page-visit` Generic page visits not included in
   * the event types above. * `purchase-complete` User finishing a purchase. *
   * `refund` Purchased items being refunded or returned. * `remove-from-cart`
   * Products being removed from cart. * `remove-from-list` Items being removed
   * from a list. * `search` Product search. * `shopping-cart-page-view` User
   * viewing a shopping cart. * `impression` List of items displayed. Used by
   * Google Tag Manager.
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
   * Optional. Retail product specific user event metadata. This field is
   * required for the following event types: * `add-to-cart` * `add-to-list` *
   * `category-page-view` * `checkout-start` * `detail-page-view` * `purchase-
   * complete` * `refund` * `remove-from-cart` * `remove-from-list` * `search`
   * This field is optional for the following event types: * `page-visit` *
   * `shopping-cart-page-view` - note that 'product_event_detail' should be set
   * for this unless the shopping cart is empty. This field is not allowed for
   * the following event types: * `home-page-view`
   *
   * @param GoogleCloudRecommendationengineV1beta1ProductEventDetail $productEventDetail
   */
  public function setProductEventDetail(GoogleCloudRecommendationengineV1beta1ProductEventDetail $productEventDetail)
  {
    $this->productEventDetail = $productEventDetail;
  }
  /**
   * @return GoogleCloudRecommendationengineV1beta1ProductEventDetail
   */
  public function getProductEventDetail()
  {
    return $this->productEventDetail;
  }
  /**
   * Required. User information.
   *
   * @param GoogleCloudRecommendationengineV1beta1UserInfo $userInfo
   */
  public function setUserInfo(GoogleCloudRecommendationengineV1beta1UserInfo $userInfo)
  {
    $this->userInfo = $userInfo;
  }
  /**
   * @return GoogleCloudRecommendationengineV1beta1UserInfo
   */
  public function getUserInfo()
  {
    return $this->userInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommendationengineV1beta1UserEvent::class, 'Google_Service_RecommendationsAI_GoogleCloudRecommendationengineV1beta1UserEvent');
