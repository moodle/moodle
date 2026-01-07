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

class Parameter extends \Google\Model
{
  /**
   * The default / unset value. The API will default to the UNFORMATTED format.
   */
  public const FORMAT_PARAMETER_FORMAT_UNSPECIFIED = 'PARAMETER_FORMAT_UNSPECIFIED';
  /**
   * Unformatted.
   */
  public const FORMAT_UNFORMATTED = 'UNFORMATTED';
  /**
   * YAML format.
   */
  public const FORMAT_YAML = 'YAML';
  /**
   * JSON format.
   */
  public const FORMAT_JSON = 'JSON';
  /**
   * Output only. [Output only] Create time stamp
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Specifies the format of a Parameter.
   *
   * @var string
   */
  public $format;
  /**
   * Optional. Customer managed encryption key (CMEK) to use for encrypting the
   * Parameter Versions. If not set, the default Google-managed encryption key
   * will be used. Cloud KMS CryptoKeys must reside in the same location as the
   * Parameter. The expected format is `projects/locations/keyRings/cryptoKeys`.
   *
   * @var string
   */
  public $kmsKey;
  /**
   * Optional. Labels as key value pairs
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. [Output only] The resource name of the Parameter in the format
   * `projects/locations/parameters`.
   *
   * @var string
   */
  public $name;
  protected $policyMemberType = ResourcePolicyMember::class;
  protected $policyMemberDataType = '';
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
   * Optional. Specifies the format of a Parameter.
   *
   * Accepted values: PARAMETER_FORMAT_UNSPECIFIED, UNFORMATTED, YAML, JSON
   *
   * @param self::FORMAT_* $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @return self::FORMAT_*
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * Optional. Customer managed encryption key (CMEK) to use for encrypting the
   * Parameter Versions. If not set, the default Google-managed encryption key
   * will be used. Cloud KMS CryptoKeys must reside in the same location as the
   * Parameter. The expected format is `projects/locations/keyRings/cryptoKeys`.
   *
   * @param string $kmsKey
   */
  public function setKmsKey($kmsKey)
  {
    $this->kmsKey = $kmsKey;
  }
  /**
   * @return string
   */
  public function getKmsKey()
  {
    return $this->kmsKey;
  }
  /**
   * Optional. Labels as key value pairs
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Identifier. [Output only] The resource name of the Parameter in the format
   * `projects/locations/parameters`.
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
   * Output only. [Output-only] policy member strings of a Google Cloud
   * resource.
   *
   * @param ResourcePolicyMember $policyMember
   */
  public function setPolicyMember(ResourcePolicyMember $policyMember)
  {
    $this->policyMember = $policyMember;
  }
  /**
   * @return ResourcePolicyMember
   */
  public function getPolicyMember()
  {
    return $this->policyMember;
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
class_alias(Parameter::class, 'Google_Service_ParameterManager_Parameter');
