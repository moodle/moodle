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

class ConsistentHashLoadBalancerSettingsHttpCookie extends \Google\Model
{
  /**
   * Name of the cookie.
   *
   * @var string
   */
  public $name;
  /**
   * Path to set for the cookie.
   *
   * @var string
   */
  public $path;
  protected $ttlType = Duration::class;
  protected $ttlDataType = '';

  /**
   * Name of the cookie.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Path to set for the cookie.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Lifetime of the cookie.
   *
   * @param Duration $ttl
   */
  public function setTtl(Duration $ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return Duration
   */
  public function getTtl()
  {
    return $this->ttl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConsistentHashLoadBalancerSettingsHttpCookie::class, 'Google_Service_Compute_ConsistentHashLoadBalancerSettingsHttpCookie');
