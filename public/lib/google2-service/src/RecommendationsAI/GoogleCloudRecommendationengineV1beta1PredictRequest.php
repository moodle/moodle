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

class GoogleCloudRecommendationengineV1beta1PredictRequest extends \Google\Model
{
  /**
   * Optional. Use dryRun mode for this prediction query. If set to true, a fake
   * model will be used that returns arbitrary catalog items. Note that the
   * dryRun mode should only be used for testing the API, or if the model is not
   * ready.
   *
   * @var bool
   */
  public $dryRun;
  /**
   * Optional. Filter for restricting prediction results. Accepts values for
   * tags and the `filterOutOfStockItems` flag. * Tag expressions. Restricts
   * predictions to items that match all of the specified tags. Boolean
   * operators `OR` and `NOT` are supported if the expression is enclosed in
   * parentheses, and must be separated from the tag values by a space.
   * `-"tagA"` is also supported and is equivalent to `NOT "tagA"`. Tag values
   * must be double quoted UTF-8 encoded strings with a size limit of 1 KiB. *
   * filterOutOfStockItems. Restricts predictions to items that do not have a
   * stockState value of OUT_OF_STOCK. Examples: * tag=("Red" OR "Blue")
   * tag="New-Arrival" tag=(NOT "promotional") * filterOutOfStockItems
   * tag=(-"promotional") * filterOutOfStockItems If your filter blocks all
   * prediction results, nothing will be returned. If you want generic
   * (unfiltered) popular items to be returned instead, set `strictFiltering` to
   * false in `PredictRequest.params`.
   *
   * @var string
   */
  public $filter;
  /**
   * Optional. The labels for the predict request. * Label keys can contain
   * lowercase letters, digits and hyphens, must start with a letter, and must
   * end with a letter or digit. * Non-zero label values can contain lowercase
   * letters, digits and hyphens, must start with a letter, and must end with a
   * letter or digit. * No more than 64 labels can be associated with a given
   * request. See https://goo.gl/xmQnxf for more information on and examples of
   * labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. Maximum number of results to return per page. Set this property
   * to the number of prediction results required. If zero, the service will
   * choose a reasonable default.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Optional. The previous PredictResponse.next_page_token.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Optional. Additional domain specific parameters for the predictions.
   * Allowed values: * `returnCatalogItem`: Boolean. If set to true, the
   * associated catalogItem object will be returned in the
   * `PredictResponse.PredictionResult.itemMetadata` object in the method
   * response. * `returnItemScore`: Boolean. If set to true, the prediction
   * 'score' corresponding to each returned item will be set in the `metadata`
   * field in the prediction response. The given 'score' indicates the
   * probability of an item being clicked/purchased given the user's context and
   * history. * `strictFiltering`: Boolean. True by default. If set to false,
   * the service will return generic (unfiltered) popular items instead of empty
   * if your filter blocks all prediction results. * `priceRerankLevel`: String.
   * Default empty. If set to be non-empty, then it needs to be one of {'no-
   * price-reranking', 'low-price-reranking', 'medium-price-reranking', 'high-
   * price-reranking'}. This gives request level control and adjust prediction
   * results based on product price. * `diversityLevel`: String. Default empty.
   * If set to be non-empty, then it needs to be one of {'no-diversity', 'low-
   * diversity', 'medium-diversity', 'high-diversity', 'auto-diversity'}. This
   * gives request level control and adjust prediction results based on product
   * category.
   *
   * @var array[]
   */
  public $params;
  protected $userEventType = GoogleCloudRecommendationengineV1beta1UserEvent::class;
  protected $userEventDataType = '';

  /**
   * Optional. Use dryRun mode for this prediction query. If set to true, a fake
   * model will be used that returns arbitrary catalog items. Note that the
   * dryRun mode should only be used for testing the API, or if the model is not
   * ready.
   *
   * @param bool $dryRun
   */
  public function setDryRun($dryRun)
  {
    $this->dryRun = $dryRun;
  }
  /**
   * @return bool
   */
  public function getDryRun()
  {
    return $this->dryRun;
  }
  /**
   * Optional. Filter for restricting prediction results. Accepts values for
   * tags and the `filterOutOfStockItems` flag. * Tag expressions. Restricts
   * predictions to items that match all of the specified tags. Boolean
   * operators `OR` and `NOT` are supported if the expression is enclosed in
   * parentheses, and must be separated from the tag values by a space.
   * `-"tagA"` is also supported and is equivalent to `NOT "tagA"`. Tag values
   * must be double quoted UTF-8 encoded strings with a size limit of 1 KiB. *
   * filterOutOfStockItems. Restricts predictions to items that do not have a
   * stockState value of OUT_OF_STOCK. Examples: * tag=("Red" OR "Blue")
   * tag="New-Arrival" tag=(NOT "promotional") * filterOutOfStockItems
   * tag=(-"promotional") * filterOutOfStockItems If your filter blocks all
   * prediction results, nothing will be returned. If you want generic
   * (unfiltered) popular items to be returned instead, set `strictFiltering` to
   * false in `PredictRequest.params`.
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
   * Optional. The labels for the predict request. * Label keys can contain
   * lowercase letters, digits and hyphens, must start with a letter, and must
   * end with a letter or digit. * Non-zero label values can contain lowercase
   * letters, digits and hyphens, must start with a letter, and must end with a
   * letter or digit. * No more than 64 labels can be associated with a given
   * request. See https://goo.gl/xmQnxf for more information on and examples of
   * labels.
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
   * Optional. Maximum number of results to return per page. Set this property
   * to the number of prediction results required. If zero, the service will
   * choose a reasonable default.
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
   * Optional. The previous PredictResponse.next_page_token.
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
   * Optional. Additional domain specific parameters for the predictions.
   * Allowed values: * `returnCatalogItem`: Boolean. If set to true, the
   * associated catalogItem object will be returned in the
   * `PredictResponse.PredictionResult.itemMetadata` object in the method
   * response. * `returnItemScore`: Boolean. If set to true, the prediction
   * 'score' corresponding to each returned item will be set in the `metadata`
   * field in the prediction response. The given 'score' indicates the
   * probability of an item being clicked/purchased given the user's context and
   * history. * `strictFiltering`: Boolean. True by default. If set to false,
   * the service will return generic (unfiltered) popular items instead of empty
   * if your filter blocks all prediction results. * `priceRerankLevel`: String.
   * Default empty. If set to be non-empty, then it needs to be one of {'no-
   * price-reranking', 'low-price-reranking', 'medium-price-reranking', 'high-
   * price-reranking'}. This gives request level control and adjust prediction
   * results based on product price. * `diversityLevel`: String. Default empty.
   * If set to be non-empty, then it needs to be one of {'no-diversity', 'low-
   * diversity', 'medium-diversity', 'high-diversity', 'auto-diversity'}. This
   * gives request level control and adjust prediction results based on product
   * category.
   *
   * @param array[] $params
   */
  public function setParams($params)
  {
    $this->params = $params;
  }
  /**
   * @return array[]
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * Required. Context about the user, what they are looking at and what action
   * they took to trigger the predict request. Note that this user event detail
   * won't be ingested to userEvent logs. Thus, a separate userEvent write
   * request is required for event logging. Don't set UserInfo.visitor_id or
   * UserInfo.user_id to the same fixed ID for different users. If you are
   * trying to receive non-personalized recommendations (not recommended; this
   * can negatively impact model performance), instead set UserInfo.visitor_id
   * to a random unique ID and leave UserInfo.user_id unset.
   *
   * @param GoogleCloudRecommendationengineV1beta1UserEvent $userEvent
   */
  public function setUserEvent(GoogleCloudRecommendationengineV1beta1UserEvent $userEvent)
  {
    $this->userEvent = $userEvent;
  }
  /**
   * @return GoogleCloudRecommendationengineV1beta1UserEvent
   */
  public function getUserEvent()
  {
    return $this->userEvent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommendationengineV1beta1PredictRequest::class, 'Google_Service_RecommendationsAI_GoogleCloudRecommendationengineV1beta1PredictRequest');
