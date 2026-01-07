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

class GoogleCloudRetailV2PredictRequest extends \Google\Model
{
  /**
   * Filter for restricting prediction results with a length limit of 5,000
   * characters. Accepts values for tags and the `filterOutOfStockItems` flag. *
   * Tag expressions. Restricts predictions to products that match all of the
   * specified tags. Boolean operators `OR` and `NOT` are supported if the
   * expression is enclosed in parentheses, and must be separated from the tag
   * values by a space. `-"tagA"` is also supported and is equivalent to `NOT
   * "tagA"`. Tag values must be double quoted UTF-8 encoded strings with a size
   * limit of 1,000 characters. Note: "Recently viewed" models don't support tag
   * filtering at the moment. * filterOutOfStockItems. Restricts predictions to
   * products that do not have a stockState value of OUT_OF_STOCK. Examples: *
   * tag=("Red" OR "Blue") tag="New-Arrival" tag=(NOT "promotional") *
   * filterOutOfStockItems tag=(-"promotional") * filterOutOfStockItems If your
   * filter blocks all prediction results, the API will return *no* results. If
   * instead you want empty result sets to return generic (unfiltered) popular
   * products, set `strictFiltering` to False in `PredictRequest.params`. Note
   * that the API will never return items with storageStatus of "EXPIRED" or
   * "DELETED" regardless of filter choices. If `filterSyntaxV2` is set to true
   * under the `params` field, then attribute-based expressions are expected
   * instead of the above described tag-based syntax. Examples: * (colors:
   * ANY("Red", "Blue")) AND NOT (categories: ANY("Phones")) * (availability:
   * ANY("IN_STOCK")) AND (colors: ANY("Red") OR categories: ANY("Phones")) For
   * more information, see [Filter
   * recommendations](https://cloud.google.com/retail/docs/filter-recs).
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
   * letter or international character. See [Google Cloud
   * Document](https://cloud.google.com/resource-manager/docs/creating-managing-
   * labels#requirements) for more details.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Maximum number of results to return. Set this property to the number of
   * prediction results needed. If zero, the service will choose a reasonable
   * default. The maximum allowed value is 100. Values above 100 will be coerced
   * to 100.
   *
   * @var int
   */
  public $pageSize;
  /**
   * This field is not used; leave it unset.
   *
   * @deprecated
   * @var string
   */
  public $pageToken;
  /**
   * Additional domain specific parameters for the predictions. Allowed values:
   * * `returnProduct`: Boolean. If set to true, the associated product object
   * will be returned in the `results.metadata` field in the prediction
   * response. * `returnScore`: Boolean. If set to true, the prediction 'score'
   * corresponding to each returned product will be set in the
   * `results.metadata` field in the prediction response. The given 'score'
   * indicates the probability of a product being clicked/purchased given the
   * user's context and history. * `strictFiltering`: Boolean. True by default.
   * If set to false, the service will return generic (unfiltered) popular
   * products instead of empty if your filter blocks all prediction results. *
   * `priceRerankLevel`: String. Default empty. If set to be non-empty, then it
   * needs to be one of {'no-price-reranking', 'low-price-reranking', 'medium-
   * price-reranking', 'high-price-reranking'}. This gives request-level control
   * and adjusts prediction results based on product price. * `diversityLevel`:
   * String. Default empty. If set to be non-empty, then it needs to be one of
   * {'no-diversity', 'low-diversity', 'medium-diversity', 'high-diversity',
   * 'auto-diversity'}. This gives request-level control and adjusts prediction
   * results based on product category. * `filterSyntaxV2`: Boolean. False by
   * default. If set to true, the `filter` field is interpreteted according to
   * the new, attribute-based syntax.
   *
   * @var array[]
   */
  public $params;
  protected $userEventType = GoogleCloudRetailV2UserEvent::class;
  protected $userEventDataType = '';
  /**
   * Use validate only mode for this prediction query. If set to true, a dummy
   * model will be used that returns arbitrary products. Note that the validate
   * only mode should only be used for testing the API, or if the model is not
   * ready.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * Filter for restricting prediction results with a length limit of 5,000
   * characters. Accepts values for tags and the `filterOutOfStockItems` flag. *
   * Tag expressions. Restricts predictions to products that match all of the
   * specified tags. Boolean operators `OR` and `NOT` are supported if the
   * expression is enclosed in parentheses, and must be separated from the tag
   * values by a space. `-"tagA"` is also supported and is equivalent to `NOT
   * "tagA"`. Tag values must be double quoted UTF-8 encoded strings with a size
   * limit of 1,000 characters. Note: "Recently viewed" models don't support tag
   * filtering at the moment. * filterOutOfStockItems. Restricts predictions to
   * products that do not have a stockState value of OUT_OF_STOCK. Examples: *
   * tag=("Red" OR "Blue") tag="New-Arrival" tag=(NOT "promotional") *
   * filterOutOfStockItems tag=(-"promotional") * filterOutOfStockItems If your
   * filter blocks all prediction results, the API will return *no* results. If
   * instead you want empty result sets to return generic (unfiltered) popular
   * products, set `strictFiltering` to False in `PredictRequest.params`. Note
   * that the API will never return items with storageStatus of "EXPIRED" or
   * "DELETED" regardless of filter choices. If `filterSyntaxV2` is set to true
   * under the `params` field, then attribute-based expressions are expected
   * instead of the above described tag-based syntax. Examples: * (colors:
   * ANY("Red", "Blue")) AND NOT (categories: ANY("Phones")) * (availability:
   * ANY("IN_STOCK")) AND (colors: ANY("Red") OR categories: ANY("Phones")) For
   * more information, see [Filter
   * recommendations](https://cloud.google.com/retail/docs/filter-recs).
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
   * letter or international character. See [Google Cloud
   * Document](https://cloud.google.com/resource-manager/docs/creating-managing-
   * labels#requirements) for more details.
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
   * Maximum number of results to return. Set this property to the number of
   * prediction results needed. If zero, the service will choose a reasonable
   * default. The maximum allowed value is 100. Values above 100 will be coerced
   * to 100.
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
   * This field is not used; leave it unset.
   *
   * @deprecated
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * Additional domain specific parameters for the predictions. Allowed values:
   * * `returnProduct`: Boolean. If set to true, the associated product object
   * will be returned in the `results.metadata` field in the prediction
   * response. * `returnScore`: Boolean. If set to true, the prediction 'score'
   * corresponding to each returned product will be set in the
   * `results.metadata` field in the prediction response. The given 'score'
   * indicates the probability of a product being clicked/purchased given the
   * user's context and history. * `strictFiltering`: Boolean. True by default.
   * If set to false, the service will return generic (unfiltered) popular
   * products instead of empty if your filter blocks all prediction results. *
   * `priceRerankLevel`: String. Default empty. If set to be non-empty, then it
   * needs to be one of {'no-price-reranking', 'low-price-reranking', 'medium-
   * price-reranking', 'high-price-reranking'}. This gives request-level control
   * and adjusts prediction results based on product price. * `diversityLevel`:
   * String. Default empty. If set to be non-empty, then it needs to be one of
   * {'no-diversity', 'low-diversity', 'medium-diversity', 'high-diversity',
   * 'auto-diversity'}. This gives request-level control and adjusts prediction
   * results based on product category. * `filterSyntaxV2`: Boolean. False by
   * default. If set to true, the `filter` field is interpreteted according to
   * the new, attribute-based syntax.
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
   * request is required for event logging. Don't set UserEvent.visitor_id or
   * UserInfo.user_id to the same fixed ID for different users. If you are
   * trying to receive non-personalized recommendations (not recommended; this
   * can negatively impact model performance), instead set UserEvent.visitor_id
   * to a random unique ID and leave UserInfo.user_id unset.
   *
   * @param GoogleCloudRetailV2UserEvent $userEvent
   */
  public function setUserEvent(GoogleCloudRetailV2UserEvent $userEvent)
  {
    $this->userEvent = $userEvent;
  }
  /**
   * @return GoogleCloudRetailV2UserEvent
   */
  public function getUserEvent()
  {
    return $this->userEvent;
  }
  /**
   * Use validate only mode for this prediction query. If set to true, a dummy
   * model will be used that returns arbitrary products. Note that the validate
   * only mode should only be used for testing the API, or if the model is not
   * ready.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2PredictRequest::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2PredictRequest');
