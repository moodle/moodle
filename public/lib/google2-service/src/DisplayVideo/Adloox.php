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

class Adloox extends \Google\Collection
{
  /**
   * This enum is only a placeholder and it doesn't specify any GARM risk
   * exclusion option.
   */
  public const ADULT_EXPLICIT_SEXUAL_CONTENT_GARM_RISK_EXCLUSION_UNSPECIFIED = 'GARM_RISK_EXCLUSION_UNSPECIFIED';
  /**
   * Exclude floor risk.
   */
  public const ADULT_EXPLICIT_SEXUAL_CONTENT_GARM_RISK_EXCLUSION_FLOOR = 'GARM_RISK_EXCLUSION_FLOOR';
  /**
   * Exclude high and floor risk.
   */
  public const ADULT_EXPLICIT_SEXUAL_CONTENT_GARM_RISK_EXCLUSION_HIGH = 'GARM_RISK_EXCLUSION_HIGH';
  /**
   * Exclude medium, high, and floor risk.
   */
  public const ADULT_EXPLICIT_SEXUAL_CONTENT_GARM_RISK_EXCLUSION_MEDIUM = 'GARM_RISK_EXCLUSION_MEDIUM';
  /**
   * Exclude all levels of risk (low, medium, high and floor).
   */
  public const ADULT_EXPLICIT_SEXUAL_CONTENT_GARM_RISK_EXCLUSION_LOW = 'GARM_RISK_EXCLUSION_LOW';
  /**
   * This enum is only a placeholder and it doesn't specify any GARM risk
   * exclusion option.
   */
  public const ARMS_AMMUNITION_CONTENT_GARM_RISK_EXCLUSION_UNSPECIFIED = 'GARM_RISK_EXCLUSION_UNSPECIFIED';
  /**
   * Exclude floor risk.
   */
  public const ARMS_AMMUNITION_CONTENT_GARM_RISK_EXCLUSION_FLOOR = 'GARM_RISK_EXCLUSION_FLOOR';
  /**
   * Exclude high and floor risk.
   */
  public const ARMS_AMMUNITION_CONTENT_GARM_RISK_EXCLUSION_HIGH = 'GARM_RISK_EXCLUSION_HIGH';
  /**
   * Exclude medium, high, and floor risk.
   */
  public const ARMS_AMMUNITION_CONTENT_GARM_RISK_EXCLUSION_MEDIUM = 'GARM_RISK_EXCLUSION_MEDIUM';
  /**
   * Exclude all levels of risk (low, medium, high and floor).
   */
  public const ARMS_AMMUNITION_CONTENT_GARM_RISK_EXCLUSION_LOW = 'GARM_RISK_EXCLUSION_LOW';
  /**
   * This enum is only a placeholder and it doesn't specify any GARM risk
   * exclusion option.
   */
  public const CRIME_HARMFUL_ACTS_INDIVIDUALS_SOCIETY_HUMAN_RIGHTS_VIOLATIONS_CONTENT_GARM_RISK_EXCLUSION_UNSPECIFIED = 'GARM_RISK_EXCLUSION_UNSPECIFIED';
  /**
   * Exclude floor risk.
   */
  public const CRIME_HARMFUL_ACTS_INDIVIDUALS_SOCIETY_HUMAN_RIGHTS_VIOLATIONS_CONTENT_GARM_RISK_EXCLUSION_FLOOR = 'GARM_RISK_EXCLUSION_FLOOR';
  /**
   * Exclude high and floor risk.
   */
  public const CRIME_HARMFUL_ACTS_INDIVIDUALS_SOCIETY_HUMAN_RIGHTS_VIOLATIONS_CONTENT_GARM_RISK_EXCLUSION_HIGH = 'GARM_RISK_EXCLUSION_HIGH';
  /**
   * Exclude medium, high, and floor risk.
   */
  public const CRIME_HARMFUL_ACTS_INDIVIDUALS_SOCIETY_HUMAN_RIGHTS_VIOLATIONS_CONTENT_GARM_RISK_EXCLUSION_MEDIUM = 'GARM_RISK_EXCLUSION_MEDIUM';
  /**
   * Exclude all levels of risk (low, medium, high and floor).
   */
  public const CRIME_HARMFUL_ACTS_INDIVIDUALS_SOCIETY_HUMAN_RIGHTS_VIOLATIONS_CONTENT_GARM_RISK_EXCLUSION_LOW = 'GARM_RISK_EXCLUSION_LOW';
  /**
   * This enum is only a placeholder and it doesn't specify any GARM risk
   * exclusion option.
   */
  public const DEATH_INJURY_MILITARY_CONFLICT_CONTENT_GARM_RISK_EXCLUSION_UNSPECIFIED = 'GARM_RISK_EXCLUSION_UNSPECIFIED';
  /**
   * Exclude floor risk.
   */
  public const DEATH_INJURY_MILITARY_CONFLICT_CONTENT_GARM_RISK_EXCLUSION_FLOOR = 'GARM_RISK_EXCLUSION_FLOOR';
  /**
   * Exclude high and floor risk.
   */
  public const DEATH_INJURY_MILITARY_CONFLICT_CONTENT_GARM_RISK_EXCLUSION_HIGH = 'GARM_RISK_EXCLUSION_HIGH';
  /**
   * Exclude medium, high, and floor risk.
   */
  public const DEATH_INJURY_MILITARY_CONFLICT_CONTENT_GARM_RISK_EXCLUSION_MEDIUM = 'GARM_RISK_EXCLUSION_MEDIUM';
  /**
   * Exclude all levels of risk (low, medium, high and floor).
   */
  public const DEATH_INJURY_MILITARY_CONFLICT_CONTENT_GARM_RISK_EXCLUSION_LOW = 'GARM_RISK_EXCLUSION_LOW';
  /**
   * This enum is only a placeholder and it doesn't specify any GARM risk
   * exclusion option.
   */
  public const DEBATED_SENSITIVE_SOCIAL_ISSUE_CONTENT_GARM_RISK_EXCLUSION_UNSPECIFIED = 'GARM_RISK_EXCLUSION_UNSPECIFIED';
  /**
   * Exclude floor risk.
   */
  public const DEBATED_SENSITIVE_SOCIAL_ISSUE_CONTENT_GARM_RISK_EXCLUSION_FLOOR = 'GARM_RISK_EXCLUSION_FLOOR';
  /**
   * Exclude high and floor risk.
   */
  public const DEBATED_SENSITIVE_SOCIAL_ISSUE_CONTENT_GARM_RISK_EXCLUSION_HIGH = 'GARM_RISK_EXCLUSION_HIGH';
  /**
   * Exclude medium, high, and floor risk.
   */
  public const DEBATED_SENSITIVE_SOCIAL_ISSUE_CONTENT_GARM_RISK_EXCLUSION_MEDIUM = 'GARM_RISK_EXCLUSION_MEDIUM';
  /**
   * Exclude all levels of risk (low, medium, high and floor).
   */
  public const DEBATED_SENSITIVE_SOCIAL_ISSUE_CONTENT_GARM_RISK_EXCLUSION_LOW = 'GARM_RISK_EXCLUSION_LOW';
  /**
   * Default value when not specified or is unknown in this version.
   */
  public const DISPLAY_IAB_VIEWABILITY_DISPLAY_IAB_VIEWABILITY_UNSPECIFIED = 'DISPLAY_IAB_VIEWABILITY_UNSPECIFIED';
  /**
   * 10%+ in view (IAB display viewability standard).
   */
  public const DISPLAY_IAB_VIEWABILITY_DISPLAY_IAB_VIEWABILITY_10 = 'DISPLAY_IAB_VIEWABILITY_10';
  /**
   * 20%+ in view (IAB display viewability standard).
   */
  public const DISPLAY_IAB_VIEWABILITY_DISPLAY_IAB_VIEWABILITY_20 = 'DISPLAY_IAB_VIEWABILITY_20';
  /**
   * 35%+ in view (IAB display viewability standard).
   */
  public const DISPLAY_IAB_VIEWABILITY_DISPLAY_IAB_VIEWABILITY_35 = 'DISPLAY_IAB_VIEWABILITY_35';
  /**
   * 50%+ in view (IAB display viewability standard).
   */
  public const DISPLAY_IAB_VIEWABILITY_DISPLAY_IAB_VIEWABILITY_50 = 'DISPLAY_IAB_VIEWABILITY_50';
  /**
   * 75%+ in view (IAB display viewability standard).
   */
  public const DISPLAY_IAB_VIEWABILITY_DISPLAY_IAB_VIEWABILITY_75 = 'DISPLAY_IAB_VIEWABILITY_75';
  /**
   * This enum is only a placeholder and it doesn't specify any GARM risk
   * exclusion option.
   */
  public const HATE_SPEECH_ACTS_AGGRESSION_CONTENT_GARM_RISK_EXCLUSION_UNSPECIFIED = 'GARM_RISK_EXCLUSION_UNSPECIFIED';
  /**
   * Exclude floor risk.
   */
  public const HATE_SPEECH_ACTS_AGGRESSION_CONTENT_GARM_RISK_EXCLUSION_FLOOR = 'GARM_RISK_EXCLUSION_FLOOR';
  /**
   * Exclude high and floor risk.
   */
  public const HATE_SPEECH_ACTS_AGGRESSION_CONTENT_GARM_RISK_EXCLUSION_HIGH = 'GARM_RISK_EXCLUSION_HIGH';
  /**
   * Exclude medium, high, and floor risk.
   */
  public const HATE_SPEECH_ACTS_AGGRESSION_CONTENT_GARM_RISK_EXCLUSION_MEDIUM = 'GARM_RISK_EXCLUSION_MEDIUM';
  /**
   * Exclude all levels of risk (low, medium, high and floor).
   */
  public const HATE_SPEECH_ACTS_AGGRESSION_CONTENT_GARM_RISK_EXCLUSION_LOW = 'GARM_RISK_EXCLUSION_LOW';
  /**
   * This enum is only a placeholder and it doesn't specify any GARM risk
   * exclusion option.
   */
  public const ILLEGAL_DRUGS_TOBACCO_ECIGARETTES_VAPING_ALCOHOL_CONTENT_GARM_RISK_EXCLUSION_UNSPECIFIED = 'GARM_RISK_EXCLUSION_UNSPECIFIED';
  /**
   * Exclude floor risk.
   */
  public const ILLEGAL_DRUGS_TOBACCO_ECIGARETTES_VAPING_ALCOHOL_CONTENT_GARM_RISK_EXCLUSION_FLOOR = 'GARM_RISK_EXCLUSION_FLOOR';
  /**
   * Exclude high and floor risk.
   */
  public const ILLEGAL_DRUGS_TOBACCO_ECIGARETTES_VAPING_ALCOHOL_CONTENT_GARM_RISK_EXCLUSION_HIGH = 'GARM_RISK_EXCLUSION_HIGH';
  /**
   * Exclude medium, high, and floor risk.
   */
  public const ILLEGAL_DRUGS_TOBACCO_ECIGARETTES_VAPING_ALCOHOL_CONTENT_GARM_RISK_EXCLUSION_MEDIUM = 'GARM_RISK_EXCLUSION_MEDIUM';
  /**
   * Exclude all levels of risk (low, medium, high and floor).
   */
  public const ILLEGAL_DRUGS_TOBACCO_ECIGARETTES_VAPING_ALCOHOL_CONTENT_GARM_RISK_EXCLUSION_LOW = 'GARM_RISK_EXCLUSION_LOW';
  /**
   * This enum is only a placeholder and it doesn't specify any GARM risk
   * exclusion option.
   */
  public const MISINFORMATION_CONTENT_GARM_RISK_EXCLUSION_UNSPECIFIED = 'GARM_RISK_EXCLUSION_UNSPECIFIED';
  /**
   * Exclude floor risk.
   */
  public const MISINFORMATION_CONTENT_GARM_RISK_EXCLUSION_FLOOR = 'GARM_RISK_EXCLUSION_FLOOR';
  /**
   * Exclude high and floor risk.
   */
  public const MISINFORMATION_CONTENT_GARM_RISK_EXCLUSION_HIGH = 'GARM_RISK_EXCLUSION_HIGH';
  /**
   * Exclude medium, high, and floor risk.
   */
  public const MISINFORMATION_CONTENT_GARM_RISK_EXCLUSION_MEDIUM = 'GARM_RISK_EXCLUSION_MEDIUM';
  /**
   * Exclude all levels of risk (low, medium, high and floor).
   */
  public const MISINFORMATION_CONTENT_GARM_RISK_EXCLUSION_LOW = 'GARM_RISK_EXCLUSION_LOW';
  /**
   * This enum is only a placeholder and it doesn't specify any GARM risk
   * exclusion option.
   */
  public const OBSCENITY_PROFANITY_CONTENT_GARM_RISK_EXCLUSION_UNSPECIFIED = 'GARM_RISK_EXCLUSION_UNSPECIFIED';
  /**
   * Exclude floor risk.
   */
  public const OBSCENITY_PROFANITY_CONTENT_GARM_RISK_EXCLUSION_FLOOR = 'GARM_RISK_EXCLUSION_FLOOR';
  /**
   * Exclude high and floor risk.
   */
  public const OBSCENITY_PROFANITY_CONTENT_GARM_RISK_EXCLUSION_HIGH = 'GARM_RISK_EXCLUSION_HIGH';
  /**
   * Exclude medium, high, and floor risk.
   */
  public const OBSCENITY_PROFANITY_CONTENT_GARM_RISK_EXCLUSION_MEDIUM = 'GARM_RISK_EXCLUSION_MEDIUM';
  /**
   * Exclude all levels of risk (low, medium, high and floor).
   */
  public const OBSCENITY_PROFANITY_CONTENT_GARM_RISK_EXCLUSION_LOW = 'GARM_RISK_EXCLUSION_LOW';
  /**
   * This enum is only a placeholder and it doesn't specify any GARM risk
   * exclusion option.
   */
  public const ONLINE_PIRACY_CONTENT_GARM_RISK_EXCLUSION_UNSPECIFIED = 'GARM_RISK_EXCLUSION_UNSPECIFIED';
  /**
   * Exclude floor risk.
   */
  public const ONLINE_PIRACY_CONTENT_GARM_RISK_EXCLUSION_FLOOR = 'GARM_RISK_EXCLUSION_FLOOR';
  /**
   * Exclude high and floor risk.
   */
  public const ONLINE_PIRACY_CONTENT_GARM_RISK_EXCLUSION_HIGH = 'GARM_RISK_EXCLUSION_HIGH';
  /**
   * Exclude medium, high, and floor risk.
   */
  public const ONLINE_PIRACY_CONTENT_GARM_RISK_EXCLUSION_MEDIUM = 'GARM_RISK_EXCLUSION_MEDIUM';
  /**
   * Exclude all levels of risk (low, medium, high and floor).
   */
  public const ONLINE_PIRACY_CONTENT_GARM_RISK_EXCLUSION_LOW = 'GARM_RISK_EXCLUSION_LOW';
  /**
   * This enum is only a placeholder and it doesn't specify any GARM risk
   * exclusion option.
   */
  public const SPAM_HARMFUL_CONTENT_GARM_RISK_EXCLUSION_UNSPECIFIED = 'GARM_RISK_EXCLUSION_UNSPECIFIED';
  /**
   * Exclude floor risk.
   */
  public const SPAM_HARMFUL_CONTENT_GARM_RISK_EXCLUSION_FLOOR = 'GARM_RISK_EXCLUSION_FLOOR';
  /**
   * Exclude high and floor risk.
   */
  public const SPAM_HARMFUL_CONTENT_GARM_RISK_EXCLUSION_HIGH = 'GARM_RISK_EXCLUSION_HIGH';
  /**
   * Exclude medium, high, and floor risk.
   */
  public const SPAM_HARMFUL_CONTENT_GARM_RISK_EXCLUSION_MEDIUM = 'GARM_RISK_EXCLUSION_MEDIUM';
  /**
   * Exclude all levels of risk (low, medium, high and floor).
   */
  public const SPAM_HARMFUL_CONTENT_GARM_RISK_EXCLUSION_LOW = 'GARM_RISK_EXCLUSION_LOW';
  /**
   * This enum is only a placeholder and it doesn't specify any GARM risk
   * exclusion option.
   */
  public const TERRORISM_CONTENT_GARM_RISK_EXCLUSION_UNSPECIFIED = 'GARM_RISK_EXCLUSION_UNSPECIFIED';
  /**
   * Exclude floor risk.
   */
  public const TERRORISM_CONTENT_GARM_RISK_EXCLUSION_FLOOR = 'GARM_RISK_EXCLUSION_FLOOR';
  /**
   * Exclude high and floor risk.
   */
  public const TERRORISM_CONTENT_GARM_RISK_EXCLUSION_HIGH = 'GARM_RISK_EXCLUSION_HIGH';
  /**
   * Exclude medium, high, and floor risk.
   */
  public const TERRORISM_CONTENT_GARM_RISK_EXCLUSION_MEDIUM = 'GARM_RISK_EXCLUSION_MEDIUM';
  /**
   * Exclude all levels of risk (low, medium, high and floor).
   */
  public const TERRORISM_CONTENT_GARM_RISK_EXCLUSION_LOW = 'GARM_RISK_EXCLUSION_LOW';
  /**
   * Default value when not specified or is unknown in this version.
   */
  public const VIDEO_IAB_VIEWABILITY_VIDEO_IAB_VIEWABILITY_UNSPECIFIED = 'VIDEO_IAB_VIEWABILITY_UNSPECIFIED';
  /**
   * 10%+ in view (IAB video viewability standard).
   */
  public const VIDEO_IAB_VIEWABILITY_VIDEO_IAB_VIEWABILITY_10 = 'VIDEO_IAB_VIEWABILITY_10';
  /**
   * 20%+ in view (IAB video viewability standard).
   */
  public const VIDEO_IAB_VIEWABILITY_VIDEO_IAB_VIEWABILITY_20 = 'VIDEO_IAB_VIEWABILITY_20';
  /**
   * 35%+ in view (IAB video viewability standard).
   */
  public const VIDEO_IAB_VIEWABILITY_VIDEO_IAB_VIEWABILITY_35 = 'VIDEO_IAB_VIEWABILITY_35';
  /**
   * 50%+ in view (IAB video viewability standard).
   */
  public const VIDEO_IAB_VIEWABILITY_VIDEO_IAB_VIEWABILITY_50 = 'VIDEO_IAB_VIEWABILITY_50';
  /**
   * 75%+ in view (IAB video viewability standard).
   */
  public const VIDEO_IAB_VIEWABILITY_VIDEO_IAB_VIEWABILITY_75 = 'VIDEO_IAB_VIEWABILITY_75';
  protected $collection_key = 'excludedFraudIvtMfaCategories';
  /**
   * Optional. Adult and Explicit Sexual Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * @var string
   */
  public $adultExplicitSexualContent;
  /**
   * Optional. Arms and Ammunition Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * @var string
   */
  public $armsAmmunitionContent;
  /**
   * Optional. Crime and Harmful Acts Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * @var string
   */
  public $crimeHarmfulActsIndividualsSocietyHumanRightsViolationsContent;
  /**
   * Optional. Death, Injury, or Military Conflict Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * @var string
   */
  public $deathInjuryMilitaryConflictContent;
  /**
   * Optional. Debated Sensitive Social Issue Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * @var string
   */
  public $debatedSensitiveSocialIssueContent;
  /**
   * Optional. IAB viewability threshold for display ads.
   *
   * @var string
   */
  public $displayIabViewability;
  /**
   * Scope3 categories to exclude.
   *
   * @var string[]
   */
  public $excludedAdlooxCategories;
  /**
   * Optional. Scope3's fraud IVT MFA categories to exclude.
   *
   * @var string[]
   */
  public $excludedFraudIvtMfaCategories;
  /**
   * Optional. Hate Speech and Acts of Aggression Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * @var string
   */
  public $hateSpeechActsAggressionContent;
  /**
   * Optional. Illegal Drugs/Alcohol Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * @var string
   */
  public $illegalDrugsTobaccoEcigarettesVapingAlcoholContent;
  /**
   * Optional. Misinformation Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * @var string
   */
  public $misinformationContent;
  /**
   * Optional. Obscenity and Profanity Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * @var string
   */
  public $obscenityProfanityContent;
  /**
   * Optional. Online Piracy Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * @var string
   */
  public $onlinePiracyContent;
  /**
   * Optional. Spam or Harmful Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * @var string
   */
  public $spamHarmfulContent;
  /**
   * Optional. Terrorism Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * @var string
   */
  public $terrorismContent;
  /**
   * Optional. IAB viewability threshold for video ads.
   *
   * @var string
   */
  public $videoIabViewability;

  /**
   * Optional. Adult and Explicit Sexual Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * Accepted values: GARM_RISK_EXCLUSION_UNSPECIFIED,
   * GARM_RISK_EXCLUSION_FLOOR, GARM_RISK_EXCLUSION_HIGH,
   * GARM_RISK_EXCLUSION_MEDIUM, GARM_RISK_EXCLUSION_LOW
   *
   * @param self::ADULT_EXPLICIT_SEXUAL_CONTENT_* $adultExplicitSexualContent
   */
  public function setAdultExplicitSexualContent($adultExplicitSexualContent)
  {
    $this->adultExplicitSexualContent = $adultExplicitSexualContent;
  }
  /**
   * @return self::ADULT_EXPLICIT_SEXUAL_CONTENT_*
   */
  public function getAdultExplicitSexualContent()
  {
    return $this->adultExplicitSexualContent;
  }
  /**
   * Optional. Arms and Ammunition Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * Accepted values: GARM_RISK_EXCLUSION_UNSPECIFIED,
   * GARM_RISK_EXCLUSION_FLOOR, GARM_RISK_EXCLUSION_HIGH,
   * GARM_RISK_EXCLUSION_MEDIUM, GARM_RISK_EXCLUSION_LOW
   *
   * @param self::ARMS_AMMUNITION_CONTENT_* $armsAmmunitionContent
   */
  public function setArmsAmmunitionContent($armsAmmunitionContent)
  {
    $this->armsAmmunitionContent = $armsAmmunitionContent;
  }
  /**
   * @return self::ARMS_AMMUNITION_CONTENT_*
   */
  public function getArmsAmmunitionContent()
  {
    return $this->armsAmmunitionContent;
  }
  /**
   * Optional. Crime and Harmful Acts Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * Accepted values: GARM_RISK_EXCLUSION_UNSPECIFIED,
   * GARM_RISK_EXCLUSION_FLOOR, GARM_RISK_EXCLUSION_HIGH,
   * GARM_RISK_EXCLUSION_MEDIUM, GARM_RISK_EXCLUSION_LOW
   *
   * @param self::CRIME_HARMFUL_ACTS_INDIVIDUALS_SOCIETY_HUMAN_RIGHTS_VIOLATIONS_CONTENT_* $crimeHarmfulActsIndividualsSocietyHumanRightsViolationsContent
   */
  public function setCrimeHarmfulActsIndividualsSocietyHumanRightsViolationsContent($crimeHarmfulActsIndividualsSocietyHumanRightsViolationsContent)
  {
    $this->crimeHarmfulActsIndividualsSocietyHumanRightsViolationsContent = $crimeHarmfulActsIndividualsSocietyHumanRightsViolationsContent;
  }
  /**
   * @return self::CRIME_HARMFUL_ACTS_INDIVIDUALS_SOCIETY_HUMAN_RIGHTS_VIOLATIONS_CONTENT_*
   */
  public function getCrimeHarmfulActsIndividualsSocietyHumanRightsViolationsContent()
  {
    return $this->crimeHarmfulActsIndividualsSocietyHumanRightsViolationsContent;
  }
  /**
   * Optional. Death, Injury, or Military Conflict Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * Accepted values: GARM_RISK_EXCLUSION_UNSPECIFIED,
   * GARM_RISK_EXCLUSION_FLOOR, GARM_RISK_EXCLUSION_HIGH,
   * GARM_RISK_EXCLUSION_MEDIUM, GARM_RISK_EXCLUSION_LOW
   *
   * @param self::DEATH_INJURY_MILITARY_CONFLICT_CONTENT_* $deathInjuryMilitaryConflictContent
   */
  public function setDeathInjuryMilitaryConflictContent($deathInjuryMilitaryConflictContent)
  {
    $this->deathInjuryMilitaryConflictContent = $deathInjuryMilitaryConflictContent;
  }
  /**
   * @return self::DEATH_INJURY_MILITARY_CONFLICT_CONTENT_*
   */
  public function getDeathInjuryMilitaryConflictContent()
  {
    return $this->deathInjuryMilitaryConflictContent;
  }
  /**
   * Optional. Debated Sensitive Social Issue Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * Accepted values: GARM_RISK_EXCLUSION_UNSPECIFIED,
   * GARM_RISK_EXCLUSION_FLOOR, GARM_RISK_EXCLUSION_HIGH,
   * GARM_RISK_EXCLUSION_MEDIUM, GARM_RISK_EXCLUSION_LOW
   *
   * @param self::DEBATED_SENSITIVE_SOCIAL_ISSUE_CONTENT_* $debatedSensitiveSocialIssueContent
   */
  public function setDebatedSensitiveSocialIssueContent($debatedSensitiveSocialIssueContent)
  {
    $this->debatedSensitiveSocialIssueContent = $debatedSensitiveSocialIssueContent;
  }
  /**
   * @return self::DEBATED_SENSITIVE_SOCIAL_ISSUE_CONTENT_*
   */
  public function getDebatedSensitiveSocialIssueContent()
  {
    return $this->debatedSensitiveSocialIssueContent;
  }
  /**
   * Optional. IAB viewability threshold for display ads.
   *
   * Accepted values: DISPLAY_IAB_VIEWABILITY_UNSPECIFIED,
   * DISPLAY_IAB_VIEWABILITY_10, DISPLAY_IAB_VIEWABILITY_20,
   * DISPLAY_IAB_VIEWABILITY_35, DISPLAY_IAB_VIEWABILITY_50,
   * DISPLAY_IAB_VIEWABILITY_75
   *
   * @param self::DISPLAY_IAB_VIEWABILITY_* $displayIabViewability
   */
  public function setDisplayIabViewability($displayIabViewability)
  {
    $this->displayIabViewability = $displayIabViewability;
  }
  /**
   * @return self::DISPLAY_IAB_VIEWABILITY_*
   */
  public function getDisplayIabViewability()
  {
    return $this->displayIabViewability;
  }
  /**
   * Scope3 categories to exclude.
   *
   * @param string[] $excludedAdlooxCategories
   */
  public function setExcludedAdlooxCategories($excludedAdlooxCategories)
  {
    $this->excludedAdlooxCategories = $excludedAdlooxCategories;
  }
  /**
   * @return string[]
   */
  public function getExcludedAdlooxCategories()
  {
    return $this->excludedAdlooxCategories;
  }
  /**
   * Optional. Scope3's fraud IVT MFA categories to exclude.
   *
   * @param string[] $excludedFraudIvtMfaCategories
   */
  public function setExcludedFraudIvtMfaCategories($excludedFraudIvtMfaCategories)
  {
    $this->excludedFraudIvtMfaCategories = $excludedFraudIvtMfaCategories;
  }
  /**
   * @return string[]
   */
  public function getExcludedFraudIvtMfaCategories()
  {
    return $this->excludedFraudIvtMfaCategories;
  }
  /**
   * Optional. Hate Speech and Acts of Aggression Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * Accepted values: GARM_RISK_EXCLUSION_UNSPECIFIED,
   * GARM_RISK_EXCLUSION_FLOOR, GARM_RISK_EXCLUSION_HIGH,
   * GARM_RISK_EXCLUSION_MEDIUM, GARM_RISK_EXCLUSION_LOW
   *
   * @param self::HATE_SPEECH_ACTS_AGGRESSION_CONTENT_* $hateSpeechActsAggressionContent
   */
  public function setHateSpeechActsAggressionContent($hateSpeechActsAggressionContent)
  {
    $this->hateSpeechActsAggressionContent = $hateSpeechActsAggressionContent;
  }
  /**
   * @return self::HATE_SPEECH_ACTS_AGGRESSION_CONTENT_*
   */
  public function getHateSpeechActsAggressionContent()
  {
    return $this->hateSpeechActsAggressionContent;
  }
  /**
   * Optional. Illegal Drugs/Alcohol Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * Accepted values: GARM_RISK_EXCLUSION_UNSPECIFIED,
   * GARM_RISK_EXCLUSION_FLOOR, GARM_RISK_EXCLUSION_HIGH,
   * GARM_RISK_EXCLUSION_MEDIUM, GARM_RISK_EXCLUSION_LOW
   *
   * @param self::ILLEGAL_DRUGS_TOBACCO_ECIGARETTES_VAPING_ALCOHOL_CONTENT_* $illegalDrugsTobaccoEcigarettesVapingAlcoholContent
   */
  public function setIllegalDrugsTobaccoEcigarettesVapingAlcoholContent($illegalDrugsTobaccoEcigarettesVapingAlcoholContent)
  {
    $this->illegalDrugsTobaccoEcigarettesVapingAlcoholContent = $illegalDrugsTobaccoEcigarettesVapingAlcoholContent;
  }
  /**
   * @return self::ILLEGAL_DRUGS_TOBACCO_ECIGARETTES_VAPING_ALCOHOL_CONTENT_*
   */
  public function getIllegalDrugsTobaccoEcigarettesVapingAlcoholContent()
  {
    return $this->illegalDrugsTobaccoEcigarettesVapingAlcoholContent;
  }
  /**
   * Optional. Misinformation Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * Accepted values: GARM_RISK_EXCLUSION_UNSPECIFIED,
   * GARM_RISK_EXCLUSION_FLOOR, GARM_RISK_EXCLUSION_HIGH,
   * GARM_RISK_EXCLUSION_MEDIUM, GARM_RISK_EXCLUSION_LOW
   *
   * @param self::MISINFORMATION_CONTENT_* $misinformationContent
   */
  public function setMisinformationContent($misinformationContent)
  {
    $this->misinformationContent = $misinformationContent;
  }
  /**
   * @return self::MISINFORMATION_CONTENT_*
   */
  public function getMisinformationContent()
  {
    return $this->misinformationContent;
  }
  /**
   * Optional. Obscenity and Profanity Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * Accepted values: GARM_RISK_EXCLUSION_UNSPECIFIED,
   * GARM_RISK_EXCLUSION_FLOOR, GARM_RISK_EXCLUSION_HIGH,
   * GARM_RISK_EXCLUSION_MEDIUM, GARM_RISK_EXCLUSION_LOW
   *
   * @param self::OBSCENITY_PROFANITY_CONTENT_* $obscenityProfanityContent
   */
  public function setObscenityProfanityContent($obscenityProfanityContent)
  {
    $this->obscenityProfanityContent = $obscenityProfanityContent;
  }
  /**
   * @return self::OBSCENITY_PROFANITY_CONTENT_*
   */
  public function getObscenityProfanityContent()
  {
    return $this->obscenityProfanityContent;
  }
  /**
   * Optional. Online Piracy Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * Accepted values: GARM_RISK_EXCLUSION_UNSPECIFIED,
   * GARM_RISK_EXCLUSION_FLOOR, GARM_RISK_EXCLUSION_HIGH,
   * GARM_RISK_EXCLUSION_MEDIUM, GARM_RISK_EXCLUSION_LOW
   *
   * @param self::ONLINE_PIRACY_CONTENT_* $onlinePiracyContent
   */
  public function setOnlinePiracyContent($onlinePiracyContent)
  {
    $this->onlinePiracyContent = $onlinePiracyContent;
  }
  /**
   * @return self::ONLINE_PIRACY_CONTENT_*
   */
  public function getOnlinePiracyContent()
  {
    return $this->onlinePiracyContent;
  }
  /**
   * Optional. Spam or Harmful Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * Accepted values: GARM_RISK_EXCLUSION_UNSPECIFIED,
   * GARM_RISK_EXCLUSION_FLOOR, GARM_RISK_EXCLUSION_HIGH,
   * GARM_RISK_EXCLUSION_MEDIUM, GARM_RISK_EXCLUSION_LOW
   *
   * @param self::SPAM_HARMFUL_CONTENT_* $spamHarmfulContent
   */
  public function setSpamHarmfulContent($spamHarmfulContent)
  {
    $this->spamHarmfulContent = $spamHarmfulContent;
  }
  /**
   * @return self::SPAM_HARMFUL_CONTENT_*
   */
  public function getSpamHarmfulContent()
  {
    return $this->spamHarmfulContent;
  }
  /**
   * Optional. Terrorism Content
   * [GARM](https://wfanet.org/leadership/garm/about-garm) risk ranges to
   * exclude.
   *
   * Accepted values: GARM_RISK_EXCLUSION_UNSPECIFIED,
   * GARM_RISK_EXCLUSION_FLOOR, GARM_RISK_EXCLUSION_HIGH,
   * GARM_RISK_EXCLUSION_MEDIUM, GARM_RISK_EXCLUSION_LOW
   *
   * @param self::TERRORISM_CONTENT_* $terrorismContent
   */
  public function setTerrorismContent($terrorismContent)
  {
    $this->terrorismContent = $terrorismContent;
  }
  /**
   * @return self::TERRORISM_CONTENT_*
   */
  public function getTerrorismContent()
  {
    return $this->terrorismContent;
  }
  /**
   * Optional. IAB viewability threshold for video ads.
   *
   * Accepted values: VIDEO_IAB_VIEWABILITY_UNSPECIFIED,
   * VIDEO_IAB_VIEWABILITY_10, VIDEO_IAB_VIEWABILITY_20,
   * VIDEO_IAB_VIEWABILITY_35, VIDEO_IAB_VIEWABILITY_50,
   * VIDEO_IAB_VIEWABILITY_75
   *
   * @param self::VIDEO_IAB_VIEWABILITY_* $videoIabViewability
   */
  public function setVideoIabViewability($videoIabViewability)
  {
    $this->videoIabViewability = $videoIabViewability;
  }
  /**
   * @return self::VIDEO_IAB_VIEWABILITY_*
   */
  public function getVideoIabViewability()
  {
    return $this->videoIabViewability;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Adloox::class, 'Google_Service_DisplayVideo_Adloox');
