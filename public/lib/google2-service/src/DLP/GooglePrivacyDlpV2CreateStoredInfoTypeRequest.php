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

class GooglePrivacyDlpV2CreateStoredInfoTypeRequest extends \Google\Model
{
  protected $configType = GooglePrivacyDlpV2StoredInfoTypeConfig::class;
  protected $configDataType = '';
  /**
   * Deprecated. This field has no effect.
   *
   * @var string
   */
  public $locationId;
  /**
   * The storedInfoType ID can contain uppercase and lowercase letters, numbers,
   * and hyphens; that is, it must match the regular expression:
   * `[a-zA-Z\d-_]+`. The maximum length is 100 characters. Can be empty to
   * allow the system to generate one.
   *
   * @var string
   */
  public $storedInfoTypeId;

  /**
   * Required. Configuration of the storedInfoType to create.
   *
   * @param GooglePrivacyDlpV2StoredInfoTypeConfig $config
   */
  public function setConfig(GooglePrivacyDlpV2StoredInfoTypeConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return GooglePrivacyDlpV2StoredInfoTypeConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Deprecated. This field has no effect.
   *
   * @param string $locationId
   */
  public function setLocationId($locationId)
  {
    $this->locationId = $locationId;
  }
  /**
   * @return string
   */
  public function getLocationId()
  {
    return $this->locationId;
  }
  /**
   * The storedInfoType ID can contain uppercase and lowercase letters, numbers,
   * and hyphens; that is, it must match the regular expression:
   * `[a-zA-Z\d-_]+`. The maximum length is 100 characters. Can be empty to
   * allow the system to generate one.
   *
   * @param string $storedInfoTypeId
   */
  public function setStoredInfoTypeId($storedInfoTypeId)
  {
    $this->storedInfoTypeId = $storedInfoTypeId;
  }
  /**
   * @return string
   */
  public function getStoredInfoTypeId()
  {
    return $this->storedInfoTypeId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2CreateStoredInfoTypeRequest::class, 'Google_Service_DLP_GooglePrivacyDlpV2CreateStoredInfoTypeRequest');
