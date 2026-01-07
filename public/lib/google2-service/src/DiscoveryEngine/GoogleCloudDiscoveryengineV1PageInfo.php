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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1PageInfo extends \Google\Model
{
  /**
   * The most specific category associated with a category page. To represent
   * full path of category, use '>' sign to separate different hierarchies. If
   * '>' is part of the category name, replace it with other character(s).
   * Category pages include special pages such as sales or promotions. For
   * instance, a special sale page may have the category hierarchy:
   * `"pageCategory" : "Sales > 2017 Black Friday Deals"`. Required for `view-
   * category-page` events. Other event types should not set this field.
   * Otherwise, an `INVALID_ARGUMENT` error is returned.
   *
   * @var string
   */
  public $pageCategory;
  /**
   * A unique ID of a web page view. This should be kept the same for all user
   * events triggered from the same pageview. For example, an item detail page
   * view could trigger multiple events as the user is browsing the page. The
   * `pageview_id` property should be kept the same for all these events so that
   * they can be grouped together properly. When using the client side event
   * reporting with JavaScript pixel and Google Tag Manager, this value is
   * filled in automatically.
   *
   * @var string
   */
  public $pageviewId;
  /**
   * The referrer URL of the current page. When using the client side event
   * reporting with JavaScript pixel and Google Tag Manager, this value is
   * filled in automatically. However, some browser privacy restrictions may
   * cause this field to be empty.
   *
   * @var string
   */
  public $referrerUri;
  /**
   * Complete URL (window.location.href) of the user's current page. When using
   * the client side event reporting with JavaScript pixel and Google Tag
   * Manager, this value is filled in automatically. Maximum length 5,000
   * characters.
   *
   * @var string
   */
  public $uri;

  /**
   * The most specific category associated with a category page. To represent
   * full path of category, use '>' sign to separate different hierarchies. If
   * '>' is part of the category name, replace it with other character(s).
   * Category pages include special pages such as sales or promotions. For
   * instance, a special sale page may have the category hierarchy:
   * `"pageCategory" : "Sales > 2017 Black Friday Deals"`. Required for `view-
   * category-page` events. Other event types should not set this field.
   * Otherwise, an `INVALID_ARGUMENT` error is returned.
   *
   * @param string $pageCategory
   */
  public function setPageCategory($pageCategory)
  {
    $this->pageCategory = $pageCategory;
  }
  /**
   * @return string
   */
  public function getPageCategory()
  {
    return $this->pageCategory;
  }
  /**
   * A unique ID of a web page view. This should be kept the same for all user
   * events triggered from the same pageview. For example, an item detail page
   * view could trigger multiple events as the user is browsing the page. The
   * `pageview_id` property should be kept the same for all these events so that
   * they can be grouped together properly. When using the client side event
   * reporting with JavaScript pixel and Google Tag Manager, this value is
   * filled in automatically.
   *
   * @param string $pageviewId
   */
  public function setPageviewId($pageviewId)
  {
    $this->pageviewId = $pageviewId;
  }
  /**
   * @return string
   */
  public function getPageviewId()
  {
    return $this->pageviewId;
  }
  /**
   * The referrer URL of the current page. When using the client side event
   * reporting with JavaScript pixel and Google Tag Manager, this value is
   * filled in automatically. However, some browser privacy restrictions may
   * cause this field to be empty.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1PageInfo::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1PageInfo');
