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

class Reference extends \Google\Model
{
  /**
   * Source of the reference e.g. NVD
   *
   * @var string
   */
  public $source;
  /**
   * Uri for the mentioned source e.g. https://cve.mitre.org/cgi-
   * bin/cvename.cgi?name=CVE-2021-34527.
   *
   * @var string
   */
  public $uri;

  /**
   * Source of the reference e.g. NVD
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Uri for the mentioned source e.g. https://cve.mitre.org/cgi-
   * bin/cvename.cgi?name=CVE-2021-34527.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Reference::class, 'Google_Service_SecurityCommandCenter_Reference');
