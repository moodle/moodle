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

namespace Google\Service\Networkconnectivity;

class DestinationEndpoint extends \Google\Model
{
  /**
   * An invalid state, which is the default case.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The `DestinationEndpoint` resource is valid.
   */
  public const STATE_VALID = 'VALID';
  /**
   * The `DestinationEndpoint` resource is invalid.
   */
  public const STATE_INVALID = 'INVALID';
  /**
   * Required. The ASN of the remote IP prefix.
   *
   * @var string
   */
  public $asn;
  /**
   * Required. The CSP of the remote IP prefix.
   *
   * @var string
   */
  public $csp;
  /**
   * Output only. The state of the `DestinationEndpoint` resource.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Time when the `DestinationEndpoint` resource was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. The ASN of the remote IP prefix.
   *
   * @param string $asn
   */
  public function setAsn($asn)
  {
    $this->asn = $asn;
  }
  /**
   * @return string
   */
  public function getAsn()
  {
    return $this->asn;
  }
  /**
   * Required. The CSP of the remote IP prefix.
   *
   * @param string $csp
   */
  public function setCsp($csp)
  {
    $this->csp = $csp;
  }
  /**
   * @return string
   */
  public function getCsp()
  {
    return $this->csp;
  }
  /**
   * Output only. The state of the `DestinationEndpoint` resource.
   *
   * Accepted values: STATE_UNSPECIFIED, VALID, INVALID
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
  /**
   * Output only. Time when the `DestinationEndpoint` resource was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DestinationEndpoint::class, 'Google_Service_Networkconnectivity_DestinationEndpoint');
