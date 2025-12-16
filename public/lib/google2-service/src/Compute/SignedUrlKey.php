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

class SignedUrlKey extends \Google\Model
{
  /**
   * Name of the key. The name must be 1-63 characters long, and comply
   * withRFC1035. Specifically, the name must be 1-63 characters long and match
   * the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first
   * character must be a lowercase letter, and all following characters must be
   * a dash, lowercase letter, or digit, except the last character, which cannot
   * be a dash.
   *
   * @var string
   */
  public $keyName;
  /**
   * 128-bit key value used for signing the URL. The key value must be a
   * validRFC 4648 Section 5 base64url encoded string.
   *
   * @var string
   */
  public $keyValue;

  /**
   * Name of the key. The name must be 1-63 characters long, and comply
   * withRFC1035. Specifically, the name must be 1-63 characters long and match
   * the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first
   * character must be a lowercase letter, and all following characters must be
   * a dash, lowercase letter, or digit, except the last character, which cannot
   * be a dash.
   *
   * @param string $keyName
   */
  public function setKeyName($keyName)
  {
    $this->keyName = $keyName;
  }
  /**
   * @return string
   */
  public function getKeyName()
  {
    return $this->keyName;
  }
  /**
   * 128-bit key value used for signing the URL. The key value must be a
   * validRFC 4648 Section 5 base64url encoded string.
   *
   * @param string $keyValue
   */
  public function setKeyValue($keyValue)
  {
    $this->keyValue = $keyValue;
  }
  /**
   * @return string
   */
  public function getKeyValue()
  {
    return $this->keyValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SignedUrlKey::class, 'Google_Service_Compute_SignedUrlKey');
