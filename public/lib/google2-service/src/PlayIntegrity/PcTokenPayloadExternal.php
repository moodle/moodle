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

namespace Google\Service\PlayIntegrity;

class PcTokenPayloadExternal extends \Google\Model
{
  protected $accountDetailsType = PcAccountDetails::class;
  protected $accountDetailsDataType = '';
  protected $deviceIntegrityType = PcDeviceIntegrity::class;
  protected $deviceIntegrityDataType = '';
  protected $requestDetailsType = PcRequestDetails::class;
  protected $requestDetailsDataType = '';
  protected $testingDetailsType = PcTestingDetails::class;
  protected $testingDetailsDataType = '';

  /**
   * Details about the account information such as the licensing status.
   *
   * @param PcAccountDetails $accountDetails
   */
  public function setAccountDetails(PcAccountDetails $accountDetails)
  {
    $this->accountDetails = $accountDetails;
  }
  /**
   * @return PcAccountDetails
   */
  public function getAccountDetails()
  {
    return $this->accountDetails;
  }
  /**
   * Required. Details about the device integrity.
   *
   * @param PcDeviceIntegrity $deviceIntegrity
   */
  public function setDeviceIntegrity(PcDeviceIntegrity $deviceIntegrity)
  {
    $this->deviceIntegrity = $deviceIntegrity;
  }
  /**
   * @return PcDeviceIntegrity
   */
  public function getDeviceIntegrity()
  {
    return $this->deviceIntegrity;
  }
  /**
   * Required. Details about the integrity request.
   *
   * @param PcRequestDetails $requestDetails
   */
  public function setRequestDetails(PcRequestDetails $requestDetails)
  {
    $this->requestDetails = $requestDetails;
  }
  /**
   * @return PcRequestDetails
   */
  public function getRequestDetails()
  {
    return $this->requestDetails;
  }
  /**
   * Indicates that this payload is generated for testing purposes and contains
   * any additional data that is linked with testing status.
   *
   * @param PcTestingDetails $testingDetails
   */
  public function setTestingDetails(PcTestingDetails $testingDetails)
  {
    $this->testingDetails = $testingDetails;
  }
  /**
   * @return PcTestingDetails
   */
  public function getTestingDetails()
  {
    return $this->testingDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PcTokenPayloadExternal::class, 'Google_Service_PlayIntegrity_PcTokenPayloadExternal');
