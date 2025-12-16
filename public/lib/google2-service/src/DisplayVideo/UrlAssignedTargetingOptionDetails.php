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

namespace Google\Service\DisplayVideo;

class UrlAssignedTargetingOptionDetails extends \Google\Model
{
  /**
   * Indicates if this option is being negatively targeted.
   *
   * @var bool
   */
  public $negative;
  /**
   * Required. The URL, for example `example.com`. DV360 supports two levels of
   * subdirectory targeting, for example `www.example.com/one-subdirectory-
   * level/second-level`, and five levels of subdomain targeting, for example
   * `five.four.three.two.one.example.com`.
   *
   * @var string
   */
  public $url;

  /**
   * Indicates if this option is being negatively targeted.
   *
   * @param bool $negative
   */
  public function setNegative($negative)
  {
    $this->negative = $negative;
  }
  /**
   * @return bool
   */
  public function getNegative()
  {
    return $this->negative;
  }
  /**
   * Required. The URL, for example `example.com`. DV360 supports two levels of
   * subdirectory targeting, for example `www.example.com/one-subdirectory-
   * level/second-level`, and five levels of subdomain targeting, for example
   * `five.four.three.two.one.example.com`.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UrlAssignedTargetingOptionDetails::class, 'Google_Service_DisplayVideo_UrlAssignedTargetingOptionDetails');
