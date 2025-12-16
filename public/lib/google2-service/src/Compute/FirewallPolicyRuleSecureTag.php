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

class FirewallPolicyRuleSecureTag extends \Google\Model
{
  public const STATE_EFFECTIVE = 'EFFECTIVE';
  public const STATE_INEFFECTIVE = 'INEFFECTIVE';
  /**
   * Name of the secure tag, created with TagManager's TagValue API.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. [Output Only] State of the secure tag, either `EFFECTIVE` or
   * `INEFFECTIVE`. A secure tag is `INEFFECTIVE` when it is deleted or its
   * network is deleted.
   *
   * @var string
   */
  public $state;

  /**
   * Name of the secure tag, created with TagManager's TagValue API.
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
   * Output only. [Output Only] State of the secure tag, either `EFFECTIVE` or
   * `INEFFECTIVE`. A secure tag is `INEFFECTIVE` when it is deleted or its
   * network is deleted.
   *
   * Accepted values: EFFECTIVE, INEFFECTIVE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FirewallPolicyRuleSecureTag::class, 'Google_Service_Compute_FirewallPolicyRuleSecureTag');
