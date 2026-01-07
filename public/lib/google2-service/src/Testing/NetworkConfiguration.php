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

namespace Google\Service\Testing;

class NetworkConfiguration extends \Google\Model
{
  protected $downRuleType = TrafficRule::class;
  protected $downRuleDataType = '';
  /**
   * The unique opaque id for this network traffic configuration.
   *
   * @var string
   */
  public $id;
  protected $upRuleType = TrafficRule::class;
  protected $upRuleDataType = '';

  /**
   * The emulation rule applying to the download traffic.
   *
   * @param TrafficRule $downRule
   */
  public function setDownRule(TrafficRule $downRule)
  {
    $this->downRule = $downRule;
  }
  /**
   * @return TrafficRule
   */
  public function getDownRule()
  {
    return $this->downRule;
  }
  /**
   * The unique opaque id for this network traffic configuration.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The emulation rule applying to the upload traffic.
   *
   * @param TrafficRule $upRule
   */
  public function setUpRule(TrafficRule $upRule)
  {
    $this->upRule = $upRule;
  }
  /**
   * @return TrafficRule
   */
  public function getUpRule()
  {
    return $this->upRule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkConfiguration::class, 'Google_Service_Testing_NetworkConfiguration');
