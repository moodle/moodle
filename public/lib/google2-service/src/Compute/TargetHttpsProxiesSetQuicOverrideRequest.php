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

class TargetHttpsProxiesSetQuicOverrideRequest extends \Google\Model
{
  /**
   * The load balancer will not attempt to negotiate QUIC with clients.
   */
  public const QUIC_OVERRIDE_DISABLE = 'DISABLE';
  /**
   * The load balancer will attempt to negotiate QUIC with clients.
   */
  public const QUIC_OVERRIDE_ENABLE = 'ENABLE';
  /**
   * No overrides to the default QUIC policy. This option is implicit if no QUIC
   * override has been specified in the request.
   */
  public const QUIC_OVERRIDE_NONE = 'NONE';
  /**
   * QUIC policy for the TargetHttpsProxy resource.
   *
   * @var string
   */
  public $quicOverride;

  /**
   * QUIC policy for the TargetHttpsProxy resource.
   *
   * Accepted values: DISABLE, ENABLE, NONE
   *
   * @param self::QUIC_OVERRIDE_* $quicOverride
   */
  public function setQuicOverride($quicOverride)
  {
    $this->quicOverride = $quicOverride;
  }
  /**
   * @return self::QUIC_OVERRIDE_*
   */
  public function getQuicOverride()
  {
    return $this->quicOverride;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TargetHttpsProxiesSetQuicOverrideRequest::class, 'Google_Service_Compute_TargetHttpsProxiesSetQuicOverrideRequest');
