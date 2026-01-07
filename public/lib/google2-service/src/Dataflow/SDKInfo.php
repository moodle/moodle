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

namespace Google\Service\Dataflow;

class SDKInfo extends \Google\Model
{
  /**
   * UNKNOWN Language.
   */
  public const LANGUAGE_UNKNOWN = 'UNKNOWN';
  /**
   * Java.
   */
  public const LANGUAGE_JAVA = 'JAVA';
  /**
   * Python.
   */
  public const LANGUAGE_PYTHON = 'PYTHON';
  /**
   * Go.
   */
  public const LANGUAGE_GO = 'GO';
  /**
   * YAML.
   */
  public const LANGUAGE_YAML = 'YAML';
  /**
   * Required. The SDK Language.
   *
   * @var string
   */
  public $language;
  /**
   * Optional. The SDK version.
   *
   * @var string
   */
  public $version;

  /**
   * Required. The SDK Language.
   *
   * Accepted values: UNKNOWN, JAVA, PYTHON, GO, YAML
   *
   * @param self::LANGUAGE_* $language
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }
  /**
   * @return self::LANGUAGE_*
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * Optional. The SDK version.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SDKInfo::class, 'Google_Service_Dataflow_SDKInfo');
