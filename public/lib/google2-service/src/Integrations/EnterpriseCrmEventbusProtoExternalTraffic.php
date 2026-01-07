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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoExternalTraffic extends \Google\Model
{
  public const SOURCE_SOURCE_UNSPECIFIED = 'SOURCE_UNSPECIFIED';
  public const SOURCE_APIGEE = 'APIGEE';
  public const SOURCE_SECURITY = 'SECURITY';
  /**
   * Indicates the client enables internal IP feature, this is applicable for
   * internal clients only.
   *
   * @var bool
   */
  public $enableInternalIp;
  /**
   * User’s GCP project id the traffic is referring to.
   *
   * @var string
   */
  public $gcpProjectId;
  /**
   * User’s GCP project number the traffic is referring to.
   *
   * @var string
   */
  public $gcpProjectNumber;
  /**
   * Location for the user's request.
   *
   * @var string
   */
  public $location;
  /**
   * Enqueue the execution request due to quota issue
   *
   * @var bool
   */
  public $retryRequestForQuota;
  /**
   * @var string
   */
  public $source;

  /**
   * Indicates the client enables internal IP feature, this is applicable for
   * internal clients only.
   *
   * @param bool $enableInternalIp
   */
  public function setEnableInternalIp($enableInternalIp)
  {
    $this->enableInternalIp = $enableInternalIp;
  }
  /**
   * @return bool
   */
  public function getEnableInternalIp()
  {
    return $this->enableInternalIp;
  }
  /**
   * User’s GCP project id the traffic is referring to.
   *
   * @param string $gcpProjectId
   */
  public function setGcpProjectId($gcpProjectId)
  {
    $this->gcpProjectId = $gcpProjectId;
  }
  /**
   * @return string
   */
  public function getGcpProjectId()
  {
    return $this->gcpProjectId;
  }
  /**
   * User’s GCP project number the traffic is referring to.
   *
   * @param string $gcpProjectNumber
   */
  public function setGcpProjectNumber($gcpProjectNumber)
  {
    $this->gcpProjectNumber = $gcpProjectNumber;
  }
  /**
   * @return string
   */
  public function getGcpProjectNumber()
  {
    return $this->gcpProjectNumber;
  }
  /**
   * Location for the user's request.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Enqueue the execution request due to quota issue
   *
   * @param bool $retryRequestForQuota
   */
  public function setRetryRequestForQuota($retryRequestForQuota)
  {
    $this->retryRequestForQuota = $retryRequestForQuota;
  }
  /**
   * @return bool
   */
  public function getRetryRequestForQuota()
  {
    return $this->retryRequestForQuota;
  }
  /**
   * @param self::SOURCE_* $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return self::SOURCE_*
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoExternalTraffic::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoExternalTraffic');
