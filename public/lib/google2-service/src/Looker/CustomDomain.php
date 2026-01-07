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

namespace Google\Service\Looker;

class CustomDomain extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_CUSTOM_DOMAIN_STATE_UNSPECIFIED = 'CUSTOM_DOMAIN_STATE_UNSPECIFIED';
  /**
   * DNS record is not created.
   */
  public const STATE_UNVERIFIED = 'UNVERIFIED';
  /**
   * DNS record is created.
   */
  public const STATE_VERIFIED = 'VERIFIED';
  /**
   * Calling SLM to update.
   */
  public const STATE_MODIFYING = 'MODIFYING';
  /**
   * ManagedCertificate is ready.
   */
  public const STATE_AVAILABLE = 'AVAILABLE';
  /**
   * ManagedCertificate is not ready.
   */
  public const STATE_UNAVAILABLE = 'UNAVAILABLE';
  /**
   * Status is not known.
   */
  public const STATE_UNKNOWN = 'UNKNOWN';
  /**
   * Domain name.
   *
   * @var string
   */
  public $domain;
  /**
   * Domain state.
   *
   * @var string
   */
  public $state;

  /**
   * Domain name.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * Domain state.
   *
   * Accepted values: CUSTOM_DOMAIN_STATE_UNSPECIFIED, UNVERIFIED, VERIFIED,
   * MODIFYING, AVAILABLE, UNAVAILABLE, UNKNOWN
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
class_alias(CustomDomain::class, 'Google_Service_Looker_CustomDomain');
