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

namespace Google\Service\Walletobjects;

class RotatingBarcodeTotpDetails extends \Google\Collection
{
  public const ALGORITHM_TOTP_ALGORITHM_UNSPECIFIED = 'TOTP_ALGORITHM_UNSPECIFIED';
  /**
   * TOTP algorithm from RFC 6238 with the SHA1 hash function
   */
  public const ALGORITHM_TOTP_SHA1 = 'TOTP_SHA1';
  protected $collection_key = 'parameters';
  /**
   * The TOTP algorithm used to generate the OTP.
   *
   * @var string
   */
  public $algorithm;
  protected $parametersType = RotatingBarcodeTotpDetailsTotpParameters::class;
  protected $parametersDataType = 'array';
  /**
   * The time interval used for the TOTP value generation, in milliseconds.
   *
   * @var string
   */
  public $periodMillis;

  /**
   * The TOTP algorithm used to generate the OTP.
   *
   * Accepted values: TOTP_ALGORITHM_UNSPECIFIED, TOTP_SHA1
   *
   * @param self::ALGORITHM_* $algorithm
   */
  public function setAlgorithm($algorithm)
  {
    $this->algorithm = $algorithm;
  }
  /**
   * @return self::ALGORITHM_*
   */
  public function getAlgorithm()
  {
    return $this->algorithm;
  }
  /**
   * The TOTP parameters for each of the {totp_value_*} substitutions. The
   * TotpParameters at index n is used for the {totp_value_n} substitution.
   *
   * @param RotatingBarcodeTotpDetailsTotpParameters[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return RotatingBarcodeTotpDetailsTotpParameters[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * The time interval used for the TOTP value generation, in milliseconds.
   *
   * @param string $periodMillis
   */
  public function setPeriodMillis($periodMillis)
  {
    $this->periodMillis = $periodMillis;
  }
  /**
   * @return string
   */
  public function getPeriodMillis()
  {
    return $this->periodMillis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RotatingBarcodeTotpDetails::class, 'Google_Service_Walletobjects_RotatingBarcodeTotpDetails');
