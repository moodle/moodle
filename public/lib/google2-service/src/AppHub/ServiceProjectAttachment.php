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

namespace Google\Service\AppHub;

class ServiceProjectAttachment extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The ServiceProjectAttachment is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The ServiceProjectAttachment is ready. This means Services and Workloads
   * under the corresponding ServiceProjectAttachment is ready for registration.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The ServiceProjectAttachment is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Output only. Create time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Identifier. The resource name of a ServiceProjectAttachment. Format:
   * `"projects/{host-project-
   * id}/locations/global/serviceProjectAttachments/{service-project-id}."`
   *
   * @var string
   */
  public $name;
  /**
   * Required. Immutable. Service project name in the format: `"projects/abc"`
   * or `"projects/123"`. As input, project name with either project id or
   * number are accepted. As output, this field will contain project number.
   *
   * @var string
   */
  public $serviceProject;
  /**
   * Output only. ServiceProjectAttachment state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. A globally unique identifier (in UUID4 format) for the
   * `ServiceProjectAttachment`.
   *
   * @var string
   */
  public $uid;

  /**
   * Output only. Create time.
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
   * Identifier. The resource name of a ServiceProjectAttachment. Format:
   * `"projects/{host-project-
   * id}/locations/global/serviceProjectAttachments/{service-project-id}."`
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
   * Required. Immutable. Service project name in the format: `"projects/abc"`
   * or `"projects/123"`. As input, project name with either project id or
   * number are accepted. As output, this field will contain project number.
   *
   * @param string $serviceProject
   */
  public function setServiceProject($serviceProject)
  {
    $this->serviceProject = $serviceProject;
  }
  /**
   * @return string
   */
  public function getServiceProject()
  {
    return $this->serviceProject;
  }
  /**
   * Output only. ServiceProjectAttachment state.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. A globally unique identifier (in UUID4 format) for the
   * `ServiceProjectAttachment`.
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
class_alias(ServiceProjectAttachment::class, 'Google_Service_AppHub_ServiceProjectAttachment');
