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

namespace Google\Service\DisplayVideo;

class AlgorithmRules extends \Google\Model
{
  /**
   * Attribution model for the algorithm. This field is only supported for
   * allowlisted partners.
   *
   * @var string
   */
  public $attributionModelId;
  protected $impressionSignalRulesetType = AlgorithmRulesRuleset::class;
  protected $impressionSignalRulesetDataType = '';
  protected $postImpressionSignalRulesetType = AlgorithmRulesRuleset::class;
  protected $postImpressionSignalRulesetDataType = '';

  /**
   * Attribution model for the algorithm. This field is only supported for
   * allowlisted partners.
   *
   * @param string $attributionModelId
   */
  public function setAttributionModelId($attributionModelId)
  {
    $this->attributionModelId = $attributionModelId;
  }
  /**
   * @return string
   */
  public function getAttributionModelId()
  {
    return $this->attributionModelId;
  }
  /**
   * Rules for the impression signals.
   *
   * @param AlgorithmRulesRuleset $impressionSignalRuleset
   */
  public function setImpressionSignalRuleset(AlgorithmRulesRuleset $impressionSignalRuleset)
  {
    $this->impressionSignalRuleset = $impressionSignalRuleset;
  }
  /**
   * @return AlgorithmRulesRuleset
   */
  public function getImpressionSignalRuleset()
  {
    return $this->impressionSignalRuleset;
  }
  /**
   * Rules for the post-impression signals. This field is only supported for
   * allowlisted partners.
   *
   * @param AlgorithmRulesRuleset $postImpressionSignalRuleset
   */
  public function setPostImpressionSignalRuleset(AlgorithmRulesRuleset $postImpressionSignalRuleset)
  {
    $this->postImpressionSignalRuleset = $postImpressionSignalRuleset;
  }
  /**
   * @return AlgorithmRulesRuleset
   */
  public function getPostImpressionSignalRuleset()
  {
    return $this->postImpressionSignalRuleset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AlgorithmRules::class, 'Google_Service_DisplayVideo_AlgorithmRules');
