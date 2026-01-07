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

namespace Google\Service\Config;

class PreviewResult extends \Google\Model
{
  /**
   * Output only. Plan binary signed URL
   *
   * @var string
   */
  public $binarySignedUri;
  /**
   * Output only. Plan JSON signed URL
   *
   * @var string
   */
  public $jsonSignedUri;

  /**
   * Output only. Plan binary signed URL
   *
   * @param string $binarySignedUri
   */
  public function setBinarySignedUri($binarySignedUri)
  {
    $this->binarySignedUri = $binarySignedUri;
  }
  /**
   * @return string
   */
  public function getBinarySignedUri()
  {
    return $this->binarySignedUri;
  }
  /**
   * Output only. Plan JSON signed URL
   *
   * @param string $jsonSignedUri
   */
  public function setJsonSignedUri($jsonSignedUri)
  {
    $this->jsonSignedUri = $jsonSignedUri;
  }
  /**
   * @return string
   */
  public function getJsonSignedUri()
  {
    return $this->jsonSignedUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PreviewResult::class, 'Google_Service_Config_PreviewResult');
