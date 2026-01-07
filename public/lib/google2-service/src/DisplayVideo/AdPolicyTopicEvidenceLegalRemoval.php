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

class AdPolicyTopicEvidenceLegalRemoval extends \Google\Collection
{
  /**
   * Not specified or unknown.
   */
  public const COMPLAINT_TYPE_AD_POLICY_TOPIC_EVIDENCE_LEGAL_REMOVAL_COMPLAINT_TYPE_UNKNOWN = 'AD_POLICY_TOPIC_EVIDENCE_LEGAL_REMOVAL_COMPLAINT_TYPE_UNKNOWN';
  /**
   * Copyright. Only applies to DMCA.
   */
  public const COMPLAINT_TYPE_COPYRIGHT = 'COPYRIGHT';
  /**
   * Court order. Only applies to local legal.
   */
  public const COMPLAINT_TYPE_COURT_ORDER = 'COURT_ORDER';
  /**
   * Local legal regulation. Only applies to local legal.
   */
  public const COMPLAINT_TYPE_LOCAL_LEGAL = 'LOCAL_LEGAL';
  protected $collection_key = 'restrictedUris';
  /**
   * The type of complaint causing the legal removal.
   *
   * @var string
   */
  public $complaintType;
  protected $countryRestrictionsType = AdPolicyCriterionRestriction::class;
  protected $countryRestrictionsDataType = 'array';
  protected $dmcaType = AdPolicyTopicEvidenceLegalRemovalDmca::class;
  protected $dmcaDataType = '';
  protected $localLegalType = AdPolicyTopicEvidenceLegalRemovalLocalLegal::class;
  protected $localLegalDataType = '';
  /**
   * The urls restricted due to the legal removal.
   *
   * @var string[]
   */
  public $restrictedUris;

  /**
   * The type of complaint causing the legal removal.
   *
   * Accepted values:
   * AD_POLICY_TOPIC_EVIDENCE_LEGAL_REMOVAL_COMPLAINT_TYPE_UNKNOWN, COPYRIGHT,
   * COURT_ORDER, LOCAL_LEGAL
   *
   * @param self::COMPLAINT_TYPE_* $complaintType
   */
  public function setComplaintType($complaintType)
  {
    $this->complaintType = $complaintType;
  }
  /**
   * @return self::COMPLAINT_TYPE_*
   */
  public function getComplaintType()
  {
    return $this->complaintType;
  }
  /**
   * The countries restricted due to the legal removal.
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
   * Details on the DMCA regulation legal removal.
   *
   * @param AdPolicyTopicEvidenceLegalRemovalDmca $dmca
   */
  public function setDmca(AdPolicyTopicEvidenceLegalRemovalDmca $dmca)
  {
    $this->dmca = $dmca;
  }
  /**
   * @return AdPolicyTopicEvidenceLegalRemovalDmca
   */
  public function getDmca()
  {
    return $this->dmca;
  }
  /**
   * Details on the local legal regulation legal removal.
   *
   * @param AdPolicyTopicEvidenceLegalRemovalLocalLegal $localLegal
   */
  public function setLocalLegal(AdPolicyTopicEvidenceLegalRemovalLocalLegal $localLegal)
  {
    $this->localLegal = $localLegal;
  }
  /**
   * @return AdPolicyTopicEvidenceLegalRemovalLocalLegal
   */
  public function getLocalLegal()
  {
    return $this->localLegal;
  }
  /**
   * The urls restricted due to the legal removal.
   *
   * @param string[] $restrictedUris
   */
  public function setRestrictedUris($restrictedUris)
  {
    $this->restrictedUris = $restrictedUris;
  }
  /**
   * @return string[]
   */
  public function getRestrictedUris()
  {
    return $this->restrictedUris;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdPolicyTopicEvidenceLegalRemoval::class, 'Google_Service_DisplayVideo_AdPolicyTopicEvidenceLegalRemoval');
