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

class GoogleCloudApigeeV1SecurityProfileV2ProfileAssessmentConfig extends \Google\Model
{
  /**
   * The weight is unspecified.
   */
  public const WEIGHT_WEIGHT_UNSPECIFIED = 'WEIGHT_UNSPECIFIED';
  /**
   * The weight is minor.
   */
  public const WEIGHT_MINOR = 'MINOR';
  /**
   * The weight is moderate.
   */
  public const WEIGHT_MODERATE = 'MODERATE';
  /**
   * The weight is major.
   */
  public const WEIGHT_MAJOR = 'MAJOR';
  protected $includeType = GoogleCloudApigeeV1SecurityProfileV2ProfileAssessmentConfigApiHubGatewayTypeArray::class;
  protected $includeDataType = '';
  /**
   * The weight of the assessment.
   *
   * @var string
   */
  public $weight;

  /**
   * Include only these Gateway Types.
   *
   * @param GoogleCloudApigeeV1SecurityProfileV2ProfileAssessmentConfigApiHubGatewayTypeArray $include
   */
  public function setInclude(GoogleCloudApigeeV1SecurityProfileV2ProfileAssessmentConfigApiHubGatewayTypeArray $include)
  {
    $this->include = $include;
  }
  /**
   * @return GoogleCloudApigeeV1SecurityProfileV2ProfileAssessmentConfigApiHubGatewayTypeArray
   */
  public function getInclude()
  {
    return $this->include;
  }
  /**
   * The weight of the assessment.
   *
   * Accepted values: WEIGHT_UNSPECIFIED, MINOR, MODERATE, MAJOR
   *
   * @param self::WEIGHT_* $weight
   */
  public function setWeight($weight)
  {
    $this->weight = $weight;
  }
  /**
   * @return self::WEIGHT_*
   */
  public function getWeight()
  {
    return $this->weight;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SecurityProfileV2ProfileAssessmentConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityProfileV2ProfileAssessmentConfig');
