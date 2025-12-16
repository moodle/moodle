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

namespace Google\Service\CloudRun;

class GoogleDevtoolsCloudbuildV1GitConfig extends \Google\Model
{
  protected $httpType = GoogleDevtoolsCloudbuildV1HttpConfig::class;
  protected $httpDataType = '';

  /**
   * Configuration for HTTP related git operations.
   *
   * @param GoogleDevtoolsCloudbuildV1HttpConfig $http
   */
  public function setHttp(GoogleDevtoolsCloudbuildV1HttpConfig $http)
  {
    $this->http = $http;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1HttpConfig
   */
  public function getHttp()
  {
    return $this->http;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDevtoolsCloudbuildV1GitConfig::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1GitConfig');
