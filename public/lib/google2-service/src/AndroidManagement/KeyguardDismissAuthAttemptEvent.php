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

class KeyguardDismissAuthAttemptEvent extends \Google\Model
{
  /**
   * Whether a strong form of authentication (password, PIN, or pattern) was
   * used to unlock device.
   *
   * @var bool
   */
  public $strongAuthMethodUsed;
  /**
   * Whether the unlock attempt was successful.
   *
   * @var bool
   */
  public $success;

  /**
   * Whether a strong form of authentication (password, PIN, or pattern) was
   * used to unlock device.
   *
   * @param bool $strongAuthMethodUsed
   */
  public function setStrongAuthMethodUsed($strongAuthMethodUsed)
  {
    $this->strongAuthMethodUsed = $strongAuthMethodUsed;
  }
  /**
   * @return bool
   */
  public function getStrongAuthMethodUsed()
  {
    return $this->strongAuthMethodUsed;
  }
  /**
   * Whether the unlock attempt was successful.
   *
   * @param bool $success
   */
  public function setSuccess($success)
  {
    $this->success = $success;
  }
  /**
   * @return bool
   */
  public function getSuccess()
  {
    return $this->success;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KeyguardDismissAuthAttemptEvent::class, 'Google_Service_AndroidManagement_KeyguardDismissAuthAttemptEvent');
