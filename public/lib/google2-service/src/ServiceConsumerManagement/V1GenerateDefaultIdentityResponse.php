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

namespace Google\Service\ServiceConsumerManagement;

class V1GenerateDefaultIdentityResponse extends \Google\Model
{
  /**
   * Indicates that the AttachStatus was not set.
   */
  public const ATTACH_STATUS_ATTACH_STATUS_UNSPECIFIED = 'ATTACH_STATUS_UNSPECIFIED';
  /**
   * The default identity was attached to a role successfully in this request.
   */
  public const ATTACH_STATUS_ATTACHED = 'ATTACHED';
  /**
   * The request specified that no attempt should be made to attach the role.
   */
  public const ATTACH_STATUS_ATTACH_SKIPPED = 'ATTACH_SKIPPED';
  /**
   * Role was attached to the consumer project at some point in time. Tenant
   * manager doesn't make assertion about the current state of the identity with
   * respect to the consumer. Role attachment should happen only once after
   * activation and cannot be reattached after customer removes it. (go/si-
   * attach-role)
   */
  public const ATTACH_STATUS_PREVIOUSLY_ATTACHED = 'PREVIOUSLY_ATTACHED';
  /**
   * Role attachment was denied in this request by customer set org policy.
   * (go/si-attach-role)
   */
  public const ATTACH_STATUS_ATTACH_DENIED_BY_ORG_POLICY = 'ATTACH_DENIED_BY_ORG_POLICY';
  /**
   * Status of the role attachment. Under development (go/si-attach-role),
   * currently always return ATTACH_STATUS_UNSPECIFIED)
   *
   * @var string
   */
  public $attachStatus;
  protected $identityType = V1DefaultIdentity::class;
  protected $identityDataType = '';
  /**
   * Role attached to consumer project. Empty if not attached in this request.
   * (Under development, currently always return empty.)
   *
   * @var string
   */
  public $role;

  /**
   * Status of the role attachment. Under development (go/si-attach-role),
   * currently always return ATTACH_STATUS_UNSPECIFIED)
   *
   * Accepted values: ATTACH_STATUS_UNSPECIFIED, ATTACHED, ATTACH_SKIPPED,
   * PREVIOUSLY_ATTACHED, ATTACH_DENIED_BY_ORG_POLICY
   *
   * @param self::ATTACH_STATUS_* $attachStatus
   */
  public function setAttachStatus($attachStatus)
  {
    $this->attachStatus = $attachStatus;
  }
  /**
   * @return self::ATTACH_STATUS_*
   */
  public function getAttachStatus()
  {
    return $this->attachStatus;
  }
  /**
   * DefaultIdentity that was created or retrieved.
   *
   * @param V1DefaultIdentity $identity
   */
  public function setIdentity(V1DefaultIdentity $identity)
  {
    $this->identity = $identity;
  }
  /**
   * @return V1DefaultIdentity
   */
  public function getIdentity()
  {
    return $this->identity;
  }
  /**
   * Role attached to consumer project. Empty if not attached in this request.
   * (Under development, currently always return empty.)
   *
   * @param string $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return string
   */
  public function getRole()
  {
    return $this->role;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(V1GenerateDefaultIdentityResponse::class, 'Google_Service_ServiceConsumerManagement_V1GenerateDefaultIdentityResponse');
