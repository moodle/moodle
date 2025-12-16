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

class FirewallPolicyListWarningData extends \Google\Model
{
  /**
   * [Output Only] A key that provides more detail on the warning being
   * returned. For example, for warnings where there are no results in a list
   * request for a particular zone, this key might be scope and the key value
   * might be the zone name. Other examples might be a key indicating a
   * deprecated resource and a suggested replacement, or a warning about invalid
   * network settings (for example, if an instance attempts to perform IP
   * forwarding but is not enabled for IP forwarding).
   *
   * @var string
   */
  public $key;
  /**
   * [Output Only] A warning data value corresponding to the key.
   *
   * @var string
   */
  public $value;

  /**
   * [Output Only] A key that provides more detail on the warning being
   * returned. For example, for warnings where there are no results in a list
   * request for a particular zone, this key might be scope and the key value
   * might be the zone name. Other examples might be a key indicating a
   * deprecated resource and a suggested replacement, or a warning about invalid
   * network settings (for example, if an instance attempts to perform IP
   * forwarding but is not enabled for IP forwarding).
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * [Output Only] A warning data value corresponding to the key.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FirewallPolicyListWarningData::class, 'Google_Service_Compute_FirewallPolicyListWarningData');
