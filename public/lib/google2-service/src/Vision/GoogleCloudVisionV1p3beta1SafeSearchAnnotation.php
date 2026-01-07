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

namespace Google\Service\Vision;

class GoogleCloudVisionV1p3beta1SafeSearchAnnotation extends \Google\Model
{
  /**
   * Unknown likelihood.
   */
  public const ADULT_UNKNOWN = 'UNKNOWN';
  /**
   * It is very unlikely.
   */
  public const ADULT_VERY_UNLIKELY = 'VERY_UNLIKELY';
  /**
   * It is unlikely.
   */
  public const ADULT_UNLIKELY = 'UNLIKELY';
  /**
   * It is possible.
   */
  public const ADULT_POSSIBLE = 'POSSIBLE';
  /**
   * It is likely.
   */
  public const ADULT_LIKELY = 'LIKELY';
  /**
   * It is very likely.
   */
  public const ADULT_VERY_LIKELY = 'VERY_LIKELY';
  /**
   * Unknown likelihood.
   */
  public const MEDICAL_UNKNOWN = 'UNKNOWN';
  /**
   * It is very unlikely.
   */
  public const MEDICAL_VERY_UNLIKELY = 'VERY_UNLIKELY';
  /**
   * It is unlikely.
   */
  public const MEDICAL_UNLIKELY = 'UNLIKELY';
  /**
   * It is possible.
   */
  public const MEDICAL_POSSIBLE = 'POSSIBLE';
  /**
   * It is likely.
   */
  public const MEDICAL_LIKELY = 'LIKELY';
  /**
   * It is very likely.
   */
  public const MEDICAL_VERY_LIKELY = 'VERY_LIKELY';
  /**
   * Unknown likelihood.
   */
  public const RACY_UNKNOWN = 'UNKNOWN';
  /**
   * It is very unlikely.
   */
  public const RACY_VERY_UNLIKELY = 'VERY_UNLIKELY';
  /**
   * It is unlikely.
   */
  public const RACY_UNLIKELY = 'UNLIKELY';
  /**
   * It is possible.
   */
  public const RACY_POSSIBLE = 'POSSIBLE';
  /**
   * It is likely.
   */
  public const RACY_LIKELY = 'LIKELY';
  /**
   * It is very likely.
   */
  public const RACY_VERY_LIKELY = 'VERY_LIKELY';
  /**
   * Unknown likelihood.
   */
  public const SPOOF_UNKNOWN = 'UNKNOWN';
  /**
   * It is very unlikely.
   */
  public const SPOOF_VERY_UNLIKELY = 'VERY_UNLIKELY';
  /**
   * It is unlikely.
   */
  public const SPOOF_UNLIKELY = 'UNLIKELY';
  /**
   * It is possible.
   */
  public const SPOOF_POSSIBLE = 'POSSIBLE';
  /**
   * It is likely.
   */
  public const SPOOF_LIKELY = 'LIKELY';
  /**
   * It is very likely.
   */
  public const SPOOF_VERY_LIKELY = 'VERY_LIKELY';
  /**
   * Unknown likelihood.
   */
  public const VIOLENCE_UNKNOWN = 'UNKNOWN';
  /**
   * It is very unlikely.
   */
  public const VIOLENCE_VERY_UNLIKELY = 'VERY_UNLIKELY';
  /**
   * It is unlikely.
   */
  public const VIOLENCE_UNLIKELY = 'UNLIKELY';
  /**
   * It is possible.
   */
  public const VIOLENCE_POSSIBLE = 'POSSIBLE';
  /**
   * It is likely.
   */
  public const VIOLENCE_LIKELY = 'LIKELY';
  /**
   * It is very likely.
   */
  public const VIOLENCE_VERY_LIKELY = 'VERY_LIKELY';
  /**
   * Represents the adult content likelihood for the image. Adult content may
   * contain elements such as nudity, pornographic images or cartoons, or sexual
   * activities.
   *
   * @var string
   */
  public $adult;
  /**
   * Likelihood that this is a medical image.
   *
   * @var string
   */
  public $medical;
  /**
   * Likelihood that the request image contains racy content. Racy content may
   * include (but is not limited to) skimpy or sheer clothing, strategically
   * covered nudity, lewd or provocative poses, or close-ups of sensitive body
   * areas.
   *
   * @var string
   */
  public $racy;
  /**
   * Spoof likelihood. The likelihood that an modification was made to the
   * image's canonical version to make it appear funny or offensive.
   *
   * @var string
   */
  public $spoof;
  /**
   * Likelihood that this image contains violent content. Violent content may
   * include death, serious harm, or injury to individuals or groups of
   * individuals.
   *
   * @var string
   */
  public $violence;

  /**
   * Represents the adult content likelihood for the image. Adult content may
   * contain elements such as nudity, pornographic images or cartoons, or sexual
   * activities.
   *
   * Accepted values: UNKNOWN, VERY_UNLIKELY, UNLIKELY, POSSIBLE, LIKELY,
   * VERY_LIKELY
   *
   * @param self::ADULT_* $adult
   */
  public function setAdult($adult)
  {
    $this->adult = $adult;
  }
  /**
   * @return self::ADULT_*
   */
  public function getAdult()
  {
    return $this->adult;
  }
  /**
   * Likelihood that this is a medical image.
   *
   * Accepted values: UNKNOWN, VERY_UNLIKELY, UNLIKELY, POSSIBLE, LIKELY,
   * VERY_LIKELY
   *
   * @param self::MEDICAL_* $medical
   */
  public function setMedical($medical)
  {
    $this->medical = $medical;
  }
  /**
   * @return self::MEDICAL_*
   */
  public function getMedical()
  {
    return $this->medical;
  }
  /**
   * Likelihood that the request image contains racy content. Racy content may
   * include (but is not limited to) skimpy or sheer clothing, strategically
   * covered nudity, lewd or provocative poses, or close-ups of sensitive body
   * areas.
   *
   * Accepted values: UNKNOWN, VERY_UNLIKELY, UNLIKELY, POSSIBLE, LIKELY,
   * VERY_LIKELY
   *
   * @param self::RACY_* $racy
   */
  public function setRacy($racy)
  {
    $this->racy = $racy;
  }
  /**
   * @return self::RACY_*
   */
  public function getRacy()
  {
    return $this->racy;
  }
  /**
   * Spoof likelihood. The likelihood that an modification was made to the
   * image's canonical version to make it appear funny or offensive.
   *
   * Accepted values: UNKNOWN, VERY_UNLIKELY, UNLIKELY, POSSIBLE, LIKELY,
   * VERY_LIKELY
   *
   * @param self::SPOOF_* $spoof
   */
  public function setSpoof($spoof)
  {
    $this->spoof = $spoof;
  }
  /**
   * @return self::SPOOF_*
   */
  public function getSpoof()
  {
    return $this->spoof;
  }
  /**
   * Likelihood that this image contains violent content. Violent content may
   * include death, serious harm, or injury to individuals or groups of
   * individuals.
   *
   * Accepted values: UNKNOWN, VERY_UNLIKELY, UNLIKELY, POSSIBLE, LIKELY,
   * VERY_LIKELY
   *
   * @param self::VIOLENCE_* $violence
   */
  public function setViolence($violence)
  {
    $this->violence = $violence;
  }
  /**
   * @return self::VIOLENCE_*
   */
  public function getViolence()
  {
    return $this->violence;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVisionV1p3beta1SafeSearchAnnotation::class, 'Google_Service_Vision_GoogleCloudVisionV1p3beta1SafeSearchAnnotation');
