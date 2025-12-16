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

class InterconnectGroupInterconnect extends \Google\Model
{
  /**
   * The URL of an Interconnect in this group. All Interconnects in the group
   * are unique.
   *
   * @var string
   */
  public $interconnect;

  /**
   * The URL of an Interconnect in this group. All Interconnects in the group
   * are unique.
   *
   * @param string $interconnect
   */
  public function setInterconnect($interconnect)
  {
    $this->interconnect = $interconnect;
  }
  /**
   * @return string
   */
  public function getInterconnect()
  {
    return $this->interconnect;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectGroupInterconnect::class, 'Google_Service_Compute_InterconnectGroupInterconnect');
