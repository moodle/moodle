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

namespace Google\Service\SecureSourceManager;

class PrivateConfig extends \Google\Collection
{
  protected $collection_key = 'pscAllowedProjects';
  /**
   * Optional. Immutable. CA pool resource, resource must in the format of
   * `projects/{project}/locations/{location}/caPools/{ca_pool}`.
   *
   * @var string
   */
  public $caPool;
  /**
   * Output only. Service Attachment for HTTP, resource is in the format of `pro
   * jects/{project}/regions/{region}/serviceAttachments/{service_attachment}`.
   *
   * @var string
   */
  public $httpServiceAttachment;
  /**
   * Required. Immutable. Indicate if it's private instance.
   *
   * @var bool
   */
  public $isPrivate;
  /**
   * Optional. Additional allowed projects for setting up PSC connections.
   * Instance host project is automatically allowed and does not need to be
   * included in this list.
   *
   * @var string[]
   */
  public $pscAllowedProjects;
  /**
   * Output only. Service Attachment for SSH, resource is in the format of `proj
   * ects/{project}/regions/{region}/serviceAttachments/{service_attachment}`.
   *
   * @var string
   */
  public $sshServiceAttachment;

  /**
   * Optional. Immutable. CA pool resource, resource must in the format of
   * `projects/{project}/locations/{location}/caPools/{ca_pool}`.
   *
   * @param string $caPool
   */
  public function setCaPool($caPool)
  {
    $this->caPool = $caPool;
  }
  /**
   * @return string
   */
  public function getCaPool()
  {
    return $this->caPool;
  }
  /**
   * Output only. Service Attachment for HTTP, resource is in the format of `pro
   * jects/{project}/regions/{region}/serviceAttachments/{service_attachment}`.
   *
   * @param string $httpServiceAttachment
   */
  public function setHttpServiceAttachment($httpServiceAttachment)
  {
    $this->httpServiceAttachment = $httpServiceAttachment;
  }
  /**
   * @return string
   */
  public function getHttpServiceAttachment()
  {
    return $this->httpServiceAttachment;
  }
  /**
   * Required. Immutable. Indicate if it's private instance.
   *
   * @param bool $isPrivate
   */
  public function setIsPrivate($isPrivate)
  {
    $this->isPrivate = $isPrivate;
  }
  /**
   * @return bool
   */
  public function getIsPrivate()
  {
    return $this->isPrivate;
  }
  /**
   * Optional. Additional allowed projects for setting up PSC connections.
   * Instance host project is automatically allowed and does not need to be
   * included in this list.
   *
   * @param string[] $pscAllowedProjects
   */
  public function setPscAllowedProjects($pscAllowedProjects)
  {
    $this->pscAllowedProjects = $pscAllowedProjects;
  }
  /**
   * @return string[]
   */
  public function getPscAllowedProjects()
  {
    return $this->pscAllowedProjects;
  }
  /**
   * Output only. Service Attachment for SSH, resource is in the format of `proj
   * ects/{project}/regions/{region}/serviceAttachments/{service_attachment}`.
   *
   * @param string $sshServiceAttachment
   */
  public function setSshServiceAttachment($sshServiceAttachment)
  {
    $this->sshServiceAttachment = $sshServiceAttachment;
  }
  /**
   * @return string
   */
  public function getSshServiceAttachment()
  {
    return $this->sshServiceAttachment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrivateConfig::class, 'Google_Service_SecureSourceManager_PrivateConfig');
