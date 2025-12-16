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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2CustomInfoType extends \Google\Collection
{
  /**
   * A finding of this custom info type will not be excluded from results.
   */
  public const EXCLUSION_TYPE_EXCLUSION_TYPE_UNSPECIFIED = 'EXCLUSION_TYPE_UNSPECIFIED';
  /**
   * A finding of this custom info type will be excluded from final results, but
   * can still affect rule execution.
   */
  public const EXCLUSION_TYPE_EXCLUSION_TYPE_EXCLUDE = 'EXCLUSION_TYPE_EXCLUDE';
  /**
   * Default value; same as POSSIBLE.
   */
  public const LIKELIHOOD_LIKELIHOOD_UNSPECIFIED = 'LIKELIHOOD_UNSPECIFIED';
  /**
   * Highest chance of a false positive.
   */
  public const LIKELIHOOD_VERY_UNLIKELY = 'VERY_UNLIKELY';
  /**
   * High chance of a false positive.
   */
  public const LIKELIHOOD_UNLIKELY = 'UNLIKELY';
  /**
   * Some matching signals. The default value.
   */
  public const LIKELIHOOD_POSSIBLE = 'POSSIBLE';
  /**
   * Low chance of a false positive.
   */
  public const LIKELIHOOD_LIKELY = 'LIKELY';
  /**
   * Confidence level is high. Lowest chance of a false positive.
   */
  public const LIKELIHOOD_VERY_LIKELY = 'VERY_LIKELY';
  protected $collection_key = 'detectionRules';
  protected $detectionRulesType = GooglePrivacyDlpV2DetectionRule::class;
  protected $detectionRulesDataType = 'array';
  protected $dictionaryType = GooglePrivacyDlpV2Dictionary::class;
  protected $dictionaryDataType = '';
  /**
   * If set to EXCLUSION_TYPE_EXCLUDE this infoType will not cause a finding to
   * be returned. It still can be used for rules matching.
   *
   * @var string
   */
  public $exclusionType;
  protected $infoTypeType = GooglePrivacyDlpV2InfoType::class;
  protected $infoTypeDataType = '';
  /**
   * Likelihood to return for this CustomInfoType. This base value can be
   * altered by a detection rule if the finding meets the criteria specified by
   * the rule. Defaults to `VERY_LIKELY` if not specified.
   *
   * @var string
   */
  public $likelihood;
  protected $regexType = GooglePrivacyDlpV2Regex::class;
  protected $regexDataType = '';
  protected $sensitivityScoreType = GooglePrivacyDlpV2SensitivityScore::class;
  protected $sensitivityScoreDataType = '';
  protected $storedTypeType = GooglePrivacyDlpV2StoredType::class;
  protected $storedTypeDataType = '';
  protected $surrogateTypeType = GooglePrivacyDlpV2SurrogateType::class;
  protected $surrogateTypeDataType = '';

  /**
   * Set of detection rules to apply to all findings of this CustomInfoType.
   * Rules are applied in order that they are specified. Not supported for the
   * `surrogate_type` CustomInfoType.
   *
   * @param GooglePrivacyDlpV2DetectionRule[] $detectionRules
   */
  public function setDetectionRules($detectionRules)
  {
    $this->detectionRules = $detectionRules;
  }
  /**
   * @return GooglePrivacyDlpV2DetectionRule[]
   */
  public function getDetectionRules()
  {
    return $this->detectionRules;
  }
  /**
   * A list of phrases to detect as a CustomInfoType.
   *
   * @param GooglePrivacyDlpV2Dictionary $dictionary
   */
  public function setDictionary(GooglePrivacyDlpV2Dictionary $dictionary)
  {
    $this->dictionary = $dictionary;
  }
  /**
   * @return GooglePrivacyDlpV2Dictionary
   */
  public function getDictionary()
  {
    return $this->dictionary;
  }
  /**
   * If set to EXCLUSION_TYPE_EXCLUDE this infoType will not cause a finding to
   * be returned. It still can be used for rules matching.
   *
   * Accepted values: EXCLUSION_TYPE_UNSPECIFIED, EXCLUSION_TYPE_EXCLUDE
   *
   * @param self::EXCLUSION_TYPE_* $exclusionType
   */
  public function setExclusionType($exclusionType)
  {
    $this->exclusionType = $exclusionType;
  }
  /**
   * @return self::EXCLUSION_TYPE_*
   */
  public function getExclusionType()
  {
    return $this->exclusionType;
  }
  /**
   * CustomInfoType can either be a new infoType, or an extension of built-in
   * infoType, when the name matches one of existing infoTypes and that infoType
   * is specified in `InspectContent.info_types` field. Specifying the latter
   * adds findings to the one detected by the system. If built-in info type is
   * not specified in `InspectContent.info_types` list then the name is treated
   * as a custom info type.
   *
   * @param GooglePrivacyDlpV2InfoType $infoType
   */
  public function setInfoType(GooglePrivacyDlpV2InfoType $infoType)
  {
    $this->infoType = $infoType;
  }
  /**
   * @return GooglePrivacyDlpV2InfoType
   */
  public function getInfoType()
  {
    return $this->infoType;
  }
  /**
   * Likelihood to return for this CustomInfoType. This base value can be
   * altered by a detection rule if the finding meets the criteria specified by
   * the rule. Defaults to `VERY_LIKELY` if not specified.
   *
   * Accepted values: LIKELIHOOD_UNSPECIFIED, VERY_UNLIKELY, UNLIKELY, POSSIBLE,
   * LIKELY, VERY_LIKELY
   *
   * @param self::LIKELIHOOD_* $likelihood
   */
  public function setLikelihood($likelihood)
  {
    $this->likelihood = $likelihood;
  }
  /**
   * @return self::LIKELIHOOD_*
   */
  public function getLikelihood()
  {
    return $this->likelihood;
  }
  /**
   * Regular expression based CustomInfoType.
   *
   * @param GooglePrivacyDlpV2Regex $regex
   */
  public function setRegex(GooglePrivacyDlpV2Regex $regex)
  {
    $this->regex = $regex;
  }
  /**
   * @return GooglePrivacyDlpV2Regex
   */
  public function getRegex()
  {
    return $this->regex;
  }
  /**
   * Sensitivity for this CustomInfoType. If this CustomInfoType extends an
   * existing InfoType, the sensitivity here will take precedence over that of
   * the original InfoType. If unset for a CustomInfoType, it will default to
   * HIGH. This only applies to data profiling.
   *
   * @param GooglePrivacyDlpV2SensitivityScore $sensitivityScore
   */
  public function setSensitivityScore(GooglePrivacyDlpV2SensitivityScore $sensitivityScore)
  {
    $this->sensitivityScore = $sensitivityScore;
  }
  /**
   * @return GooglePrivacyDlpV2SensitivityScore
   */
  public function getSensitivityScore()
  {
    return $this->sensitivityScore;
  }
  /**
   * Load an existing `StoredInfoType` resource for use in `InspectDataSource`.
   * Not currently supported in `InspectContent`.
   *
   * @param GooglePrivacyDlpV2StoredType $storedType
   */
  public function setStoredType(GooglePrivacyDlpV2StoredType $storedType)
  {
    $this->storedType = $storedType;
  }
  /**
   * @return GooglePrivacyDlpV2StoredType
   */
  public function getStoredType()
  {
    return $this->storedType;
  }
  /**
   * Message for detecting output from deidentification transformations that
   * support reversing.
   *
   * @param GooglePrivacyDlpV2SurrogateType $surrogateType
   */
  public function setSurrogateType(GooglePrivacyDlpV2SurrogateType $surrogateType)
  {
    $this->surrogateType = $surrogateType;
  }
  /**
   * @return GooglePrivacyDlpV2SurrogateType
   */
  public function getSurrogateType()
  {
    return $this->surrogateType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2CustomInfoType::class, 'Google_Service_DLP_GooglePrivacyDlpV2CustomInfoType');
