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

namespace Google\Service\Firebaseappcheck;

class GoogleFirebaseAppcheckV1RecaptchaV3Config extends \Google\Model
{
  /**
   * Specifies a minimum score required for a reCAPTCHA token to be considered
   * valid. If its score is greater than or equal to this value, it will be
   * accepted; otherwise, it will be rejected. The value must be between 0.0 and
   * 1.0. The default value is 0.5.
   *
   * @var float
   */
  public $minValidScore;
  /**
   * Required. The relative resource name of the reCAPTCHA v3 configuration
   * object, in the format: ```
   * projects/{project_number}/apps/{app_id}/recaptchaV3Config ```
   *
   * @var string
   */
  public $name;
  /**
   * Required. Input only. The site secret used to identify your service for
   * reCAPTCHA v3 verification. For security reasons, this field will never be
   * populated in any response.
   *
   * @var string
   */
  public $siteSecret;
  /**
   * Output only. Whether the `site_secret` field was previously set. Since we
   * will never return the `site_secret` field, this field is the only way to
   * find out whether it was previously set.
   *
   * @var bool
   */
  public $siteSecretSet;
  /**
   * Specifies the duration for which App Check tokens exchanged from reCAPTCHA
   * tokens will be valid. If unset, a default value of 1 day is assumed. Must
   * be between 30 minutes and 7 days, inclusive.
   *
   * @var string
   */
  public $tokenTtl;

  /**
   * Specifies a minimum score required for a reCAPTCHA token to be considered
   * valid. If its score is greater than or equal to this value, it will be
   * accepted; otherwise, it will be rejected. The value must be between 0.0 and
   * 1.0. The default value is 0.5.
   *
   * @param float $minValidScore
   */
  public function setMinValidScore($minValidScore)
  {
    $this->minValidScore = $minValidScore;
  }
  /**
   * @return float
   */
  public function getMinValidScore()
  {
    return $this->minValidScore;
  }
  /**
   * Required. The relative resource name of the reCAPTCHA v3 configuration
   * object, in the format: ```
   * projects/{project_number}/apps/{app_id}/recaptchaV3Config ```
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. Input only. The site secret used to identify your service for
   * reCAPTCHA v3 verification. For security reasons, this field will never be
   * populated in any response.
   *
   * @param string $siteSecret
   */
  public function setSiteSecret($siteSecret)
  {
    $this->siteSecret = $siteSecret;
  }
  /**
   * @return string
   */
  public function getSiteSecret()
  {
    return $this->siteSecret;
  }
  /**
   * Output only. Whether the `site_secret` field was previously set. Since we
   * will never return the `site_secret` field, this field is the only way to
   * find out whether it was previously set.
   *
   * @param bool $siteSecretSet
   */
  public function setSiteSecretSet($siteSecretSet)
  {
    $this->siteSecretSet = $siteSecretSet;
  }
  /**
   * @return bool
   */
  public function getSiteSecretSet()
  {
    return $this->siteSecretSet;
  }
  /**
   * Specifies the duration for which App Check tokens exchanged from reCAPTCHA
   * tokens will be valid. If unset, a default value of 1 day is assumed. Must
   * be between 30 minutes and 7 days, inclusive.
   *
   * @param string $tokenTtl
   */
  public function setTokenTtl($tokenTtl)
  {
    $this->tokenTtl = $tokenTtl;
  }
  /**
   * @return string
   */
  public function getTokenTtl()
  {
    return $this->tokenTtl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppcheckV1RecaptchaV3Config::class, 'Google_Service_Firebaseappcheck_GoogleFirebaseAppcheckV1RecaptchaV3Config');
