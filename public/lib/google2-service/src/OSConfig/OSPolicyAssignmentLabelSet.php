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

namespace Google\Service\OSConfig;

class OSPolicyAssignmentLabelSet extends \Google\Model
{
  /**
   * Labels are identified by key/value pairs in this map. A VM should contain
   * all the key/value pairs specified in this map to be selected.
   *
   * @var string[]
   */
  public $labels;

  /**
   * Labels are identified by key/value pairs in this map. A VM should contain
   * all the key/value pairs specified in this map to be selected.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OSPolicyAssignmentLabelSet::class, 'Google_Service_OSConfig_OSPolicyAssignmentLabelSet');
