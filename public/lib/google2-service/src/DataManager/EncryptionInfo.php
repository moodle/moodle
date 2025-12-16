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

namespace Google\Service\DataManager;

class EncryptionInfo extends \Google\Model
{
  protected $awsWrappedKeyInfoType = AwsWrappedKeyInfo::class;
  protected $awsWrappedKeyInfoDataType = '';
  protected $gcpWrappedKeyInfoType = GcpWrappedKeyInfo::class;
  protected $gcpWrappedKeyInfoDataType = '';

  /**
   * Amazon Web Services wrapped key information.
   *
   * @param AwsWrappedKeyInfo $awsWrappedKeyInfo
   */
  public function setAwsWrappedKeyInfo(AwsWrappedKeyInfo $awsWrappedKeyInfo)
  {
    $this->awsWrappedKeyInfo = $awsWrappedKeyInfo;
  }
  /**
   * @return AwsWrappedKeyInfo
   */
  public function getAwsWrappedKeyInfo()
  {
    return $this->awsWrappedKeyInfo;
  }
  /**
   * Google Cloud Platform wrapped key information.
   *
   * @param GcpWrappedKeyInfo $gcpWrappedKeyInfo
   */
  public function setGcpWrappedKeyInfo(GcpWrappedKeyInfo $gcpWrappedKeyInfo)
  {
    $this->gcpWrappedKeyInfo = $gcpWrappedKeyInfo;
  }
  /**
   * @return GcpWrappedKeyInfo
   */
  public function getGcpWrappedKeyInfo()
  {
    return $this->gcpWrappedKeyInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EncryptionInfo::class, 'Google_Service_DataManager_EncryptionInfo');
