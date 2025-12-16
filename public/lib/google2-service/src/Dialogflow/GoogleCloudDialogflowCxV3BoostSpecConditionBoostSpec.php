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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3BoostSpecConditionBoostSpec extends \Google\Model
{
  /**
   * Optional. Strength of the condition boost, which should be in [-1, 1].
   * Negative boost means demotion. Default is 0.0. Setting to 1.0 gives the
   * document a big promotion. However, it does not necessarily mean that the
   * boosted document will be the top result at all times, nor that other
   * documents will be excluded. Results could still be shown even when none of
   * them matches the condition. And results that are significantly more
   * relevant to the search query can still trump your heavily favored but
   * irrelevant documents. Setting to -1.0 gives the document a big demotion.
   * However, results that are deeply relevant might still be shown. The
   * document will have an upstream battle to get a fairly high ranking, but it
   * is not blocked out completely. Setting to 0.0 means no boost applied. The
   * boosting condition is ignored.
   *
   * @var float
   */
  public $boost;
  protected $boostControlSpecType = GoogleCloudDialogflowCxV3BoostSpecConditionBoostSpecBoostControlSpec::class;
  protected $boostControlSpecDataType = '';
  /**
   * Optional. An expression which specifies a boost condition. The syntax and
   * supported fields are the same as a filter expression. Examples: * To boost
   * documents with document ID "doc_1" or "doc_2", and color "Red" or "Blue": *
   * (id: ANY("doc_1", "doc_2")) AND (color: ANY("Red","Blue"))
   *
   * @var string
   */
  public $condition;

  /**
   * Optional. Strength of the condition boost, which should be in [-1, 1].
   * Negative boost means demotion. Default is 0.0. Setting to 1.0 gives the
   * document a big promotion. However, it does not necessarily mean that the
   * boosted document will be the top result at all times, nor that other
   * documents will be excluded. Results could still be shown even when none of
   * them matches the condition. And results that are significantly more
   * relevant to the search query can still trump your heavily favored but
   * irrelevant documents. Setting to -1.0 gives the document a big demotion.
   * However, results that are deeply relevant might still be shown. The
   * document will have an upstream battle to get a fairly high ranking, but it
   * is not blocked out completely. Setting to 0.0 means no boost applied. The
   * boosting condition is ignored.
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
   * Optional. Complex specification for custom ranking based on customer
   * defined attribute value.
   *
   * @param GoogleCloudDialogflowCxV3BoostSpecConditionBoostSpecBoostControlSpec $boostControlSpec
   */
  public function setBoostControlSpec(GoogleCloudDialogflowCxV3BoostSpecConditionBoostSpecBoostControlSpec $boostControlSpec)
  {
    $this->boostControlSpec = $boostControlSpec;
  }
  /**
   * @return GoogleCloudDialogflowCxV3BoostSpecConditionBoostSpecBoostControlSpec
   */
  public function getBoostControlSpec()
  {
    return $this->boostControlSpec;
  }
  /**
   * Optional. An expression which specifies a boost condition. The syntax and
   * supported fields are the same as a filter expression. Examples: * To boost
   * documents with document ID "doc_1" or "doc_2", and color "Red" or "Blue": *
   * (id: ANY("doc_1", "doc_2")) AND (color: ANY("Red","Blue"))
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
class_alias(GoogleCloudDialogflowCxV3BoostSpecConditionBoostSpec::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3BoostSpecConditionBoostSpec');
