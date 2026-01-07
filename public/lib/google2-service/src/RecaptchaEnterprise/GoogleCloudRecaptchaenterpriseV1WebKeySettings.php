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

class GoogleCloudRecaptchaenterpriseV1WebKeySettings extends \Google\Collection
{
  /**
   * Default type that indicates this enum hasn't been specified.
   */
  public const CHALLENGE_SECURITY_PREFERENCE_CHALLENGE_SECURITY_PREFERENCE_UNSPECIFIED = 'CHALLENGE_SECURITY_PREFERENCE_UNSPECIFIED';
  /**
   * Key tends to show fewer and easier challenges.
   */
  public const CHALLENGE_SECURITY_PREFERENCE_USABILITY = 'USABILITY';
  /**
   * Key tends to show balanced (in amount and difficulty) challenges.
   */
  public const CHALLENGE_SECURITY_PREFERENCE_BALANCE = 'BALANCE';
  /**
   * Key tends to show more and harder challenges.
   */
  public const CHALLENGE_SECURITY_PREFERENCE_SECURITY = 'SECURITY';
  /**
   * Default type that indicates this enum hasn't been specified. This is not a
   * valid IntegrationType, one of the other types must be specified instead.
   */
  public const INTEGRATION_TYPE_INTEGRATION_TYPE_UNSPECIFIED = 'INTEGRATION_TYPE_UNSPECIFIED';
  /**
   * Only used to produce scores. It doesn't display the "I'm not a robot"
   * checkbox and never shows captcha challenges.
   */
  public const INTEGRATION_TYPE_SCORE = 'SCORE';
  /**
   * Displays the "I'm not a robot" checkbox and may show captcha challenges
   * after it is checked.
   */
  public const INTEGRATION_TYPE_CHECKBOX = 'CHECKBOX';
  /**
   * Doesn't display the "I'm not a robot" checkbox, but may show captcha
   * challenges after risk analysis.
   */
  public const INTEGRATION_TYPE_INVISIBLE = 'INVISIBLE';
  /**
   * Displays a visual challenge or not depending on the user risk analysis
   * score.
   */
  public const INTEGRATION_TYPE_POLICY_BASED_CHALLENGE = 'POLICY_BASED_CHALLENGE';
  protected $collection_key = 'allowedDomains';
  /**
   * Optional. If set to true, it means allowed_domains are not enforced.
   *
   * @var bool
   */
  public $allowAllDomains;
  /**
   * Optional. If set to true, the key can be used on AMP (Accelerated Mobile
   * Pages) websites. This is supported only for the SCORE integration type.
   *
   * @var bool
   */
  public $allowAmpTraffic;
  /**
   * Optional. Domains or subdomains of websites allowed to use the key. All
   * subdomains of an allowed domain are automatically allowed. A valid domain
   * requires a host and must not include any path, port, query or fragment.
   * Examples: 'example.com' or 'subdomain.example.com' Each key supports a
   * maximum of 250 domains. To use a key on more domains, set
   * `allow_all_domains` to true. When this is set, you are responsible for
   * validating the hostname by checking the `token_properties.hostname` field
   * in each assessment response against your list of allowed domains.
   *
   * @var string[]
   */
  public $allowedDomains;
  /**
   * Optional. Settings for the frequency and difficulty at which this key
   * triggers captcha challenges. This should only be specified for
   * `IntegrationType` CHECKBOX, INVISIBLE or POLICY_BASED_CHALLENGE.
   *
   * @var string
   */
  public $challengeSecurityPreference;
  protected $challengeSettingsType = GoogleCloudRecaptchaenterpriseV1WebKeySettingsChallengeSettings::class;
  protected $challengeSettingsDataType = '';
  /**
   * Required. Describes how this key is integrated with the website.
   *
   * @var string
   */
  public $integrationType;

  /**
   * Optional. If set to true, it means allowed_domains are not enforced.
   *
   * @param bool $allowAllDomains
   */
  public function setAllowAllDomains($allowAllDomains)
  {
    $this->allowAllDomains = $allowAllDomains;
  }
  /**
   * @return bool
   */
  public function getAllowAllDomains()
  {
    return $this->allowAllDomains;
  }
  /**
   * Optional. If set to true, the key can be used on AMP (Accelerated Mobile
   * Pages) websites. This is supported only for the SCORE integration type.
   *
   * @param bool $allowAmpTraffic
   */
  public function setAllowAmpTraffic($allowAmpTraffic)
  {
    $this->allowAmpTraffic = $allowAmpTraffic;
  }
  /**
   * @return bool
   */
  public function getAllowAmpTraffic()
  {
    return $this->allowAmpTraffic;
  }
  /**
   * Optional. Domains or subdomains of websites allowed to use the key. All
   * subdomains of an allowed domain are automatically allowed. A valid domain
   * requires a host and must not include any path, port, query or fragment.
   * Examples: 'example.com' or 'subdomain.example.com' Each key supports a
   * maximum of 250 domains. To use a key on more domains, set
   * `allow_all_domains` to true. When this is set, you are responsible for
   * validating the hostname by checking the `token_properties.hostname` field
   * in each assessment response against your list of allowed domains.
   *
   * @param string[] $allowedDomains
   */
  public function setAllowedDomains($allowedDomains)
  {
    $this->allowedDomains = $allowedDomains;
  }
  /**
   * @return string[]
   */
  public function getAllowedDomains()
  {
    return $this->allowedDomains;
  }
  /**
   * Optional. Settings for the frequency and difficulty at which this key
   * triggers captcha challenges. This should only be specified for
   * `IntegrationType` CHECKBOX, INVISIBLE or POLICY_BASED_CHALLENGE.
   *
   * Accepted values: CHALLENGE_SECURITY_PREFERENCE_UNSPECIFIED, USABILITY,
   * BALANCE, SECURITY
   *
   * @param self::CHALLENGE_SECURITY_PREFERENCE_* $challengeSecurityPreference
   */
  public function setChallengeSecurityPreference($challengeSecurityPreference)
  {
    $this->challengeSecurityPreference = $challengeSecurityPreference;
  }
  /**
   * @return self::CHALLENGE_SECURITY_PREFERENCE_*
   */
  public function getChallengeSecurityPreference()
  {
    return $this->challengeSecurityPreference;
  }
  /**
   * Optional. Challenge settings.
   *
   * @param GoogleCloudRecaptchaenterpriseV1WebKeySettingsChallengeSettings $challengeSettings
   */
  public function setChallengeSettings(GoogleCloudRecaptchaenterpriseV1WebKeySettingsChallengeSettings $challengeSettings)
  {
    $this->challengeSettings = $challengeSettings;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1WebKeySettingsChallengeSettings
   */
  public function getChallengeSettings()
  {
    return $this->challengeSettings;
  }
  /**
   * Required. Describes how this key is integrated with the website.
   *
   * Accepted values: INTEGRATION_TYPE_UNSPECIFIED, SCORE, CHECKBOX, INVISIBLE,
   * POLICY_BASED_CHALLENGE
   *
   * @param self::INTEGRATION_TYPE_* $integrationType
   */
  public function setIntegrationType($integrationType)
  {
    $this->integrationType = $integrationType;
  }
  /**
   * @return self::INTEGRATION_TYPE_*
   */
  public function getIntegrationType()
  {
    return $this->integrationType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1WebKeySettings::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1WebKeySettings');
