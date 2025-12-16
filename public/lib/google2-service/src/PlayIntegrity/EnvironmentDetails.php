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

namespace Google\Service\PlayIntegrity;

class EnvironmentDetails extends \Google\Model
{
  /**
   * Play Protect verdict has not been set.
   */
  public const PLAY_PROTECT_VERDICT_PLAY_PROTECT_VERDICT_UNSPECIFIED = 'PLAY_PROTECT_VERDICT_UNSPECIFIED';
  /**
   * Play Protect state was not evaluated. Device may not be trusted.
   */
  public const PLAY_PROTECT_VERDICT_UNEVALUATED = 'UNEVALUATED';
  /**
   * Play Protect is on and no issues found.
   */
  public const PLAY_PROTECT_VERDICT_NO_ISSUES = 'NO_ISSUES';
  /**
   * Play Protect is on but no scan has been performed yet. The device or Play
   * Store app may have been reset.
   */
  public const PLAY_PROTECT_VERDICT_NO_DATA = 'NO_DATA';
  /**
   * Play Protect is on and warnings found.
   */
  public const PLAY_PROTECT_VERDICT_MEDIUM_RISK = 'MEDIUM_RISK';
  /**
   * Play Protect is on and high severity issues found.
   */
  public const PLAY_PROTECT_VERDICT_HIGH_RISK = 'HIGH_RISK';
  /**
   * Play Protect is turned off. Turn on Play Protect.
   */
  public const PLAY_PROTECT_VERDICT_POSSIBLE_RISK = 'POSSIBLE_RISK';
  protected $appAccessRiskVerdictType = AppAccessRiskVerdict::class;
  protected $appAccessRiskVerdictDataType = '';
  /**
   * The evaluation of Play Protect verdict.
   *
   * @var string
   */
  public $playProtectVerdict;

  /**
   * The evaluation of the App Access Risk verdicts.
   *
   * @param AppAccessRiskVerdict $appAccessRiskVerdict
   */
  public function setAppAccessRiskVerdict(AppAccessRiskVerdict $appAccessRiskVerdict)
  {
    $this->appAccessRiskVerdict = $appAccessRiskVerdict;
  }
  /**
   * @return AppAccessRiskVerdict
   */
  public function getAppAccessRiskVerdict()
  {
    return $this->appAccessRiskVerdict;
  }
  /**
   * The evaluation of Play Protect verdict.
   *
   * Accepted values: PLAY_PROTECT_VERDICT_UNSPECIFIED, UNEVALUATED, NO_ISSUES,
   * NO_DATA, MEDIUM_RISK, HIGH_RISK, POSSIBLE_RISK
   *
   * @param self::PLAY_PROTECT_VERDICT_* $playProtectVerdict
   */
  public function setPlayProtectVerdict($playProtectVerdict)
  {
    $this->playProtectVerdict = $playProtectVerdict;
  }
  /**
   * @return self::PLAY_PROTECT_VERDICT_*
   */
  public function getPlayProtectVerdict()
  {
    return $this->playProtectVerdict;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnvironmentDetails::class, 'Google_Service_PlayIntegrity_EnvironmentDetails');
