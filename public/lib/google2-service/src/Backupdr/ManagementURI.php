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

namespace Google\Service\Backupdr;

class ManagementURI extends \Google\Model
{
  /**
   * Output only. The ManagementServer AGM/RD API URL.
   *
   * @var string
   */
  public $api;
  /**
   * Output only. The ManagementServer AGM/RD WebUI URL.
   *
   * @var string
   */
  public $webUi;

  /**
   * Output only. The ManagementServer AGM/RD API URL.
   *
   * @param string $api
   */
  public function setApi($api)
  {
    $this->api = $api;
  }
  /**
   * @return string
   */
  public function getApi()
  {
    return $this->api;
  }
  /**
   * Output only. The ManagementServer AGM/RD WebUI URL.
   *
   * @param string $webUi
   */
  public function setWebUi($webUi)
  {
    $this->webUi = $webUi;
  }
  /**
   * @return string
   */
  public function getWebUi()
  {
    return $this->webUi;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ManagementURI::class, 'Google_Service_Backupdr_ManagementURI');
