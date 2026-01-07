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

namespace Google\Service\ShoppingContent;

class AccountIdentityType extends \Google\Model
{
  /**
   * Optional. Indicates that the business identifies itself with a given
   * identity type. Setting this field does not automatically mean eligibility
   * for promotions.
   *
   * @var bool
   */
  public $selfIdentified;

  /**
   * Optional. Indicates that the business identifies itself with a given
   * identity type. Setting this field does not automatically mean eligibility
   * for promotions.
   *
   * @param bool $selfIdentified
   */
  public function setSelfIdentified($selfIdentified)
  {
    $this->selfIdentified = $selfIdentified;
  }
  /**
   * @return bool
   */
  public function getSelfIdentified()
  {
    return $this->selfIdentified;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountIdentityType::class, 'Google_Service_ShoppingContent_AccountIdentityType');
