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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaSerializedFile extends \Google\Model
{
  /**
   * Default value.
   */
  public const FILE_INTEGRATION_FILE_UNSPECIFIED = 'INTEGRATION_FILE_UNSPECIFIED';
  /**
   * Integration file.
   */
  public const FILE_INTEGRATION = 'INTEGRATION';
  /**
   * Integration Config variables.
   */
  public const FILE_INTEGRATION_CONFIG_VARIABLES = 'INTEGRATION_CONFIG_VARIABLES';
  /**
   * String representation of the file content.
   *
   * @var string
   */
  public $content;
  /**
   * File information like Integration version, Integration Config variables
   * etc.
   *
   * @var string
   */
  public $file;

  /**
   * String representation of the file content.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * File information like Integration version, Integration Config variables
   * etc.
   *
   * Accepted values: INTEGRATION_FILE_UNSPECIFIED, INTEGRATION,
   * INTEGRATION_CONFIG_VARIABLES
   *
   * @param self::FILE_* $file
   */
  public function setFile($file)
  {
    $this->file = $file;
  }
  /**
   * @return self::FILE_*
   */
  public function getFile()
  {
    return $this->file;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaSerializedFile::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaSerializedFile');
