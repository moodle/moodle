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

class RouterMd5AuthenticationKey extends \Google\Model
{
  /**
   * [Input only] Value of the key.
   *
   * For patch and update calls, it can be skipped to copy the value from the
   * previous configuration. This is allowed if the key with the same name
   * existed before the operation. Maximum length is 80 characters. Can only
   * contain printable ASCII characters.
   *
   * @var string
   */
  public $key;
  /**
   * Name used to identify the key.
   *
   * Must be unique within a router. Must be referenced by exactly one bgpPeer.
   * Must comply withRFC1035.
   *
   * @var string
   */
  public $name;

  /**
   * [Input only] Value of the key.
   *
   * For patch and update calls, it can be skipped to copy the value from the
   * previous configuration. This is allowed if the key with the same name
   * existed before the operation. Maximum length is 80 characters. Can only
   * contain printable ASCII characters.
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
   * Name used to identify the key.
   *
   * Must be unique within a router. Must be referenced by exactly one bgpPeer.
   * Must comply withRFC1035.
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
class_alias(RouterMd5AuthenticationKey::class, 'Google_Service_Compute_RouterMd5AuthenticationKey');
