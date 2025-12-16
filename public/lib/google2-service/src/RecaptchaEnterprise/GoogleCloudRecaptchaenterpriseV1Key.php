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

class GoogleCloudRecaptchaenterpriseV1Key extends \Google\Model
{
  protected $androidSettingsType = GoogleCloudRecaptchaenterpriseV1AndroidKeySettings::class;
  protected $androidSettingsDataType = '';
  /**
   * Output only. The timestamp corresponding to the creation of this key.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. Human-readable display name of this key. Modifiable by user.
   *
   * @var string
   */
  public $displayName;
  protected $expressSettingsType = GoogleCloudRecaptchaenterpriseV1ExpressKeySettings::class;
  protected $expressSettingsDataType = '';
  protected $iosSettingsType = GoogleCloudRecaptchaenterpriseV1IOSKeySettings::class;
  protected $iosSettingsDataType = '';
  /**
   * Optional. See [Creating and managing labels]
   * (https://cloud.google.com/recaptcha/docs/labels).
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name for the Key in the format
   * `projects/{project}/keys/{key}`.
   *
   * @var string
   */
  public $name;
  protected $testingOptionsType = GoogleCloudRecaptchaenterpriseV1TestingOptions::class;
  protected $testingOptionsDataType = '';
  protected $wafSettingsType = GoogleCloudRecaptchaenterpriseV1WafSettings::class;
  protected $wafSettingsDataType = '';
  protected $webSettingsType = GoogleCloudRecaptchaenterpriseV1WebKeySettings::class;
  protected $webSettingsDataType = '';

  /**
   * Settings for keys that can be used by Android apps.
   *
   * @param GoogleCloudRecaptchaenterpriseV1AndroidKeySettings $androidSettings
   */
  public function setAndroidSettings(GoogleCloudRecaptchaenterpriseV1AndroidKeySettings $androidSettings)
  {
    $this->androidSettings = $androidSettings;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1AndroidKeySettings
   */
  public function getAndroidSettings()
  {
    return $this->androidSettings;
  }
  /**
   * Output only. The timestamp corresponding to the creation of this key.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Required. Human-readable display name of this key. Modifiable by user.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Settings for keys that can be used by reCAPTCHA Express.
   *
   * @param GoogleCloudRecaptchaenterpriseV1ExpressKeySettings $expressSettings
   */
  public function setExpressSettings(GoogleCloudRecaptchaenterpriseV1ExpressKeySettings $expressSettings)
  {
    $this->expressSettings = $expressSettings;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1ExpressKeySettings
   */
  public function getExpressSettings()
  {
    return $this->expressSettings;
  }
  /**
   * Settings for keys that can be used by iOS apps.
   *
   * @param GoogleCloudRecaptchaenterpriseV1IOSKeySettings $iosSettings
   */
  public function setIosSettings(GoogleCloudRecaptchaenterpriseV1IOSKeySettings $iosSettings)
  {
    $this->iosSettings = $iosSettings;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1IOSKeySettings
   */
  public function getIosSettings()
  {
    return $this->iosSettings;
  }
  /**
   * Optional. See [Creating and managing labels]
   * (https://cloud.google.com/recaptcha/docs/labels).
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Identifier. The resource name for the Key in the format
   * `projects/{project}/keys/{key}`.
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
   * Optional. Options for user acceptance testing.
   *
   * @param GoogleCloudRecaptchaenterpriseV1TestingOptions $testingOptions
   */
  public function setTestingOptions(GoogleCloudRecaptchaenterpriseV1TestingOptions $testingOptions)
  {
    $this->testingOptions = $testingOptions;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1TestingOptions
   */
  public function getTestingOptions()
  {
    return $this->testingOptions;
  }
  /**
   * Optional. Settings for Web Application Firewall (WAF).
   *
   * @param GoogleCloudRecaptchaenterpriseV1WafSettings $wafSettings
   */
  public function setWafSettings(GoogleCloudRecaptchaenterpriseV1WafSettings $wafSettings)
  {
    $this->wafSettings = $wafSettings;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1WafSettings
   */
  public function getWafSettings()
  {
    return $this->wafSettings;
  }
  /**
   * Settings for keys that can be used by websites.
   *
   * @param GoogleCloudRecaptchaenterpriseV1WebKeySettings $webSettings
   */
  public function setWebSettings(GoogleCloudRecaptchaenterpriseV1WebKeySettings $webSettings)
  {
    $this->webSettings = $webSettings;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1WebKeySettings
   */
  public function getWebSettings()
  {
    return $this->webSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1Key::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1Key');
