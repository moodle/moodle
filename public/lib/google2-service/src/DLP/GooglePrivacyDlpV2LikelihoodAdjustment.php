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

class GooglePrivacyDlpV2LikelihoodAdjustment extends \Google\Model
{
  /**
   * Default value; same as POSSIBLE.
   */
  public const FIXED_LIKELIHOOD_LIKELIHOOD_UNSPECIFIED = 'LIKELIHOOD_UNSPECIFIED';
  /**
   * Highest chance of a false positive.
   */
  public const FIXED_LIKELIHOOD_VERY_UNLIKELY = 'VERY_UNLIKELY';
  /**
   * High chance of a false positive.
   */
  public const FIXED_LIKELIHOOD_UNLIKELY = 'UNLIKELY';
  /**
   * Some matching signals. The default value.
   */
  public const FIXED_LIKELIHOOD_POSSIBLE = 'POSSIBLE';
  /**
   * Low chance of a false positive.
   */
  public const FIXED_LIKELIHOOD_LIKELY = 'LIKELY';
  /**
   * Confidence level is high. Lowest chance of a false positive.
   */
  public const FIXED_LIKELIHOOD_VERY_LIKELY = 'VERY_LIKELY';
  /**
   * Set the likelihood of a finding to a fixed value.
   *
   * @var string
   */
  public $fixedLikelihood;
  /**
   * Increase or decrease the likelihood by the specified number of levels. For
   * example, if a finding would be `POSSIBLE` without the detection rule and
   * `relative_likelihood` is 1, then it is upgraded to `LIKELY`, while a value
   * of -1 would downgrade it to `UNLIKELY`. Likelihood may never drop below
   * `VERY_UNLIKELY` or exceed `VERY_LIKELY`, so applying an adjustment of 1
   * followed by an adjustment of -1 when base likelihood is `VERY_LIKELY` will
   * result in a final likelihood of `LIKELY`.
   *
   * @var int
   */
  public $relativeLikelihood;

  /**
   * Set the likelihood of a finding to a fixed value.
   *
   * Accepted values: LIKELIHOOD_UNSPECIFIED, VERY_UNLIKELY, UNLIKELY, POSSIBLE,
   * LIKELY, VERY_LIKELY
   *
   * @param self::FIXED_LIKELIHOOD_* $fixedLikelihood
   */
  public function setFixedLikelihood($fixedLikelihood)
  {
    $this->fixedLikelihood = $fixedLikelihood;
  }
  /**
   * @return self::FIXED_LIKELIHOOD_*
   */
  public function getFixedLikelihood()
  {
    return $this->fixedLikelihood;
  }
  /**
   * Increase or decrease the likelihood by the specified number of levels. For
   * example, if a finding would be `POSSIBLE` without the detection rule and
   * `relative_likelihood` is 1, then it is upgraded to `LIKELY`, while a value
   * of -1 would downgrade it to `UNLIKELY`. Likelihood may never drop below
   * `VERY_UNLIKELY` or exceed `VERY_LIKELY`, so applying an adjustment of 1
   * followed by an adjustment of -1 when base likelihood is `VERY_LIKELY` will
   * result in a final likelihood of `LIKELY`.
   *
   * @param int $relativeLikelihood
   */
  public function setRelativeLikelihood($relativeLikelihood)
  {
    $this->relativeLikelihood = $relativeLikelihood;
  }
  /**
   * @return int
   */
  public function getRelativeLikelihood()
  {
    return $this->relativeLikelihood;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2LikelihoodAdjustment::class, 'Google_Service_DLP_GooglePrivacyDlpV2LikelihoodAdjustment');
