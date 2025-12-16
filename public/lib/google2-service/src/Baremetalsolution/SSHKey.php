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

namespace Google\Service\Baremetalsolution;

class SSHKey extends \Google\Model
{
  /**
   * Output only. The name of this SSH key. Currently, the only valid value for
   * the location is "global".
   *
   * @var string
   */
  public $name;
  /**
   * The public SSH key. This must be in OpenSSH .authorized_keys format.
   *
   * @var string
   */
  public $publicKey;

  /**
   * Output only. The name of this SSH key. Currently, the only valid value for
   * the location is "global".
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
   * The public SSH key. This must be in OpenSSH .authorized_keys format.
   *
   * @param string $publicKey
   */
  public function setPublicKey($publicKey)
  {
    $this->publicKey = $publicKey;
  }
  /**
   * @return string
   */
  public function getPublicKey()
  {
    return $this->publicKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SSHKey::class, 'Google_Service_Baremetalsolution_SSHKey');
