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

namespace Google\Service\MigrationCenterAPI;

class PlatformDetails extends \Google\Model
{
  protected $awsEc2DetailsType = AwsEc2PlatformDetails::class;
  protected $awsEc2DetailsDataType = '';
  protected $azureVmDetailsType = AzureVmPlatformDetails::class;
  protected $azureVmDetailsDataType = '';
  protected $genericDetailsType = GenericPlatformDetails::class;
  protected $genericDetailsDataType = '';
  protected $physicalDetailsType = PhysicalPlatformDetails::class;
  protected $physicalDetailsDataType = '';
  protected $vmwareDetailsType = VmwarePlatformDetails::class;
  protected $vmwareDetailsDataType = '';

  /**
   * AWS EC2 specific details.
   *
   * @param AwsEc2PlatformDetails $awsEc2Details
   */
  public function setAwsEc2Details(AwsEc2PlatformDetails $awsEc2Details)
  {
    $this->awsEc2Details = $awsEc2Details;
  }
  /**
   * @return AwsEc2PlatformDetails
   */
  public function getAwsEc2Details()
  {
    return $this->awsEc2Details;
  }
  /**
   * Azure VM specific details.
   *
   * @param AzureVmPlatformDetails $azureVmDetails
   */
  public function setAzureVmDetails(AzureVmPlatformDetails $azureVmDetails)
  {
    $this->azureVmDetails = $azureVmDetails;
  }
  /**
   * @return AzureVmPlatformDetails
   */
  public function getAzureVmDetails()
  {
    return $this->azureVmDetails;
  }
  /**
   * Generic platform details.
   *
   * @param GenericPlatformDetails $genericDetails
   */
  public function setGenericDetails(GenericPlatformDetails $genericDetails)
  {
    $this->genericDetails = $genericDetails;
  }
  /**
   * @return GenericPlatformDetails
   */
  public function getGenericDetails()
  {
    return $this->genericDetails;
  }
  /**
   * Physical machines platform details.
   *
   * @param PhysicalPlatformDetails $physicalDetails
   */
  public function setPhysicalDetails(PhysicalPlatformDetails $physicalDetails)
  {
    $this->physicalDetails = $physicalDetails;
  }
  /**
   * @return PhysicalPlatformDetails
   */
  public function getPhysicalDetails()
  {
    return $this->physicalDetails;
  }
  /**
   * VMware specific details.
   *
   * @param VmwarePlatformDetails $vmwareDetails
   */
  public function setVmwareDetails(VmwarePlatformDetails $vmwareDetails)
  {
    $this->vmwareDetails = $vmwareDetails;
  }
  /**
   * @return VmwarePlatformDetails
   */
  public function getVmwareDetails()
  {
    return $this->vmwareDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlatformDetails::class, 'Google_Service_MigrationCenterAPI_PlatformDetails');
