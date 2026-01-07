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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1ProjectServiceTerms extends \Google\Model
{
  /**
   * The default value of the enum. This value is not actually used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The project has given consent to the terms of service.
   */
  public const STATE_TERMS_ACCEPTED = 'TERMS_ACCEPTED';
  /**
   * The project is pending to review and accept the terms of service.
   */
  public const STATE_TERMS_PENDING = 'TERMS_PENDING';
  /**
   * The project has declined or revoked the agreement to terms of service.
   */
  public const STATE_TERMS_DECLINED = 'TERMS_DECLINED';
  /**
   * The last time when the project agreed to the terms of service.
   *
   * @var string
   */
  public $acceptTime;
  /**
   * The last time when the project declined or revoked the agreement to terms
   * of service.
   *
   * @var string
   */
  public $declineTime;
  /**
   * The unique identifier of this terms of service. Available terms: *
   * `GA_DATA_USE_TERMS`: [Terms for data
   * use](https://cloud.google.com/retail/data-use-terms). When using this as
   * `id`, the acceptable version to provide is `2022-11-23`.
   *
   * @var string
   */
  public $id;
  /**
   * Whether the project has accepted/rejected the service terms or it is still
   * pending.
   *
   * @var string
   */
  public $state;
  /**
   * The version string of the terms of service. For acceptable values, see the
   * comments for id above.
   *
   * @var string
   */
  public $version;

  /**
   * The last time when the project agreed to the terms of service.
   *
   * @param string $acceptTime
   */
  public function setAcceptTime($acceptTime)
  {
    $this->acceptTime = $acceptTime;
  }
  /**
   * @return string
   */
  public function getAcceptTime()
  {
    return $this->acceptTime;
  }
  /**
   * The last time when the project declined or revoked the agreement to terms
   * of service.
   *
   * @param string $declineTime
   */
  public function setDeclineTime($declineTime)
  {
    $this->declineTime = $declineTime;
  }
  /**
   * @return string
   */
  public function getDeclineTime()
  {
    return $this->declineTime;
  }
  /**
   * The unique identifier of this terms of service. Available terms: *
   * `GA_DATA_USE_TERMS`: [Terms for data
   * use](https://cloud.google.com/retail/data-use-terms). When using this as
   * `id`, the acceptable version to provide is `2022-11-23`.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Whether the project has accepted/rejected the service terms or it is still
   * pending.
   *
   * Accepted values: STATE_UNSPECIFIED, TERMS_ACCEPTED, TERMS_PENDING,
   * TERMS_DECLINED
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
   * The version string of the terms of service. For acceptable values, see the
   * comments for id above.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1ProjectServiceTerms::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ProjectServiceTerms');
