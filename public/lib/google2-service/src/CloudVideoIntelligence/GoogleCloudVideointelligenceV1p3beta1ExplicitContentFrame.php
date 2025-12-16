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

namespace Google\Service\CloudVideoIntelligence;

class GoogleCloudVideointelligenceV1p3beta1ExplicitContentFrame extends \Google\Model
{
  /**
   * Unspecified likelihood.
   */
  public const PORNOGRAPHY_LIKELIHOOD_LIKELIHOOD_UNSPECIFIED = 'LIKELIHOOD_UNSPECIFIED';
  /**
   * Very unlikely.
   */
  public const PORNOGRAPHY_LIKELIHOOD_VERY_UNLIKELY = 'VERY_UNLIKELY';
  /**
   * Unlikely.
   */
  public const PORNOGRAPHY_LIKELIHOOD_UNLIKELY = 'UNLIKELY';
  /**
   * Possible.
   */
  public const PORNOGRAPHY_LIKELIHOOD_POSSIBLE = 'POSSIBLE';
  /**
   * Likely.
   */
  public const PORNOGRAPHY_LIKELIHOOD_LIKELY = 'LIKELY';
  /**
   * Very likely.
   */
  public const PORNOGRAPHY_LIKELIHOOD_VERY_LIKELY = 'VERY_LIKELY';
  /**
   * Likelihood of the pornography content..
   *
   * @var string
   */
  public $pornographyLikelihood;
  /**
   * Time-offset, relative to the beginning of the video, corresponding to the
   * video frame for this location.
   *
   * @var string
   */
  public $timeOffset;

  /**
   * Likelihood of the pornography content..
   *
   * Accepted values: LIKELIHOOD_UNSPECIFIED, VERY_UNLIKELY, UNLIKELY, POSSIBLE,
   * LIKELY, VERY_LIKELY
   *
   * @param self::PORNOGRAPHY_LIKELIHOOD_* $pornographyLikelihood
   */
  public function setPornographyLikelihood($pornographyLikelihood)
  {
    $this->pornographyLikelihood = $pornographyLikelihood;
  }
  /**
   * @return self::PORNOGRAPHY_LIKELIHOOD_*
   */
  public function getPornographyLikelihood()
  {
    return $this->pornographyLikelihood;
  }
  /**
   * Time-offset, relative to the beginning of the video, corresponding to the
   * video frame for this location.
   *
   * @param string $timeOffset
   */
  public function setTimeOffset($timeOffset)
  {
    $this->timeOffset = $timeOffset;
  }
  /**
   * @return string
   */
  public function getTimeOffset()
  {
    return $this->timeOffset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVideointelligenceV1p3beta1ExplicitContentFrame::class, 'Google_Service_CloudVideoIntelligence_GoogleCloudVideointelligenceV1p3beta1ExplicitContentFrame');
