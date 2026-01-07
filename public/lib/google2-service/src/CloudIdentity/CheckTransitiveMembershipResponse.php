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

namespace Google\Service\CloudIdentity;

class CheckTransitiveMembershipResponse extends \Google\Model
{
  /**
   * Response does not include the possible roles of a member since the behavior
   * of this rpc is not all-or-nothing unlike the other rpcs. So, it may not be
   * possible to list all the roles definitively, due to possible lack of
   * authorization in some of the paths.
   *
   * @var bool
   */
  public $hasMembership;

  /**
   * Response does not include the possible roles of a member since the behavior
   * of this rpc is not all-or-nothing unlike the other rpcs. So, it may not be
   * possible to list all the roles definitively, due to possible lack of
   * authorization in some of the paths.
   *
   * @param bool $hasMembership
   */
  public function setHasMembership($hasMembership)
  {
    $this->hasMembership = $hasMembership;
  }
  /**
   * @return bool
   */
  public function getHasMembership()
  {
    return $this->hasMembership;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CheckTransitiveMembershipResponse::class, 'Google_Service_CloudIdentity_CheckTransitiveMembershipResponse');
