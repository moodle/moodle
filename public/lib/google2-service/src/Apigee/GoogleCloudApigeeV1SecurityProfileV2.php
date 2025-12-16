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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1SecurityProfileV2 extends \Google\Model
{
  /**
   * Risk assessment type is not specified.
   */
  public const RISK_ASSESSMENT_TYPE_RISK_ASSESSMENT_TYPE_UNSPECIFIED = 'RISK_ASSESSMENT_TYPE_UNSPECIFIED';
  /**
   * Risk assessment type is Apigee.
   */
  public const RISK_ASSESSMENT_TYPE_APIGEE = 'APIGEE';
  /**
   * Risk assessment type is API Hub.
   */
  public const RISK_ASSESSMENT_TYPE_API_HUB = 'API_HUB';
  /**
   * Output only. The time of the security profile creation.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The description of the security profile.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Whether the security profile is google defined.
   *
   * @var bool
   */
  public $googleDefined;
  /**
   * Identifier. Name of the security profile v2 resource. Format:
   * organizations/{org}/securityProfilesV2/{profile}
   *
   * @var string
   */
  public $name;
  protected $profileAssessmentConfigsType = GoogleCloudApigeeV1SecurityProfileV2ProfileAssessmentConfig::class;
  protected $profileAssessmentConfigsDataType = 'map';
  /**
   * Optional. The risk assessment type of the security profile. Defaults to
   * ADVANCED_API_SECURITY.
   *
   * @var string
   */
  public $riskAssessmentType;
  /**
   * Output only. The time of the security profile update.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time of the security profile creation.
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
   * Optional. The description of the security profile.
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
   * Output only. Whether the security profile is google defined.
   *
   * @param bool $googleDefined
   */
  public function setGoogleDefined($googleDefined)
  {
    $this->googleDefined = $googleDefined;
  }
  /**
   * @return bool
   */
  public function getGoogleDefined()
  {
    return $this->googleDefined;
  }
  /**
   * Identifier. Name of the security profile v2 resource. Format:
   * organizations/{org}/securityProfilesV2/{profile}
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
   * Required. The configuration for each assessment in this profile. Key is the
   * name/id of the assessment.
   *
   * @param GoogleCloudApigeeV1SecurityProfileV2ProfileAssessmentConfig[] $profileAssessmentConfigs
   */
  public function setProfileAssessmentConfigs($profileAssessmentConfigs)
  {
    $this->profileAssessmentConfigs = $profileAssessmentConfigs;
  }
  /**
   * @return GoogleCloudApigeeV1SecurityProfileV2ProfileAssessmentConfig[]
   */
  public function getProfileAssessmentConfigs()
  {
    return $this->profileAssessmentConfigs;
  }
  /**
   * Optional. The risk assessment type of the security profile. Defaults to
   * ADVANCED_API_SECURITY.
   *
   * Accepted values: RISK_ASSESSMENT_TYPE_UNSPECIFIED, APIGEE, API_HUB
   *
   * @param self::RISK_ASSESSMENT_TYPE_* $riskAssessmentType
   */
  public function setRiskAssessmentType($riskAssessmentType)
  {
    $this->riskAssessmentType = $riskAssessmentType;
  }
  /**
   * @return self::RISK_ASSESSMENT_TYPE_*
   */
  public function getRiskAssessmentType()
  {
    return $this->riskAssessmentType;
  }
  /**
   * Output only. The time of the security profile update.
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
class_alias(GoogleCloudApigeeV1SecurityProfileV2::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityProfileV2');
