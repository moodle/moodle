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

namespace Google\Service\DeveloperConnect;

class GitProxyConfig extends \Google\Model
{
  /**
   * Optional. Setting this to true allows the git proxy to be used for
   * performing git operations on the repositories linked in the connection.
   *
   * @var bool
   */
  public $enabled;

  /**
   * Optional. Setting this to true allows the git proxy to be used for
   * performing git operations on the repositories linked in the connection.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GitProxyConfig::class, 'Google_Service_DeveloperConnect_GitProxyConfig');
