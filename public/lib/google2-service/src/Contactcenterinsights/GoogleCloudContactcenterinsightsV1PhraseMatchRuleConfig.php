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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1PhraseMatchRuleConfig extends \Google\Model
{
  protected $exactMatchConfigType = GoogleCloudContactcenterinsightsV1ExactMatchConfig::class;
  protected $exactMatchConfigDataType = '';
  protected $regexMatchConfigType = GoogleCloudContactcenterinsightsV1RegexMatchConfig::class;
  protected $regexMatchConfigDataType = '';

  /**
   * The configuration for the exact match rule.
   *
   * @param GoogleCloudContactcenterinsightsV1ExactMatchConfig $exactMatchConfig
   */
  public function setExactMatchConfig(GoogleCloudContactcenterinsightsV1ExactMatchConfig $exactMatchConfig)
  {
    $this->exactMatchConfig = $exactMatchConfig;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1ExactMatchConfig
   */
  public function getExactMatchConfig()
  {
    return $this->exactMatchConfig;
  }
  /**
   * The configuration for the regex match rule.
   *
   * @param GoogleCloudContactcenterinsightsV1RegexMatchConfig $regexMatchConfig
   */
  public function setRegexMatchConfig(GoogleCloudContactcenterinsightsV1RegexMatchConfig $regexMatchConfig)
  {
    $this->regexMatchConfig = $regexMatchConfig;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1RegexMatchConfig
   */
  public function getRegexMatchConfig()
  {
    return $this->regexMatchConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1PhraseMatchRuleConfig::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1PhraseMatchRuleConfig');
