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

class GoogleCloudDiscoveryengineV1SearchRequestBoostSpecConditionBoostSpec extends \Google\Model
{
  /**
   * Strength of the condition boost, which should be in [-1, 1]. Negative boost
   * means demotion. Default is 0.0. Setting to 1.0 gives the document a big
   * promotion. However, it does not necessarily mean that the boosted document
   * will be the top result at all times, nor that other documents will be
   * excluded. Results could still be shown even when none of them matches the
   * condition. And results that are significantly more relevant to the search
   * query can still trump your heavily favored but irrelevant documents.
   * Setting to -1.0 gives the document a big demotion. However, results that
   * are deeply relevant might still be shown. The document will have an
   * upstream battle to get a fairly high ranking, but it is not blocked out
   * completely. Setting to 0.0 means no boost applied. The boosting condition
   * is ignored. Only one of the (condition, boost) combination or the
   * boost_control_spec below are set. If both are set then the global boost is
   * ignored and the more fine-grained boost_control_spec is applied.
   *
   * @var float
   */
  public $boost;
  protected $boostControlSpecType = GoogleCloudDiscoveryengineV1SearchRequestBoostSpecConditionBoostSpecBoostControlSpec::class;
  protected $boostControlSpecDataType = '';
  /**
   * An expression which specifies a boost condition. The syntax and supported
   * fields are the same as a filter expression. See SearchRequest.filter for
   * detail syntax and limitations. Examples: * To boost documents with document
   * ID "doc_1" or "doc_2", and color "Red" or "Blue": `(document_id:
   * ANY("doc_1", "doc_2")) AND (color: ANY("Red", "Blue"))`
   *
   * @var string
   */
  public $condition;

  /**
   * Strength of the condition boost, which should be in [-1, 1]. Negative boost
   * means demotion. Default is 0.0. Setting to 1.0 gives the document a big
   * promotion. However, it does not necessarily mean that the boosted document
   * will be the top result at all times, nor that other documents will be
   * excluded. Results could still be shown even when none of them matches the
   * condition. And results that are significantly more relevant to the search
   * query can still trump your heavily favored but irrelevant documents.
   * Setting to -1.0 gives the document a big demotion. However, results that
   * are deeply relevant might still be shown. The document will have an
   * upstream battle to get a fairly high ranking, but it is not blocked out
   * completely. Setting to 0.0 means no boost applied. The boosting condition
   * is ignored. Only one of the (condition, boost) combination or the
   * boost_control_spec below are set. If both are set then the global boost is
   * ignored and the more fine-grained boost_control_spec is applied.
   *
   * @param float $boost
   */
  public function setBoost($boost)
  {
    $this->boost = $boost;
  }
  /**
   * @return float
   */
  public function getBoost()
  {
    return $this->boost;
  }
  /**
   * Complex specification for custom ranking based on customer defined
   * attribute value.
   *
   * @param GoogleCloudDiscoveryengineV1SearchRequestBoostSpecConditionBoostSpecBoostControlSpec $boostControlSpec
   */
  public function setBoostControlSpec(GoogleCloudDiscoveryengineV1SearchRequestBoostSpecConditionBoostSpecBoostControlSpec $boostControlSpec)
  {
    $this->boostControlSpec = $boostControlSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchRequestBoostSpecConditionBoostSpecBoostControlSpec
   */
  public function getBoostControlSpec()
  {
    return $this->boostControlSpec;
  }
  /**
   * An expression which specifies a boost condition. The syntax and supported
   * fields are the same as a filter expression. See SearchRequest.filter for
   * detail syntax and limitations. Examples: * To boost documents with document
   * ID "doc_1" or "doc_2", and color "Red" or "Blue": `(document_id:
   * ANY("doc_1", "doc_2")) AND (color: ANY("Red", "Blue"))`
   *
   * @param string $condition
   */
  public function setCondition($condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return string
   */
  public function getCondition()
  {
    return $this->condition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SearchRequestBoostSpecConditionBoostSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchRequestBoostSpecConditionBoostSpec');
