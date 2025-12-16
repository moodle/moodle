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

namespace Google\Service\NetworkServices;

class HttpRouteURLRewrite extends \Google\Model
{
  /**
   * Prior to forwarding the request to the selected destination, the requests
   * host header is replaced by this value.
   *
   * @var string
   */
  public $hostRewrite;
  /**
   * Prior to forwarding the request to the selected destination, the matching
   * portion of the requests path is replaced by this value.
   *
   * @var string
   */
  public $pathPrefixRewrite;

  /**
   * Prior to forwarding the request to the selected destination, the requests
   * host header is replaced by this value.
   *
   * @param string $hostRewrite
   */
  public function setHostRewrite($hostRewrite)
  {
    $this->hostRewrite = $hostRewrite;
  }
  /**
   * @return string
   */
  public function getHostRewrite()
  {
    return $this->hostRewrite;
  }
  /**
   * Prior to forwarding the request to the selected destination, the matching
   * portion of the requests path is replaced by this value.
   *
   * @param string $pathPrefixRewrite
   */
  public function setPathPrefixRewrite($pathPrefixRewrite)
  {
    $this->pathPrefixRewrite = $pathPrefixRewrite;
  }
  /**
   * @return string
   */
  public function getPathPrefixRewrite()
  {
    return $this->pathPrefixRewrite;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpRouteURLRewrite::class, 'Google_Service_NetworkServices_HttpRouteURLRewrite');
