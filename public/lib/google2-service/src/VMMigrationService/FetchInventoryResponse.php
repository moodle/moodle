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

namespace Google\Service\VMMigrationService;

class FetchInventoryResponse extends \Google\Model
{
  protected $awsVmsType = AwsVmsDetails::class;
  protected $awsVmsDataType = '';
  protected $azureVmsType = AzureVmsDetails::class;
  protected $azureVmsDataType = '';
  /**
   * Output only. A token, which can be sent as `page_token` to retrieve the
   * next page. If this field is omitted, there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Output only. The timestamp when the source was last queried (if the result
   * is from the cache).
   *
   * @var string
   */
  public $updateTime;
  protected $vmwareVmsType = VmwareVmsDetails::class;
  protected $vmwareVmsDataType = '';

  /**
   * The description of the VMs in a Source of type AWS.
   *
   * @param AwsVmsDetails $awsVms
   */
  public function setAwsVms(AwsVmsDetails $awsVms)
  {
    $this->awsVms = $awsVms;
  }
  /**
   * @return AwsVmsDetails
   */
  public function getAwsVms()
  {
    return $this->awsVms;
  }
  /**
   * The description of the VMs in a Source of type Azure.
   *
   * @param AzureVmsDetails $azureVms
   */
  public function setAzureVms(AzureVmsDetails $azureVms)
  {
    $this->azureVms = $azureVms;
  }
  /**
   * @return AzureVmsDetails
   */
  public function getAzureVms()
  {
    return $this->azureVms;
  }
  /**
   * Output only. A token, which can be sent as `page_token` to retrieve the
   * next page. If this field is omitted, there are no subsequent pages.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * Output only. The timestamp when the source was last queried (if the result
   * is from the cache).
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
  /**
   * The description of the VMs in a Source of type Vmware.
   *
   * @param VmwareVmsDetails $vmwareVms
   */
  public function setVmwareVms(VmwareVmsDetails $vmwareVms)
  {
    $this->vmwareVms = $vmwareVms;
  }
  /**
   * @return VmwareVmsDetails
   */
  public function getVmwareVms()
  {
    return $this->vmwareVms;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FetchInventoryResponse::class, 'Google_Service_VMMigrationService_FetchInventoryResponse');
