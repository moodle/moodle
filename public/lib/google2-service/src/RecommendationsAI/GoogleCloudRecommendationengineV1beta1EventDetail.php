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

class GoogleCloudRecommendationengineV1beta1EventDetail extends \Google\Collection
{
  protected $collection_key = 'experimentIds';
  protected $eventAttributesType = GoogleCloudRecommendationengineV1beta1FeatureMap::class;
  protected $eventAttributesDataType = '';
  /**
   * Optional. A list of identifiers for the independent experiment groups this
   * user event belongs to. This is used to distinguish between user events
   * associated with different experiment setups (e.g. using Recommendation
   * Engine system, using different recommendation models).
   *
   * @var string[]
   */
  public $experimentIds;
  /**
   * Optional. A unique id of a web page view. This should be kept the same for
   * all user events triggered from the same pageview. For example, an item
   * detail page view could trigger multiple events as the user is browsing the
   * page. The `pageViewId` property should be kept the same for all these
   * events so that they can be grouped together properly. This `pageViewId`
   * will be automatically generated if using the JavaScript pixel.
   *
   * @var string
   */
  public $pageViewId;
  /**
   * Optional. Recommendation token included in the recommendation prediction
   * response. This field enables accurate attribution of recommendation model
   * performance. This token enables us to accurately attribute page view or
   * purchase back to the event and the particular predict response containing
   * this clicked/purchased item. If user clicks on product K in the
   * recommendation results, pass the `PredictResponse.recommendationToken`
   * property as a url parameter to product K's page. When recording events on
   * product K's page, log the PredictResponse.recommendation_token to this
   * field. Optional, but highly encouraged for user events that are the result
   * of a recommendation prediction query.
   *
   * @var string
   */
  public $recommendationToken;
  /**
   * Optional. The referrer url of the current page. When using the JavaScript
   * pixel, this value is filled in automatically.
   *
   * @var string
   */
  public $referrerUri;
  /**
   * Optional. Complete url (window.location.href) of the user's current page.
   * When using the JavaScript pixel, this value is filled in automatically.
   * Maximum length 5KB.
   *
   * @var string
   */
  public $uri;

  /**
   * Optional. Extra user event features to include in the recommendation model.
   * For product recommendation, an example of extra user information is
   * traffic_channel, i.e. how user arrives at the site. Users can arrive at the
   * site by coming to the site directly, or coming through Google search, and
   * etc.
   *
   * @param GoogleCloudRecommendationengineV1beta1FeatureMap $eventAttributes
   */
  public function setEventAttributes(GoogleCloudRecommendationengineV1beta1FeatureMap $eventAttributes)
  {
    $this->eventAttributes = $eventAttributes;
  }
  /**
   * @return GoogleCloudRecommendationengineV1beta1FeatureMap
   */
  public function getEventAttributes()
  {
    return $this->eventAttributes;
  }
  /**
   * Optional. A list of identifiers for the independent experiment groups this
   * user event belongs to. This is used to distinguish between user events
   * associated with different experiment setups (e.g. using Recommendation
   * Engine system, using different recommendation models).
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
   * Optional. A unique id of a web page view. This should be kept the same for
   * all user events triggered from the same pageview. For example, an item
   * detail page view could trigger multiple events as the user is browsing the
   * page. The `pageViewId` property should be kept the same for all these
   * events so that they can be grouped together properly. This `pageViewId`
   * will be automatically generated if using the JavaScript pixel.
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
   * Optional. Recommendation token included in the recommendation prediction
   * response. This field enables accurate attribution of recommendation model
   * performance. This token enables us to accurately attribute page view or
   * purchase back to the event and the particular predict response containing
   * this clicked/purchased item. If user clicks on product K in the
   * recommendation results, pass the `PredictResponse.recommendationToken`
   * property as a url parameter to product K's page. When recording events on
   * product K's page, log the PredictResponse.recommendation_token to this
   * field. Optional, but highly encouraged for user events that are the result
   * of a recommendation prediction query.
   *
   * @param string $recommendationToken
   */
  public function setRecommendationToken($recommendationToken)
  {
    $this->recommendationToken = $recommendationToken;
  }
  /**
   * @return string
   */
  public function getRecommendationToken()
  {
    return $this->recommendationToken;
  }
  /**
   * Optional. The referrer url of the current page. When using the JavaScript
   * pixel, this value is filled in automatically.
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
   * Optional. Complete url (window.location.href) of the user's current page.
   * When using the JavaScript pixel, this value is filled in automatically.
   * Maximum length 5KB.
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
class_alias(GoogleCloudRecommendationengineV1beta1EventDetail::class, 'Google_Service_RecommendationsAI_GoogleCloudRecommendationengineV1beta1EventDetail');
