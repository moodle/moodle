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

namespace Google\Service\NetworkSecurity;

class SecurityProfile extends \Google\Model
{
  /**
   * Profile type not specified.
   */
  public const TYPE_PROFILE_TYPE_UNSPECIFIED = 'PROFILE_TYPE_UNSPECIFIED';
  /**
   * Profile type for threat prevention.
   */
  public const TYPE_THREAT_PREVENTION = 'THREAT_PREVENTION';
  /**
   * Profile type for packet mirroring v2
   */
  public const TYPE_CUSTOM_MIRRORING = 'CUSTOM_MIRRORING';
  /**
   * Profile type for TPPI.
   */
  public const TYPE_CUSTOM_INTERCEPT = 'CUSTOM_INTERCEPT';
  /**
   * Output only. Resource creation timestamp.
   *
   * @var string
   */
  public $createTime;
  protected $customInterceptProfileType = CustomInterceptProfile::class;
  protected $customInterceptProfileDataType = '';
  protected $customMirroringProfileType = CustomMirroringProfile::class;
  protected $customMirroringProfileDataType = '';
  /**
   * Optional. An optional description of the profile. Max length 512
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Labels as key value pairs.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Immutable. Identifier. Name of the SecurityProfile resource. It matches
   * pattern `projects|organizations/locations/{location}/securityProfiles/{secu
   * rity_profile}`.
   *
   * @var string
   */
  public $name;
  protected $threatPreventionProfileType = ThreatPreventionProfile::class;
  protected $threatPreventionProfileDataType = '';
  /**
   * Immutable. The single ProfileType that the SecurityProfile resource
   * configures.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. Last resource update timestamp.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Resource creation timestamp.
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
   * The custom TPPI configuration for the SecurityProfile.
   *
   * @param CustomInterceptProfile $customInterceptProfile
   */
  public function setCustomInterceptProfile(CustomInterceptProfile $customInterceptProfile)
  {
    $this->customInterceptProfile = $customInterceptProfile;
  }
  /**
   * @return CustomInterceptProfile
   */
  public function getCustomInterceptProfile()
  {
    return $this->customInterceptProfile;
  }
  /**
   * The custom Packet Mirroring v2 configuration for the SecurityProfile.
   *
   * @param CustomMirroringProfile $customMirroringProfile
   */
  public function setCustomMirroringProfile(CustomMirroringProfile $customMirroringProfile)
  {
    $this->customMirroringProfile = $customMirroringProfile;
  }
  /**
   * @return CustomMirroringProfile
   */
  public function getCustomMirroringProfile()
  {
    return $this->customMirroringProfile;
  }
  /**
   * Optional. An optional description of the profile. Max length 512
   * characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. Labels as key value pairs.
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
   * Immutable. Identifier. Name of the SecurityProfile resource. It matches
   * pattern `projects|organizations/locations/{location}/securityProfiles/{secu
   * rity_profile}`.
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
   * The threat prevention configuration for the SecurityProfile.
   *
   * @param ThreatPreventionProfile $threatPreventionProfile
   */
  public function setThreatPreventionProfile(ThreatPreventionProfile $threatPreventionProfile)
  {
    $this->threatPreventionProfile = $threatPreventionProfile;
  }
  /**
   * @return ThreatPreventionProfile
   */
  public function getThreatPreventionProfile()
  {
    return $this->threatPreventionProfile;
  }
  /**
   * Immutable. The single ProfileType that the SecurityProfile resource
   * configures.
   *
   * Accepted values: PROFILE_TYPE_UNSPECIFIED, THREAT_PREVENTION,
   * CUSTOM_MIRRORING, CUSTOM_INTERCEPT
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. Last resource update timestamp.
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
class_alias(SecurityProfile::class, 'Google_Service_NetworkSecurity_SecurityProfile');
