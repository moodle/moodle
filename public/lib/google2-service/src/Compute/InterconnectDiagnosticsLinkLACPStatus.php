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

class InterconnectDiagnosticsLinkLACPStatus extends \Google\Model
{
  /**
   * The link is configured and active within the bundle.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The link is not configured within the bundle, this means the rest of the
   * object should be empty.
   */
  public const STATE_DETACHED = 'DETACHED';
  /**
   * System ID of the port on Google's side of the LACP exchange.
   *
   * @var string
   */
  public $googleSystemId;
  /**
   * System ID of the port on the neighbor's side of the LACP exchange.
   *
   * @var string
   */
  public $neighborSystemId;
  /**
   * The state of a LACP link, which can take one of the following values:
   * - ACTIVE: The link is configured and active within the bundle.    -
   * DETACHED: The link is not configured within the bundle. This means    that
   * the rest of the object should be empty.
   *
   * @var string
   */
  public $state;

  /**
   * System ID of the port on Google's side of the LACP exchange.
   *
   * @param string $googleSystemId
   */
  public function setGoogleSystemId($googleSystemId)
  {
    $this->googleSystemId = $googleSystemId;
  }
  /**
   * @return string
   */
  public function getGoogleSystemId()
  {
    return $this->googleSystemId;
  }
  /**
   * System ID of the port on the neighbor's side of the LACP exchange.
   *
   * @param string $neighborSystemId
   */
  public function setNeighborSystemId($neighborSystemId)
  {
    $this->neighborSystemId = $neighborSystemId;
  }
  /**
   * @return string
   */
  public function getNeighborSystemId()
  {
    return $this->neighborSystemId;
  }
  /**
   * The state of a LACP link, which can take one of the following values:
   * - ACTIVE: The link is configured and active within the bundle.    -
   * DETACHED: The link is not configured within the bundle. This means    that
   * the rest of the object should be empty.
   *
   * Accepted values: ACTIVE, DETACHED
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
class_alias(InterconnectDiagnosticsLinkLACPStatus::class, 'Google_Service_Compute_InterconnectDiagnosticsLinkLACPStatus');
