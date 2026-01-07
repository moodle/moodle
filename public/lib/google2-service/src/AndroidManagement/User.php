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

class User extends \Google\Model
{
  /**
   * A unique identifier you create for this user, such as user342 or
   * asset#44418. This field must be set when the user is created and can't be
   * updated. This field must not contain personally identifiable information
   * (PII). This identifier must be 1024 characters or less; otherwise, the
   * update policy request will fail.
   *
   * @var string
   */
  public $accountIdentifier;

  /**
   * A unique identifier you create for this user, such as user342 or
   * asset#44418. This field must be set when the user is created and can't be
   * updated. This field must not contain personally identifiable information
   * (PII). This identifier must be 1024 characters or less; otherwise, the
   * update policy request will fail.
   *
   * @param string $accountIdentifier
   */
  public function setAccountIdentifier($accountIdentifier)
  {
    $this->accountIdentifier = $accountIdentifier;
  }
  /**
   * @return string
   */
  public function getAccountIdentifier()
  {
    return $this->accountIdentifier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(User::class, 'Google_Service_AndroidManagement_User');
