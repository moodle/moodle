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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2IssueFinding extends \Google\Model
{
  protected $cveType = GoogleCloudSecuritycenterV2IssueFindingCve::class;
  protected $cveDataType = '';
  /**
   * The name of the finding.
   *
   * @var string
   */
  public $name;
  protected $securityBulletinType = GoogleCloudSecuritycenterV2IssueFindingSecurityBulletin::class;
  protected $securityBulletinDataType = '';

  /**
   * The CVE of the finding.
   *
   * @param GoogleCloudSecuritycenterV2IssueFindingCve $cve
   */
  public function setCve(GoogleCloudSecuritycenterV2IssueFindingCve $cve)
  {
    $this->cve = $cve;
  }
  /**
   * @return GoogleCloudSecuritycenterV2IssueFindingCve
   */
  public function getCve()
  {
    return $this->cve;
  }
  /**
   * The name of the finding.
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
   * The security bulletin of the finding.
   *
   * @param GoogleCloudSecuritycenterV2IssueFindingSecurityBulletin $securityBulletin
   */
  public function setSecurityBulletin(GoogleCloudSecuritycenterV2IssueFindingSecurityBulletin $securityBulletin)
  {
    $this->securityBulletin = $securityBulletin;
  }
  /**
   * @return GoogleCloudSecuritycenterV2IssueFindingSecurityBulletin
   */
  public function getSecurityBulletin()
  {
    return $this->securityBulletin;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2IssueFinding::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2IssueFinding');
