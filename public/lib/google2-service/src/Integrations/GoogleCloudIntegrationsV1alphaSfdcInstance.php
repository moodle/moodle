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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaSfdcInstance extends \Google\Collection
{
  protected $collection_key = 'authConfigId';
  /**
   * A list of AuthConfigs that can be tried to open the channel to SFDC
   *
   * @var string[]
   */
  public $authConfigId;
  /**
   * Output only. Time when the instance is created
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Time when the instance was deleted. Empty if not deleted.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Optional. A description of the sfdc instance.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. User selected unique name/alias to easily reference an instance.
   *
   * @var string
   */
  public $displayName;
  /**
   * Resource name of the SFDC instance
   * projects/{project}/locations/{location}/sfdcInstances/{sfdcInstance}.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. URL used for API calls after authentication (the login authority
   * is configured within the referenced AuthConfig).
   *
   * @var string
   */
  public $serviceAuthority;
  /**
   * The SFDC Org Id. This is defined in salesforce.
   *
   * @var string
   */
  public $sfdcOrgId;
  /**
   * Output only. Time when the instance was last updated
   *
   * @var string
   */
  public $updateTime;

  /**
   * A list of AuthConfigs that can be tried to open the channel to SFDC
   *
   * @param string[] $authConfigId
   */
  public function setAuthConfigId($authConfigId)
  {
    $this->authConfigId = $authConfigId;
  }
  /**
   * @return string[]
   */
  public function getAuthConfigId()
  {
    return $this->authConfigId;
  }
  /**
   * Output only. Time when the instance is created
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
   * Output only. Time when the instance was deleted. Empty if not deleted.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * Optional. A description of the sfdc instance.
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
   * Optional. User selected unique name/alias to easily reference an instance.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Resource name of the SFDC instance
   * projects/{project}/locations/{location}/sfdcInstances/{sfdcInstance}.
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
   * Optional. URL used for API calls after authentication (the login authority
   * is configured within the referenced AuthConfig).
   *
   * @param string $serviceAuthority
   */
  public function setServiceAuthority($serviceAuthority)
  {
    $this->serviceAuthority = $serviceAuthority;
  }
  /**
   * @return string
   */
  public function getServiceAuthority()
  {
    return $this->serviceAuthority;
  }
  /**
   * The SFDC Org Id. This is defined in salesforce.
   *
   * @param string $sfdcOrgId
   */
  public function setSfdcOrgId($sfdcOrgId)
  {
    $this->sfdcOrgId = $sfdcOrgId;
  }
  /**
   * @return string
   */
  public function getSfdcOrgId()
  {
    return $this->sfdcOrgId;
  }
  /**
   * Output only. Time when the instance was last updated
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
class_alias(GoogleCloudIntegrationsV1alphaSfdcInstance::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaSfdcInstance');
