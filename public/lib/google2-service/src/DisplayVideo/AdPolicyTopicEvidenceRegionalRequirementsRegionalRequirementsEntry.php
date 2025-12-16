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

class AdPolicyTopicEvidenceRegionalRequirementsRegionalRequirementsEntry extends \Google\Collection
{
  protected $collection_key = 'countryRestrictions';
  protected $countryRestrictionsType = AdPolicyCriterionRestriction::class;
  protected $countryRestrictionsDataType = 'array';
  /**
   * The legal policy that is being violated.
   *
   * @var string
   */
  public $legalPolicy;

  /**
   * The countries restricted due to the legal policy.
   *
   * @param AdPolicyCriterionRestriction[] $countryRestrictions
   */
  public function setCountryRestrictions($countryRestrictions)
  {
    $this->countryRestrictions = $countryRestrictions;
  }
  /**
   * @return AdPolicyCriterionRestriction[]
   */
  public function getCountryRestrictions()
  {
    return $this->countryRestrictions;
  }
  /**
   * The legal policy that is being violated.
   *
   * @param string $legalPolicy
   */
  public function setLegalPolicy($legalPolicy)
  {
    $this->legalPolicy = $legalPolicy;
  }
  /**
   * @return string
   */
  public function getLegalPolicy()
  {
    return $this->legalPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdPolicyTopicEvidenceRegionalRequirementsRegionalRequirementsEntry::class, 'Google_Service_DisplayVideo_AdPolicyTopicEvidenceRegionalRequirementsRegionalRequirementsEntry');
