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

namespace Google\Service\SecurityCommandCenter;

class Application extends \Google\Model
{
  /**
   * The base URI that identifies the network location of the application in
   * which the vulnerability was detected. For example, `http://example.com`.
   *
   * @var string
   */
  public $baseUri;
  /**
   * The full URI with payload that can be used to reproduce the vulnerability.
   * For example, `http://example.com?p=aMmYgI6H`.
   *
   * @var string
   */
  public $fullUri;

  /**
   * The base URI that identifies the network location of the application in
   * which the vulnerability was detected. For example, `http://example.com`.
   *
   * @param string $baseUri
   */
  public function setBaseUri($baseUri)
  {
    $this->baseUri = $baseUri;
  }
  /**
   * @return string
   */
  public function getBaseUri()
  {
    return $this->baseUri;
  }
  /**
   * The full URI with payload that can be used to reproduce the vulnerability.
   * For example, `http://example.com?p=aMmYgI6H`.
   *
   * @param string $fullUri
   */
  public function setFullUri($fullUri)
  {
    $this->fullUri = $fullUri;
  }
  /**
   * @return string
   */
  public function getFullUri()
  {
    return $this->fullUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Application::class, 'Google_Service_SecurityCommandCenter_Application');
