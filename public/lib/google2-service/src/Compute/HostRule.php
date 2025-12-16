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

namespace Google\Service\Compute;

class HostRule extends \Google\Collection
{
  protected $collection_key = 'hosts';
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * The list of host patterns to match. They must be valid hostnames with
   * optional port numbers in the format host:port.* matches any string of
   * ([a-z0-9-.]*). In that case, * must be the first character, and if followed
   * by anything, the immediate following character must be either - or ..
   *
   * * based matching is not supported when the URL map is bound to a target
   * gRPC proxy that has the validateForProxyless field set to true.
   *
   * @var string[]
   */
  public $hosts;
  /**
   * The name of the PathMatcher to use to match the path portion of the URL if
   * the hostRule matches the URL's host portion.
   *
   * @var string
   */
  public $pathMatcher;

  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The list of host patterns to match. They must be valid hostnames with
   * optional port numbers in the format host:port.* matches any string of
   * ([a-z0-9-.]*). In that case, * must be the first character, and if followed
   * by anything, the immediate following character must be either - or ..
   *
   * * based matching is not supported when the URL map is bound to a target
   * gRPC proxy that has the validateForProxyless field set to true.
   *
   * @param string[] $hosts
   */
  public function setHosts($hosts)
  {
    $this->hosts = $hosts;
  }
  /**
   * @return string[]
   */
  public function getHosts()
  {
    return $this->hosts;
  }
  /**
   * The name of the PathMatcher to use to match the path portion of the URL if
   * the hostRule matches the URL's host portion.
   *
   * @param string $pathMatcher
   */
  public function setPathMatcher($pathMatcher)
  {
    $this->pathMatcher = $pathMatcher;
  }
  /**
   * @return string
   */
  public function getPathMatcher()
  {
    return $this->pathMatcher;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HostRule::class, 'Google_Service_Compute_HostRule');
