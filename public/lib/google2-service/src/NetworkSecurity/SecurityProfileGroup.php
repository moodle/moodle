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

class SecurityProfileGroup extends \Google\Model
{
  /**
   * Output only. Resource creation timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Reference to a SecurityProfile with the CustomIntercept
   * configuration.
   *
   * @var string
   */
  public $customInterceptProfile;
  /**
   * Optional. Reference to a SecurityProfile with the CustomMirroring
   * configuration.
   *
   * @var string
   */
  public $customMirroringProfile;
  /**
   * Output only. Identifier used by the data-path. Unique within {container,
   * location}.
   *
   * @var string
   */
  public $dataPathId;
  /**
   * Optional. An optional description of the profile group. Max length 2048
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
   * Immutable. Identifier. Name of the SecurityProfileGroup resource. It
   * matches pattern `projects|organizations/locations/{location}/securityProfil
   * eGroups/{security_profile_group}`.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Reference to a SecurityProfile with the ThreatPrevention
   * configuration.
   *
   * @var string
   */
  public $threatPreventionProfile;
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
   * Optional. Reference to a SecurityProfile with the CustomIntercept
   * configuration.
   *
   * @param string $customInterceptProfile
   */
  public function setCustomInterceptProfile($customInterceptProfile)
  {
    $this->customInterceptProfile = $customInterceptProfile;
  }
  /**
   * @return string
   */
  public function getCustomInterceptProfile()
  {
    return $this->customInterceptProfile;
  }
  /**
   * Optional. Reference to a SecurityProfile with the CustomMirroring
   * configuration.
   *
   * @param string $customMirroringProfile
   */
  public function setCustomMirroringProfile($customMirroringProfile)
  {
    $this->customMirroringProfile = $customMirroringProfile;
  }
  /**
   * @return string
   */
  public function getCustomMirroringProfile()
  {
    return $this->customMirroringProfile;
  }
  /**
   * Output only. Identifier used by the data-path. Unique within {container,
   * location}.
   *
   * @param string $dataPathId
   */
  public function setDataPathId($dataPathId)
  {
    $this->dataPathId = $dataPathId;
  }
  /**
   * @return string
   */
  public function getDataPathId()
  {
    return $this->dataPathId;
  }
  /**
   * Optional. An optional description of the profile group. Max length 2048
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
   * Immutable. Identifier. Name of the SecurityProfileGroup resource. It
   * matches pattern `projects|organizations/locations/{location}/securityProfil
   * eGroups/{security_profile_group}`.
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
   * Optional. Reference to a SecurityProfile with the ThreatPrevention
   * configuration.
   *
   * @param string $threatPreventionProfile
   */
  public function setThreatPreventionProfile($threatPreventionProfile)
  {
    $this->threatPreventionProfile = $threatPreventionProfile;
  }
  /**
   * @return string
   */
  public function getThreatPreventionProfile()
  {
    return $this->threatPreventionProfile;
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
class_alias(SecurityProfileGroup::class, 'Google_Service_NetworkSecurity_SecurityProfileGroup');
