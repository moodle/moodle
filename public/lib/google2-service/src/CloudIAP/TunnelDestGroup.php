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

namespace Google\Service\CloudIAP;

class TunnelDestGroup extends \Google\Collection
{
  protected $collection_key = 'fqdns';
  /**
   * Optional. Unordered list. List of CIDRs that this group applies to.
   *
   * @var string[]
   */
  public $cidrs;
  /**
   * Optional. Unordered list. List of FQDNs that this group applies to.
   *
   * @var string[]
   */
  public $fqdns;
  /**
   * Identifier. Identifier for the TunnelDestGroup. Must be unique within the
   * project and contain only lower case letters (a-z) and dashes (-).
   *
   * @var string
   */
  public $name;

  /**
   * Optional. Unordered list. List of CIDRs that this group applies to.
   *
   * @param string[] $cidrs
   */
  public function setCidrs($cidrs)
  {
    $this->cidrs = $cidrs;
  }
  /**
   * @return string[]
   */
  public function getCidrs()
  {
    return $this->cidrs;
  }
  /**
   * Optional. Unordered list. List of FQDNs that this group applies to.
   *
   * @param string[] $fqdns
   */
  public function setFqdns($fqdns)
  {
    $this->fqdns = $fqdns;
  }
  /**
   * @return string[]
   */
  public function getFqdns()
  {
    return $this->fqdns;
  }
  /**
   * Identifier. Identifier for the TunnelDestGroup. Must be unique within the
   * project and contain only lower case letters (a-z) and dashes (-).
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
class_alias(TunnelDestGroup::class, 'Google_Service_CloudIAP_TunnelDestGroup');
