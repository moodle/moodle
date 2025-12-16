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

class GooglePrivacyDlpV2InspectConfig extends \Google\Collection
{
  /**
   * Default value; same as POSSIBLE.
   */
  public const MIN_LIKELIHOOD_LIKELIHOOD_UNSPECIFIED = 'LIKELIHOOD_UNSPECIFIED';
  /**
   * Highest chance of a false positive.
   */
  public const MIN_LIKELIHOOD_VERY_UNLIKELY = 'VERY_UNLIKELY';
  /**
   * High chance of a false positive.
   */
  public const MIN_LIKELIHOOD_UNLIKELY = 'UNLIKELY';
  /**
   * Some matching signals. The default value.
   */
  public const MIN_LIKELIHOOD_POSSIBLE = 'POSSIBLE';
  /**
   * Low chance of a false positive.
   */
  public const MIN_LIKELIHOOD_LIKELY = 'LIKELY';
  /**
   * Confidence level is high. Lowest chance of a false positive.
   */
  public const MIN_LIKELIHOOD_VERY_LIKELY = 'VERY_LIKELY';
  protected $collection_key = 'ruleSet';
  /**
   * Deprecated and unused.
   *
   * @var string[]
   */
  public $contentOptions;
  protected $customInfoTypesType = GooglePrivacyDlpV2CustomInfoType::class;
  protected $customInfoTypesDataType = 'array';
  /**
   * When true, excludes type information of the findings. This is not used for
   * data profiling.
   *
   * @var bool
   */
  public $excludeInfoTypes;
  /**
   * When true, a contextual quote from the data that triggered a finding is
   * included in the response; see Finding.quote. This is not used for data
   * profiling.
   *
   * @var bool
   */
  public $includeQuote;
  protected $infoTypesType = GooglePrivacyDlpV2InfoType::class;
  protected $infoTypesDataType = 'array';
  protected $limitsType = GooglePrivacyDlpV2FindingLimits::class;
  protected $limitsDataType = '';
  /**
   * Only returns findings equal to or above this threshold. The default is
   * POSSIBLE. In general, the highest likelihood setting yields the fewest
   * findings in results and the lowest chance of a false positive. For more
   * information, see [Match likelihood](https://cloud.google.com/sensitive-
   * data-protection/docs/likelihood).
   *
   * @var string
   */
  public $minLikelihood;
  protected $minLikelihoodPerInfoTypeType = GooglePrivacyDlpV2InfoTypeLikelihood::class;
  protected $minLikelihoodPerInfoTypeDataType = 'array';
  protected $ruleSetType = GooglePrivacyDlpV2InspectionRuleSet::class;
  protected $ruleSetDataType = 'array';

  /**
   * Deprecated and unused.
   *
   * @param string[] $contentOptions
   */
  public function setContentOptions($contentOptions)
  {
    $this->contentOptions = $contentOptions;
  }
  /**
   * @return string[]
   */
  public function getContentOptions()
  {
    return $this->contentOptions;
  }
  /**
   * CustomInfoTypes provided by the user. See
   * https://cloud.google.com/sensitive-data-protection/docs/creating-custom-
   * infotypes to learn more.
   *
   * @param GooglePrivacyDlpV2CustomInfoType[] $customInfoTypes
   */
  public function setCustomInfoTypes($customInfoTypes)
  {
    $this->customInfoTypes = $customInfoTypes;
  }
  /**
   * @return GooglePrivacyDlpV2CustomInfoType[]
   */
  public function getCustomInfoTypes()
  {
    return $this->customInfoTypes;
  }
  /**
   * When true, excludes type information of the findings. This is not used for
   * data profiling.
   *
   * @param bool $excludeInfoTypes
   */
  public function setExcludeInfoTypes($excludeInfoTypes)
  {
    $this->excludeInfoTypes = $excludeInfoTypes;
  }
  /**
   * @return bool
   */
  public function getExcludeInfoTypes()
  {
    return $this->excludeInfoTypes;
  }
  /**
   * When true, a contextual quote from the data that triggered a finding is
   * included in the response; see Finding.quote. This is not used for data
   * profiling.
   *
   * @param bool $includeQuote
   */
  public function setIncludeQuote($includeQuote)
  {
    $this->includeQuote = $includeQuote;
  }
  /**
   * @return bool
   */
  public function getIncludeQuote()
  {
    return $this->includeQuote;
  }
  /**
   * Restricts what info_types to look for. The values must correspond to
   * InfoType values returned by ListInfoTypes or listed at
   * https://cloud.google.com/sensitive-data-protection/docs/infotypes-
   * reference. When no InfoTypes or CustomInfoTypes are specified in a request,
   * the system may automatically choose a default list of detectors to run,
   * which may change over time. If you need precise control and predictability
   * as to what detectors are run you should specify specific InfoTypes listed
   * in the reference, otherwise a default list will be used, which may change
   * over time.
   *
   * @param GooglePrivacyDlpV2InfoType[] $infoTypes
   */
  public function setInfoTypes($infoTypes)
  {
    $this->infoTypes = $infoTypes;
  }
  /**
   * @return GooglePrivacyDlpV2InfoType[]
   */
  public function getInfoTypes()
  {
    return $this->infoTypes;
  }
  /**
   * Configuration to control the number of findings returned. This is not used
   * for data profiling. When redacting sensitive data from images, finding
   * limits don't apply. They can cause unexpected or inconsistent results,
   * where only some data is redacted. Don't include finding limits in
   * RedactImage requests. Otherwise, Cloud DLP returns an error. When set
   * within an InspectJobConfig, the specified maximum values aren't hard
   * limits. If an inspection job reaches these limits, the job ends gradually,
   * not abruptly. Therefore, the actual number of findings that Cloud DLP
   * returns can be multiple times higher than these maximum values.
   *
   * @param GooglePrivacyDlpV2FindingLimits $limits
   */
  public function setLimits(GooglePrivacyDlpV2FindingLimits $limits)
  {
    $this->limits = $limits;
  }
  /**
   * @return GooglePrivacyDlpV2FindingLimits
   */
  public function getLimits()
  {
    return $this->limits;
  }
  /**
   * Only returns findings equal to or above this threshold. The default is
   * POSSIBLE. In general, the highest likelihood setting yields the fewest
   * findings in results and the lowest chance of a false positive. For more
   * information, see [Match likelihood](https://cloud.google.com/sensitive-
   * data-protection/docs/likelihood).
   *
   * Accepted values: LIKELIHOOD_UNSPECIFIED, VERY_UNLIKELY, UNLIKELY, POSSIBLE,
   * LIKELY, VERY_LIKELY
   *
   * @param self::MIN_LIKELIHOOD_* $minLikelihood
   */
  public function setMinLikelihood($minLikelihood)
  {
    $this->minLikelihood = $minLikelihood;
  }
  /**
   * @return self::MIN_LIKELIHOOD_*
   */
  public function getMinLikelihood()
  {
    return $this->minLikelihood;
  }
  /**
   * Minimum likelihood per infotype. For each infotype, a user can specify a
   * minimum likelihood. The system only returns a finding if its likelihood is
   * above this threshold. If this field is not set, the system uses the
   * InspectConfig min_likelihood.
   *
   * @param GooglePrivacyDlpV2InfoTypeLikelihood[] $minLikelihoodPerInfoType
   */
  public function setMinLikelihoodPerInfoType($minLikelihoodPerInfoType)
  {
    $this->minLikelihoodPerInfoType = $minLikelihoodPerInfoType;
  }
  /**
   * @return GooglePrivacyDlpV2InfoTypeLikelihood[]
   */
  public function getMinLikelihoodPerInfoType()
  {
    return $this->minLikelihoodPerInfoType;
  }
  /**
   * Set of rules to apply to the findings for this InspectConfig. Exclusion
   * rules, contained in the set are executed in the end, other rules are
   * executed in the order they are specified for each info type.
   *
   * @param GooglePrivacyDlpV2InspectionRuleSet[] $ruleSet
   */
  public function setRuleSet($ruleSet)
  {
    $this->ruleSet = $ruleSet;
  }
  /**
   * @return GooglePrivacyDlpV2InspectionRuleSet[]
   */
  public function getRuleSet()
  {
    return $this->ruleSet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2InspectConfig::class, 'Google_Service_DLP_GooglePrivacyDlpV2InspectConfig');
