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

namespace Google\Service\Reseller;

class PrimaryAdmin extends \Google\Model
{
  /**
   * The business email of the primary administrator of the customer. The email
   * verification link is sent to this email address at the time of customer
   * creation. Primary administrators have access to the customer's Admin
   * Console, including the ability to invite and evict users and manage the
   * administrative needs of the customer.
   *
   * @var string
   */
  public $primaryEmail;

  /**
   * The business email of the primary administrator of the customer. The email
   * verification link is sent to this email address at the time of customer
   * creation. Primary administrators have access to the customer's Admin
   * Console, including the ability to invite and evict users and manage the
   * administrative needs of the customer.
   *
   * @param string $primaryEmail
   */
  public function setPrimaryEmail($primaryEmail)
  {
    $this->primaryEmail = $primaryEmail;
  }
  /**
   * @return string
   */
  public function getPrimaryEmail()
  {
    return $this->primaryEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrimaryAdmin::class, 'Google_Service_Reseller_PrimaryAdmin');
