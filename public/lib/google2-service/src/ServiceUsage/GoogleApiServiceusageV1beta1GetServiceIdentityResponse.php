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

namespace Google\Service\ServiceUsage;

class GoogleApiServiceusageV1beta1GetServiceIdentityResponse extends \Google\Model
{
  /**
   * Default service identity state. This value is used if the state is omitted.
   */
  public const STATE_IDENTITY_STATE_UNSPECIFIED = 'IDENTITY_STATE_UNSPECIFIED';
  /**
   * Service identity has been created and can be used.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  protected $identityType = GoogleApiServiceusageV1beta1ServiceIdentity::class;
  protected $identityDataType = '';
  /**
   * Service identity state.
   *
   * @var string
   */
  public $state;

  /**
   * Service identity that service producer can use to access consumer
   * resources. If exists is true, it contains email and unique_id. If exists is
   * false, it contains pre-constructed email and empty unique_id.
   *
   * @param GoogleApiServiceusageV1beta1ServiceIdentity $identity
   */
  public function setIdentity(GoogleApiServiceusageV1beta1ServiceIdentity $identity)
  {
    $this->identity = $identity;
  }
  /**
   * @return GoogleApiServiceusageV1beta1ServiceIdentity
   */
  public function getIdentity()
  {
    return $this->identity;
  }
  /**
   * Service identity state.
   *
   * Accepted values: IDENTITY_STATE_UNSPECIFIED, ACTIVE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleApiServiceusageV1beta1GetServiceIdentityResponse::class, 'Google_Service_ServiceUsage_GoogleApiServiceusageV1beta1GetServiceIdentityResponse');
