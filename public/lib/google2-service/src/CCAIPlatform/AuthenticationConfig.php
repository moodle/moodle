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

namespace Google\Service\CCAIPlatform;

class AuthenticationConfig extends \Google\Model
{
  protected $basicAuthSettingType = BasicAuthConfig::class;
  protected $basicAuthSettingDataType = '';
  /**
   * @var string
   */
  public $name;
  protected $samlSettingType = SamlConfig::class;
  protected $samlSettingDataType = '';

  /**
   * @param BasicAuthConfig
   */
  public function setBasicAuthSetting(BasicAuthConfig $basicAuthSetting)
  {
    $this->basicAuthSetting = $basicAuthSetting;
  }
  /**
   * @return BasicAuthConfig
   */
  public function getBasicAuthSetting()
  {
    return $this->basicAuthSetting;
  }
  /**
   * @param string
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
   * @param SamlConfig
   */
  public function setSamlSetting(SamlConfig $samlSetting)
  {
    $this->samlSetting = $samlSetting;
  }
  /**
   * @return SamlConfig
   */
  public function getSamlSetting()
  {
    return $this->samlSetting;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthenticationConfig::class, 'Google_Service_CCAIPlatform_AuthenticationConfig');
