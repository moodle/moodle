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

namespace Google\Service\Networkconnectivity;

class VirtualMachine extends \Google\Collection
{
  protected $collection_key = 'tags';
  /**
   * Optional. A list of VM instance tags that this policy-based route applies
   * to. VM instances that have ANY of tags specified here installs this PBR.
   *
   * @var string[]
   */
  public $tags;

  /**
   * Optional. A list of VM instance tags that this policy-based route applies
   * to. VM instances that have ANY of tags specified here installs this PBR.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VirtualMachine::class, 'Google_Service_Networkconnectivity_VirtualMachine');
