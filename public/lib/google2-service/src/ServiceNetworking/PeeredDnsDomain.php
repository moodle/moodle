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

namespace Google\Service\ServiceNetworking;

class PeeredDnsDomain extends \Google\Model
{
  /**
   * The DNS domain name suffix e.g. `example.com.`. Cloud DNS requires that a
   * DNS suffix ends with a trailing dot.
   *
   * @var string
   */
  public $dnsSuffix;
  /**
   * Required. User assigned name for this resource. Must be unique within the
   * consumer network. The name must be 1-63 characters long, must begin with a
   * letter, end with a letter or digit, and only contain lowercase letters,
   * digits or dashes.
   *
   * @var string
   */
  public $name;

  /**
   * The DNS domain name suffix e.g. `example.com.`. Cloud DNS requires that a
   * DNS suffix ends with a trailing dot.
   *
   * @param string $dnsSuffix
   */
  public function setDnsSuffix($dnsSuffix)
  {
    $this->dnsSuffix = $dnsSuffix;
  }
  /**
   * @return string
   */
  public function getDnsSuffix()
  {
    return $this->dnsSuffix;
  }
  /**
   * Required. User assigned name for this resource. Must be unique within the
   * consumer network. The name must be 1-63 characters long, must begin with a
   * letter, end with a letter or digit, and only contain lowercase letters,
   * digits or dashes.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PeeredDnsDomain::class, 'Google_Service_ServiceNetworking_PeeredDnsDomain');
