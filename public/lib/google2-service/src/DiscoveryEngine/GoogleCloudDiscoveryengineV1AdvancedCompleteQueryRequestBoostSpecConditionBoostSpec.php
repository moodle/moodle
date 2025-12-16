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

class GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequestBoostSpecConditionBoostSpec extends \Google\Model
{
  /**
   * Strength of the boost, which should be in [-1, 1]. Negative boost means
   * demotion. Default is 0.0. Setting to 1.0 gives the suggestions a big
   * promotion. However, it does not necessarily mean that the top result will
   * be a boosted suggestion. Setting to -1.0 gives the suggestions a big
   * demotion. However, other suggestions that are relevant might still be
   * shown. Setting to 0.0 means no boost applied. The boosting condition is
   * ignored.
   *
   * @var float
   */
  public $boost;
  /**
   * An expression which specifies a boost condition. The syntax is the same as
   * [filter expression syntax](https://cloud.google.com/generative-ai-app-
   * builder/docs/filter-search-metadata#filter-expression-syntax). Currently,
   * the only supported condition is a list of BCP-47 lang codes. Example: * To
   * boost suggestions in languages `en` or `fr`: `(lang_code: ANY("en", "fr"))`
   *
   * @var string
   */
  public $condition;

  /**
   * Strength of the boost, which should be in [-1, 1]. Negative boost means
   * demotion. Default is 0.0. Setting to 1.0 gives the suggestions a big
   * promotion. However, it does not necessarily mean that the top result will
   * be a boosted suggestion. Setting to -1.0 gives the suggestions a big
   * demotion. However, other suggestions that are relevant might still be
   * shown. Setting to 0.0 means no boost applied. The boosting condition is
   * ignored.
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
   * An expression which specifies a boost condition. The syntax is the same as
   * [filter expression syntax](https://cloud.google.com/generative-ai-app-
   * builder/docs/filter-search-metadata#filter-expression-syntax). Currently,
   * the only supported condition is a list of BCP-47 lang codes. Example: * To
   * boost suggestions in languages `en` or `fr`: `(lang_code: ANY("en", "fr"))`
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
class_alias(GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequestBoostSpecConditionBoostSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequestBoostSpecConditionBoostSpec');
