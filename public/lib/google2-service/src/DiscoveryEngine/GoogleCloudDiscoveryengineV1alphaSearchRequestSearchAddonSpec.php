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

class GoogleCloudDiscoveryengineV1alphaSearchRequestSearchAddonSpec extends \Google\Model
{
  /**
   * Optional. If true, generative answer add-on is disabled. Generative answer
   * add-on includes natural language to filters and simple answers.
   *
   * @var bool
   */
  public $disableGenerativeAnswerAddOn;
  /**
   * Optional. If true, disables event re-ranking and personalization to
   * optimize KPIs & personalize results.
   *
   * @var bool
   */
  public $disableKpiPersonalizationAddOn;
  /**
   * Optional. If true, semantic add-on is disabled. Semantic add-on includes
   * embeddings and jetstream.
   *
   * @var bool
   */
  public $disableSemanticAddOn;

  /**
   * Optional. If true, generative answer add-on is disabled. Generative answer
   * add-on includes natural language to filters and simple answers.
   *
   * @param bool $disableGenerativeAnswerAddOn
   */
  public function setDisableGenerativeAnswerAddOn($disableGenerativeAnswerAddOn)
  {
    $this->disableGenerativeAnswerAddOn = $disableGenerativeAnswerAddOn;
  }
  /**
   * @return bool
   */
  public function getDisableGenerativeAnswerAddOn()
  {
    return $this->disableGenerativeAnswerAddOn;
  }
  /**
   * Optional. If true, disables event re-ranking and personalization to
   * optimize KPIs & personalize results.
   *
   * @param bool $disableKpiPersonalizationAddOn
   */
  public function setDisableKpiPersonalizationAddOn($disableKpiPersonalizationAddOn)
  {
    $this->disableKpiPersonalizationAddOn = $disableKpiPersonalizationAddOn;
  }
  /**
   * @return bool
   */
  public function getDisableKpiPersonalizationAddOn()
  {
    return $this->disableKpiPersonalizationAddOn;
  }
  /**
   * Optional. If true, semantic add-on is disabled. Semantic add-on includes
   * embeddings and jetstream.
   *
   * @param bool $disableSemanticAddOn
   */
  public function setDisableSemanticAddOn($disableSemanticAddOn)
  {
    $this->disableSemanticAddOn = $disableSemanticAddOn;
  }
  /**
   * @return bool
   */
  public function getDisableSemanticAddOn()
  {
    return $this->disableSemanticAddOn;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaSearchRequestSearchAddonSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaSearchRequestSearchAddonSpec');
