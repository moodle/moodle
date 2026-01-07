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

class GoogleCloudApigeeV1IngressConfig extends \Google\Collection
{
  protected $collection_key = 'environmentGroups';
  protected $environmentGroupsType = GoogleCloudApigeeV1EnvironmentGroupConfig::class;
  protected $environmentGroupsDataType = 'array';
  /**
   * Name of the resource in the following format:
   * `organizations/{org}/deployedIngressConfig`.
   *
   * @var string
   */
  public $name;
  /**
   * Time at which the IngressConfig revision was created.
   *
   * @var string
   */
  public $revisionCreateTime;
  /**
   * Revision id that defines the ordering on IngressConfig resources. The
   * higher the revision, the more recently the configuration was deployed.
   *
   * @var string
   */
  public $revisionId;
  /**
   * A unique id for the ingress config that will only change if the
   * organization is deleted and recreated.
   *
   * @var string
   */
  public $uid;

  /**
   * List of environment groups in the organization.
   *
   * @param GoogleCloudApigeeV1EnvironmentGroupConfig[] $environmentGroups
   */
  public function setEnvironmentGroups($environmentGroups)
  {
    $this->environmentGroups = $environmentGroups;
  }
  /**
   * @return GoogleCloudApigeeV1EnvironmentGroupConfig[]
   */
  public function getEnvironmentGroups()
  {
    return $this->environmentGroups;
  }
  /**
   * Name of the resource in the following format:
   * `organizations/{org}/deployedIngressConfig`.
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
   * Time at which the IngressConfig revision was created.
   *
   * @param string $revisionCreateTime
   */
  public function setRevisionCreateTime($revisionCreateTime)
  {
    $this->revisionCreateTime = $revisionCreateTime;
  }
  /**
   * @return string
   */
  public function getRevisionCreateTime()
  {
    return $this->revisionCreateTime;
  }
  /**
   * Revision id that defines the ordering on IngressConfig resources. The
   * higher the revision, the more recently the configuration was deployed.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * A unique id for the ingress config that will only change if the
   * organization is deleted and recreated.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1IngressConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1IngressConfig');
