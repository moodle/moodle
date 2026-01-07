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

namespace Google\Service\AndroidProvisioningPartner;

class FindDevicesByDeviceIdentifierRequest extends \Google\Model
{
  protected $deviceIdentifierType = DeviceIdentifier::class;
  protected $deviceIdentifierDataType = '';
  /**
   * Required. The maximum number of devices to show in a page of results. Must
   * be between 1 and 100 inclusive.
   *
   * @var string
   */
  public $limit;
  /**
   * A token specifying which result page to return.
   *
   * @var string
   */
  public $pageToken;

  /**
   * Required. Required. The device identifier to search for. If serial number
   * is provided then case insensitive serial number matches are allowed.
   *
   * @param DeviceIdentifier $deviceIdentifier
   */
  public function setDeviceIdentifier(DeviceIdentifier $deviceIdentifier)
  {
    $this->deviceIdentifier = $deviceIdentifier;
  }
  /**
   * @return DeviceIdentifier
   */
  public function getDeviceIdentifier()
  {
    return $this->deviceIdentifier;
  }
  /**
   * Required. The maximum number of devices to show in a page of results. Must
   * be between 1 and 100 inclusive.
   *
   * @param string $limit
   */
  public function setLimit($limit)
  {
    $this->limit = $limit;
  }
  /**
   * @return string
   */
  public function getLimit()
  {
    return $this->limit;
  }
  /**
   * A token specifying which result page to return.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FindDevicesByDeviceIdentifierRequest::class, 'Google_Service_AndroidProvisioningPartner_FindDevicesByDeviceIdentifierRequest');
