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

namespace Google\Service\FirebaseManagement;

class AndroidAppConfig extends \Google\Model
{
  /**
   * The contents of the JSON configuration file.
   *
   * @var string
   */
  public $configFileContents;
  /**
   * The filename that the configuration artifact for the `AndroidApp` is
   * typically saved as. For example: `google-services.json`
   *
   * @var string
   */
  public $configFilename;

  /**
   * The contents of the JSON configuration file.
   *
   * @param string $configFileContents
   */
  public function setConfigFileContents($configFileContents)
  {
    $this->configFileContents = $configFileContents;
  }
  /**
   * @return string
   */
  public function getConfigFileContents()
  {
    return $this->configFileContents;
  }
  /**
   * The filename that the configuration artifact for the `AndroidApp` is
   * typically saved as. For example: `google-services.json`
   *
   * @param string $configFilename
   */
  public function setConfigFilename($configFilename)
  {
    $this->configFilename = $configFilename;
  }
  /**
   * @return string
   */
  public function getConfigFilename()
  {
    return $this->configFilename;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AndroidAppConfig::class, 'Google_Service_FirebaseManagement_AndroidAppConfig');
