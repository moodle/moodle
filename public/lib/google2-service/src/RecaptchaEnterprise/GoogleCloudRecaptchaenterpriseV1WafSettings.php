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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1WafSettings extends \Google\Model
{
  /**
   * Undefined feature.
   */
  public const WAF_FEATURE_WAF_FEATURE_UNSPECIFIED = 'WAF_FEATURE_UNSPECIFIED';
  /**
   * Redirects suspicious traffic to reCAPTCHA.
   */
  public const WAF_FEATURE_CHALLENGE_PAGE = 'CHALLENGE_PAGE';
  /**
   * Use reCAPTCHA session-tokens to protect the whole user session on the
   * site's domain.
   */
  public const WAF_FEATURE_SESSION_TOKEN = 'SESSION_TOKEN';
  /**
   * Use reCAPTCHA action-tokens to protect user actions.
   */
  public const WAF_FEATURE_ACTION_TOKEN = 'ACTION_TOKEN';
  /**
   * Deprecated: Use `express_settings` instead.
   *
   * @deprecated
   */
  public const WAF_FEATURE_EXPRESS = 'EXPRESS';
  /**
   * Undefined WAF
   */
  public const WAF_SERVICE_WAF_SERVICE_UNSPECIFIED = 'WAF_SERVICE_UNSPECIFIED';
  /**
   * Cloud Armor
   */
  public const WAF_SERVICE_CA = 'CA';
  /**
   * Fastly
   */
  public const WAF_SERVICE_FASTLY = 'FASTLY';
  /**
   * Cloudflare
   */
  public const WAF_SERVICE_CLOUDFLARE = 'CLOUDFLARE';
  /**
   * Akamai
   */
  public const WAF_SERVICE_AKAMAI = 'AKAMAI';
  /**
   * Required. The Web Application Firewall (WAF) feature for which this key is
   * enabled.
   *
   * @var string
   */
  public $wafFeature;
  /**
   * Required. The Web Application Firewall (WAF) service that uses this key.
   *
   * @var string
   */
  public $wafService;

  /**
   * Required. The Web Application Firewall (WAF) feature for which this key is
   * enabled.
   *
   * Accepted values: WAF_FEATURE_UNSPECIFIED, CHALLENGE_PAGE, SESSION_TOKEN,
   * ACTION_TOKEN, EXPRESS
   *
   * @param self::WAF_FEATURE_* $wafFeature
   */
  public function setWafFeature($wafFeature)
  {
    $this->wafFeature = $wafFeature;
  }
  /**
   * @return self::WAF_FEATURE_*
   */
  public function getWafFeature()
  {
    return $this->wafFeature;
  }
  /**
   * Required. The Web Application Firewall (WAF) service that uses this key.
   *
   * Accepted values: WAF_SERVICE_UNSPECIFIED, CA, FASTLY, CLOUDFLARE, AKAMAI
   *
   * @param self::WAF_SERVICE_* $wafService
   */
  public function setWafService($wafService)
  {
    $this->wafService = $wafService;
  }
  /**
   * @return self::WAF_SERVICE_*
   */
  public function getWafService()
  {
    return $this->wafService;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1WafSettings::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1WafSettings');
