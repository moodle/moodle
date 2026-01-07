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

namespace Google\Service\SecureSourceManager;

class URIs extends \Google\Model
{
  /**
   * Output only. API is the URI for API access.
   *
   * @var string
   */
  public $api;
  /**
   * Output only. git_https is the git HTTPS URI for git operations.
   *
   * @var string
   */
  public $gitHttps;
  /**
   * Output only. HTML is the URI for user to view the repository in a browser.
   *
   * @var string
   */
  public $html;

  /**
   * Output only. API is the URI for API access.
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
   * Output only. git_https is the git HTTPS URI for git operations.
   *
   * @param string $gitHttps
   */
  public function setGitHttps($gitHttps)
  {
    $this->gitHttps = $gitHttps;
  }
  /**
   * @return string
   */
  public function getGitHttps()
  {
    return $this->gitHttps;
  }
  /**
   * Output only. HTML is the URI for user to view the repository in a browser.
   *
   * @param string $html
   */
  public function setHtml($html)
  {
    $this->html = $html;
  }
  /**
   * @return string
   */
  public function getHtml()
  {
    return $this->html;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(URIs::class, 'Google_Service_SecureSourceManager_URIs');
