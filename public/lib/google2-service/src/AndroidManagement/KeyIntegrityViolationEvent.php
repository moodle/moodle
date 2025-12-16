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

namespace Google\Service\AndroidManagement;

class KeyIntegrityViolationEvent extends \Google\Model
{
  /**
   * UID of the application which owns the key
   *
   * @var int
   */
  public $applicationUid;
  /**
   * Alias of the key.
   *
   * @var string
   */
  public $keyAlias;

  /**
   * UID of the application which owns the key
   *
   * @param int $applicationUid
   */
  public function setApplicationUid($applicationUid)
  {
    $this->applicationUid = $applicationUid;
  }
  /**
   * @return int
   */
  public function getApplicationUid()
  {
    return $this->applicationUid;
  }
  /**
   * Alias of the key.
   *
   * @param string $keyAlias
   */
  public function setKeyAlias($keyAlias)
  {
    $this->keyAlias = $keyAlias;
  }
  /**
   * @return string
   */
  public function getKeyAlias()
  {
    return $this->keyAlias;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KeyIntegrityViolationEvent::class, 'Google_Service_AndroidManagement_KeyIntegrityViolationEvent');
