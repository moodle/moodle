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

namespace Google\Service\BeyondCorp;

class GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeaders extends \Google\Model
{
  /**
   * The unspecified output type.
   */
  public const OUTPUT_TYPE_OUTPUT_TYPE_UNSPECIFIED = 'OUTPUT_TYPE_UNSPECIFIED';
  /**
   * Protobuf output type.
   */
  public const OUTPUT_TYPE_PROTOBUF = 'PROTOBUF';
  /**
   * JSON output type.
   */
  public const OUTPUT_TYPE_JSON = 'JSON';
  /**
   * Explicitly disable header output.
   */
  public const OUTPUT_TYPE_NONE = 'NONE';
  protected $deviceInfoType = GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeadersDelegatedDeviceInfo::class;
  protected $deviceInfoDataType = '';
  protected $groupInfoType = GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeadersDelegatedGroupInfo::class;
  protected $groupInfoDataType = '';
  /**
   * Optional. Default output type for all enabled headers.
   *
   * @var string
   */
  public $outputType;
  protected $userInfoType = GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeadersDelegatedUserInfo::class;
  protected $userInfoDataType = '';

  /**
   * Optional. The device information configuration.
   *
   * @param GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeadersDelegatedDeviceInfo $deviceInfo
   */
  public function setDeviceInfo(GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeadersDelegatedDeviceInfo $deviceInfo)
  {
    $this->deviceInfo = $deviceInfo;
  }
  /**
   * @return GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeadersDelegatedDeviceInfo
   */
  public function getDeviceInfo()
  {
    return $this->deviceInfo;
  }
  /**
   * Optional. Group details.
   *
   * @param GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeadersDelegatedGroupInfo $groupInfo
   */
  public function setGroupInfo(GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeadersDelegatedGroupInfo $groupInfo)
  {
    $this->groupInfo = $groupInfo;
  }
  /**
   * @return GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeadersDelegatedGroupInfo
   */
  public function getGroupInfo()
  {
    return $this->groupInfo;
  }
  /**
   * Optional. Default output type for all enabled headers.
   *
   * Accepted values: OUTPUT_TYPE_UNSPECIFIED, PROTOBUF, JSON, NONE
   *
   * @param self::OUTPUT_TYPE_* $outputType
   */
  public function setOutputType($outputType)
  {
    $this->outputType = $outputType;
  }
  /**
   * @return self::OUTPUT_TYPE_*
   */
  public function getOutputType()
  {
    return $this->outputType;
  }
  /**
   * Optional. User details.
   *
   * @param GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeadersDelegatedUserInfo $userInfo
   */
  public function setUserInfo(GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeadersDelegatedUserInfo $userInfo)
  {
    $this->userInfo = $userInfo;
  }
  /**
   * @return GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeadersDelegatedUserInfo
   */
  public function getUserInfo()
  {
    return $this->userInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeaders::class, 'Google_Service_BeyondCorp_GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeaders');
