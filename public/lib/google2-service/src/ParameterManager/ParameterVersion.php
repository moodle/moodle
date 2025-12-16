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

namespace Google\Service\ParameterManager;

class ParameterVersion extends \Google\Model
{
  /**
   * Output only. [Output only] Create time stamp
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Disabled boolean to determine if a ParameterVersion acts as a
   * metadata only resource (payload is never returned if disabled is true). If
   * true any calls will always default to BASIC view even if the user
   * explicitly passes FULL view as part of the request. A render call on a
   * disabled resource fails with an error. Default value is False.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Optional. Output only. [Output only] The resource name of the KMS key
   * version used to encrypt the ParameterVersion payload. This field is
   * populated only if the Parameter resource has customer managed encryption
   * key (CMEK) configured.
   *
   * @var string
   */
  public $kmsKeyVersion;
  /**
   * Identifier. [Output only] The resource name of the ParameterVersion in the
   * format `projects/locations/parameters/versions`.
   *
   * @var string
   */
  public $name;
  protected $payloadType = ParameterVersionPayload::class;
  protected $payloadDataType = '';
  /**
   * Output only. [Output only] Update time stamp
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. [Output only] Create time stamp
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. Disabled boolean to determine if a ParameterVersion acts as a
   * metadata only resource (payload is never returned if disabled is true). If
   * true any calls will always default to BASIC view even if the user
   * explicitly passes FULL view as part of the request. A render call on a
   * disabled resource fails with an error. Default value is False.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Optional. Output only. [Output only] The resource name of the KMS key
   * version used to encrypt the ParameterVersion payload. This field is
   * populated only if the Parameter resource has customer managed encryption
   * key (CMEK) configured.
   *
   * @param string $kmsKeyVersion
   */
  public function setKmsKeyVersion($kmsKeyVersion)
  {
    $this->kmsKeyVersion = $kmsKeyVersion;
  }
  /**
   * @return string
   */
  public function getKmsKeyVersion()
  {
    return $this->kmsKeyVersion;
  }
  /**
   * Identifier. [Output only] The resource name of the ParameterVersion in the
   * format `projects/locations/parameters/versions`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. Immutable. Payload content of a ParameterVersion resource. This
   * is only returned when the request provides the View value of FULL (default
   * for GET request).
   *
   * @param ParameterVersionPayload $payload
   */
  public function setPayload(ParameterVersionPayload $payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return ParameterVersionPayload
   */
  public function getPayload()
  {
    return $this->payload;
  }
  /**
   * Output only. [Output only] Update time stamp
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
class_alias(ParameterVersion::class, 'Google_Service_ParameterManager_ParameterVersion');
