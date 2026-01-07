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

class InterconnectMacsecPreSharedKey extends \Google\Model
{
  /**
   * Required. A name for this pre-shared key. The name must be 1-63 characters
   * long, and comply withRFC1035. Specifically, the name must be 1-63
   * characters long and match the regular expression
   * `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character must be a
   * lowercase letter, and all following characters must be a dash, lowercase
   * letter, or digit, except the last character, which cannot be a dash.
   *
   * @var string
   */
  public $name;
  /**
   * A RFC3339 timestamp on or after which the key is valid. startTime can be in
   * the future. If the keychain has a single key, startTime can be omitted. If
   * the keychain has multiple keys, startTime is mandatory for each key. The
   * start times of keys must be in increasing order. The start times of two
   * consecutive keys must be at least 6 hours apart.
   *
   * @var string
   */
  public $startTime;

  /**
   * Required. A name for this pre-shared key. The name must be 1-63 characters
   * long, and comply withRFC1035. Specifically, the name must be 1-63
   * characters long and match the regular expression
   * `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character must be a
   * lowercase letter, and all following characters must be a dash, lowercase
   * letter, or digit, except the last character, which cannot be a dash.
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
   * A RFC3339 timestamp on or after which the key is valid. startTime can be in
   * the future. If the keychain has a single key, startTime can be omitted. If
   * the keychain has multiple keys, startTime is mandatory for each key. The
   * start times of keys must be in increasing order. The start times of two
   * consecutive keys must be at least 6 hours apart.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectMacsecPreSharedKey::class, 'Google_Service_Compute_InterconnectMacsecPreSharedKey');
