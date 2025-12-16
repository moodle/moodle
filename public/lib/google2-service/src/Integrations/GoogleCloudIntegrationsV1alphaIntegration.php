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

class GoogleCloudIntegrationsV1alphaIntegration extends \Google\Model
{
  /**
   * Required. If any integration version is published.
   *
   * @var bool
   */
  public $active;
  /**
   * Required. Output only. Auto-generated.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The creator's email address. Generated based on the End User
   * Credentials/LOAS role of the user making the call.
   *
   * @var string
   */
  public $creatorEmail;
  /**
   * Optional.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The last modifier of this integration
   *
   * @var string
   */
  public $lastModifierEmail;
  /**
   * Required. The resource name of the integration.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Auto-generated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. If any integration version is published.
   *
   * @param bool $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }
  /**
   * @return bool
   */
  public function getActive()
  {
    return $this->active;
  }
  /**
   * Required. Output only. Auto-generated.
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
   * Output only. The creator's email address. Generated based on the End User
   * Credentials/LOAS role of the user making the call.
   *
   * @param string $creatorEmail
   */
  public function setCreatorEmail($creatorEmail)
  {
    $this->creatorEmail = $creatorEmail;
  }
  /**
   * @return string
   */
  public function getCreatorEmail()
  {
    return $this->creatorEmail;
  }
  /**
   * Optional.
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
   * Required. The last modifier of this integration
   *
   * @param string $lastModifierEmail
   */
  public function setLastModifierEmail($lastModifierEmail)
  {
    $this->lastModifierEmail = $lastModifierEmail;
  }
  /**
   * @return string
   */
  public function getLastModifierEmail()
  {
    return $this->lastModifierEmail;
  }
  /**
   * Required. The resource name of the integration.
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
   * Output only. Auto-generated.
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
class_alias(GoogleCloudIntegrationsV1alphaIntegration::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaIntegration');
