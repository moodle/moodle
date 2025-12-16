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

class GoogleFirebaseAppcheckV1RecaptchaEnterpriseConfig extends \Google\Model
{
  /**
   * Required. The relative resource name of the reCAPTCHA Enterprise
   * configuration object, in the format: ```
   * projects/{project_number}/apps/{app_id}/recaptchaEnterpriseConfig ```
   *
   * @var string
   */
  public $name;
  protected $riskAnalysisType = GoogleFirebaseAppcheckV1RecaptchaEnterpriseConfigRiskAnalysis::class;
  protected $riskAnalysisDataType = '';
  /**
   * The score-based site key [created in reCAPTCHA
   * Enterprise](https://cloud.google.com/recaptcha-enterprise/docs/create-
   * key#creating_a_site_key) used to [invoke reCAPTCHA and generate the
   * reCAPTCHA tokens](https://cloud.google.com/recaptcha-
   * enterprise/docs/instrument-web-pages) for your application. Important: This
   * is *not* the `site_secret` (as it is in reCAPTCHA v3), but rather your
   * score-based reCAPTCHA Enterprise site key.
   *
   * @var string
   */
  public $siteKey;
  /**
   * Specifies the duration for which App Check tokens exchanged from reCAPTCHA
   * Enterprise tokens will be valid. If unset, a default value of 1 hour is
   * assumed. Must be between 30 minutes and 7 days, inclusive.
   *
   * @var string
   */
  public $tokenTtl;

  /**
   * Required. The relative resource name of the reCAPTCHA Enterprise
   * configuration object, in the format: ```
   * projects/{project_number}/apps/{app_id}/recaptchaEnterpriseConfig ```
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
   * Specifies risk tolerance and requirements for your application. These
   * settings correspond to requirements on the
   * [**`riskAnalysis`**](https://cloud.google.com/recaptcha/docs/interpret-
   * assessment-website#interpret_assessment) tuple in the assessment obtained
   * from reCAPTCHA Enterprise. The default values for these settings work for
   * most apps, and are recommended.
   *
   * @param GoogleFirebaseAppcheckV1RecaptchaEnterpriseConfigRiskAnalysis $riskAnalysis
   */
  public function setRiskAnalysis(GoogleFirebaseAppcheckV1RecaptchaEnterpriseConfigRiskAnalysis $riskAnalysis)
  {
    $this->riskAnalysis = $riskAnalysis;
  }
  /**
   * @return GoogleFirebaseAppcheckV1RecaptchaEnterpriseConfigRiskAnalysis
   */
  public function getRiskAnalysis()
  {
    return $this->riskAnalysis;
  }
  /**
   * The score-based site key [created in reCAPTCHA
   * Enterprise](https://cloud.google.com/recaptcha-enterprise/docs/create-
   * key#creating_a_site_key) used to [invoke reCAPTCHA and generate the
   * reCAPTCHA tokens](https://cloud.google.com/recaptcha-
   * enterprise/docs/instrument-web-pages) for your application. Important: This
   * is *not* the `site_secret` (as it is in reCAPTCHA v3), but rather your
   * score-based reCAPTCHA Enterprise site key.
   *
   * @param string $siteKey
   */
  public function setSiteKey($siteKey)
  {
    $this->siteKey = $siteKey;
  }
  /**
   * @return string
   */
  public function getSiteKey()
  {
    return $this->siteKey;
  }
  /**
   * Specifies the duration for which App Check tokens exchanged from reCAPTCHA
   * Enterprise tokens will be valid. If unset, a default value of 1 hour is
   * assumed. Must be between 30 minutes and 7 days, inclusive.
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
class_alias(GoogleFirebaseAppcheckV1RecaptchaEnterpriseConfig::class, 'Google_Service_Firebaseappcheck_GoogleFirebaseAppcheckV1RecaptchaEnterpriseConfig');
