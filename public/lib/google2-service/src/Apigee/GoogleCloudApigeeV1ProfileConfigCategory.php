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

class GoogleCloudApigeeV1ProfileConfigCategory extends \Google\Model
{
  protected $abuseType = GoogleCloudApigeeV1ProfileConfigAbuse::class;
  protected $abuseDataType = '';
  protected $authorizationType = GoogleCloudApigeeV1ProfileConfigAuthorization::class;
  protected $authorizationDataType = '';
  protected $corsType = GoogleCloudApigeeV1ProfileConfigCORS::class;
  protected $corsDataType = '';
  protected $mediationType = GoogleCloudApigeeV1ProfileConfigMediation::class;
  protected $mediationDataType = '';
  protected $mtlsType = GoogleCloudApigeeV1ProfileConfigMTLS::class;
  protected $mtlsDataType = '';
  protected $threatType = GoogleCloudApigeeV1ProfileConfigThreat::class;
  protected $threatDataType = '';

  /**
   * Checks for abuse, which includes any requests sent to the API for purposes
   * other than what it is intended for, such as high volumes of requests, data
   * scraping, and abuse related to authorization.
   *
   * @param GoogleCloudApigeeV1ProfileConfigAbuse $abuse
   */
  public function setAbuse(GoogleCloudApigeeV1ProfileConfigAbuse $abuse)
  {
    $this->abuse = $abuse;
  }
  /**
   * @return GoogleCloudApigeeV1ProfileConfigAbuse
   */
  public function getAbuse()
  {
    return $this->abuse;
  }
  /**
   * Checks to see if you have an authorization policy in place.
   *
   * @param GoogleCloudApigeeV1ProfileConfigAuthorization $authorization
   */
  public function setAuthorization(GoogleCloudApigeeV1ProfileConfigAuthorization $authorization)
  {
    $this->authorization = $authorization;
  }
  /**
   * @return GoogleCloudApigeeV1ProfileConfigAuthorization
   */
  public function getAuthorization()
  {
    return $this->authorization;
  }
  /**
   * Checks to see if you have CORS policy in place.
   *
   * @param GoogleCloudApigeeV1ProfileConfigCORS $cors
   */
  public function setCors(GoogleCloudApigeeV1ProfileConfigCORS $cors)
  {
    $this->cors = $cors;
  }
  /**
   * @return GoogleCloudApigeeV1ProfileConfigCORS
   */
  public function getCors()
  {
    return $this->cors;
  }
  /**
   * Checks to see if you have a mediation policy in place.
   *
   * @param GoogleCloudApigeeV1ProfileConfigMediation $mediation
   */
  public function setMediation(GoogleCloudApigeeV1ProfileConfigMediation $mediation)
  {
    $this->mediation = $mediation;
  }
  /**
   * @return GoogleCloudApigeeV1ProfileConfigMediation
   */
  public function getMediation()
  {
    return $this->mediation;
  }
  /**
   * Checks to see if you have configured mTLS for the target server.
   *
   * @param GoogleCloudApigeeV1ProfileConfigMTLS $mtls
   */
  public function setMtls(GoogleCloudApigeeV1ProfileConfigMTLS $mtls)
  {
    $this->mtls = $mtls;
  }
  /**
   * @return GoogleCloudApigeeV1ProfileConfigMTLS
   */
  public function getMtls()
  {
    return $this->mtls;
  }
  /**
   * Checks to see if you have a threat protection policy in place.
   *
   * @param GoogleCloudApigeeV1ProfileConfigThreat $threat
   */
  public function setThreat(GoogleCloudApigeeV1ProfileConfigThreat $threat)
  {
    $this->threat = $threat;
  }
  /**
   * @return GoogleCloudApigeeV1ProfileConfigThreat
   */
  public function getThreat()
  {
    return $this->threat;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ProfileConfigCategory::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ProfileConfigCategory');
