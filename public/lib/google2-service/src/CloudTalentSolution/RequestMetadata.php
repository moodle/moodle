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

namespace Google\Service\CloudTalentSolution;

class RequestMetadata extends \Google\Model
{
  /**
   * Only set when any of domain, session_id and user_id isn't available for
   * some reason. It is highly recommended not to set this field and provide
   * accurate domain, session_id and user_id for the best service experience.
   *
   * @var bool
   */
  public $allowMissingIds;
  protected $deviceInfoType = DeviceInfo::class;
  protected $deviceInfoDataType = '';
  /**
   * Required if allow_missing_ids is unset or `false`. The client-defined scope
   * or source of the service call, which typically is the domain on which the
   * service has been implemented and is currently being run. For example, if
   * the service is being run by client *Foo, Inc.*, on job board www.foo.com
   * and career site www.bar.com, then this field is set to "foo.com" for use on
   * the job board, and "bar.com" for use on the career site. Note that any
   * improvements to the model for a particular tenant site rely on this field
   * being set correctly to a unique domain. The maximum number of allowed
   * characters is 255.
   *
   * @var string
   */
  public $domain;
  /**
   * Required if allow_missing_ids is unset or `false`. A unique session
   * identification string. A session is defined as the duration of an end
   * user's interaction with the service over a certain period. Obfuscate this
   * field for privacy concerns before providing it to the service. Note that
   * any improvements to the model for a particular tenant site rely on this
   * field being set correctly to a unique session ID. The maximum number of
   * allowed characters is 255.
   *
   * @var string
   */
  public $sessionId;
  /**
   * Required if allow_missing_ids is unset or `false`. A unique user
   * identification string, as determined by the client. To have the strongest
   * positive impact on search quality make sure the client-level is unique.
   * Obfuscate this field for privacy concerns before providing it to the
   * service. Note that any improvements to the model for a particular tenant
   * site rely on this field being set correctly to a unique user ID. The
   * maximum number of allowed characters is 255.
   *
   * @var string
   */
  public $userId;

  /**
   * Only set when any of domain, session_id and user_id isn't available for
   * some reason. It is highly recommended not to set this field and provide
   * accurate domain, session_id and user_id for the best service experience.
   *
   * @param bool $allowMissingIds
   */
  public function setAllowMissingIds($allowMissingIds)
  {
    $this->allowMissingIds = $allowMissingIds;
  }
  /**
   * @return bool
   */
  public function getAllowMissingIds()
  {
    return $this->allowMissingIds;
  }
  /**
   * The type of device used by the job seeker at the time of the call to the
   * service.
   *
   * @param DeviceInfo $deviceInfo
   */
  public function setDeviceInfo(DeviceInfo $deviceInfo)
  {
    $this->deviceInfo = $deviceInfo;
  }
  /**
   * @return DeviceInfo
   */
  public function getDeviceInfo()
  {
    return $this->deviceInfo;
  }
  /**
   * Required if allow_missing_ids is unset or `false`. The client-defined scope
   * or source of the service call, which typically is the domain on which the
   * service has been implemented and is currently being run. For example, if
   * the service is being run by client *Foo, Inc.*, on job board www.foo.com
   * and career site www.bar.com, then this field is set to "foo.com" for use on
   * the job board, and "bar.com" for use on the career site. Note that any
   * improvements to the model for a particular tenant site rely on this field
   * being set correctly to a unique domain. The maximum number of allowed
   * characters is 255.
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
   * Required if allow_missing_ids is unset or `false`. A unique session
   * identification string. A session is defined as the duration of an end
   * user's interaction with the service over a certain period. Obfuscate this
   * field for privacy concerns before providing it to the service. Note that
   * any improvements to the model for a particular tenant site rely on this
   * field being set correctly to a unique session ID. The maximum number of
   * allowed characters is 255.
   *
   * @param string $sessionId
   */
  public function setSessionId($sessionId)
  {
    $this->sessionId = $sessionId;
  }
  /**
   * @return string
   */
  public function getSessionId()
  {
    return $this->sessionId;
  }
  /**
   * Required if allow_missing_ids is unset or `false`. A unique user
   * identification string, as determined by the client. To have the strongest
   * positive impact on search quality make sure the client-level is unique.
   * Obfuscate this field for privacy concerns before providing it to the
   * service. Note that any improvements to the model for a particular tenant
   * site rely on this field being set correctly to a unique user ID. The
   * maximum number of allowed characters is 255.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RequestMetadata::class, 'Google_Service_CloudTalentSolution_RequestMetadata');
