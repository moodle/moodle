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

namespace Google\Service\Apigateway;

class ApigatewayApi extends \Google\Model
{
  /**
   * API does not have a state yet.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * API is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * API is active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * API creation failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * API is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * API is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Output only. Created time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Resource labels to represent user-provided metadata. Refer to
   * cloud documentation on labels for more details.
   * https://cloud.google.com/compute/docs/labeling-resources
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. Immutable. The name of a Google Managed Service (
   * https://cloud.google.com/service-infrastructure/docs/glossary#managed). If
   * not specified, a new Service will automatically be created in the same
   * project as this API.
   *
   * @var string
   */
  public $managedService;
  /**
   * Output only. Resource name of the API. Format:
   * projects/{project}/locations/global/apis/{api}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. State of the API.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Updated time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Created time.
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
   * Optional. Display name.
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
   * Optional. Resource labels to represent user-provided metadata. Refer to
   * cloud documentation on labels for more details.
   * https://cloud.google.com/compute/docs/labeling-resources
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
   * Optional. Immutable. The name of a Google Managed Service (
   * https://cloud.google.com/service-infrastructure/docs/glossary#managed). If
   * not specified, a new Service will automatically be created in the same
   * project as this API.
   *
   * @param string $managedService
   */
  public function setManagedService($managedService)
  {
    $this->managedService = $managedService;
  }
  /**
   * @return string
   */
  public function getManagedService()
  {
    return $this->managedService;
  }
  /**
   * Output only. Resource name of the API. Format:
   * projects/{project}/locations/global/apis/{api}
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
   * Output only. State of the API.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, FAILED, DELETING,
   * UPDATING
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
   * Output only. Updated time.
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
class_alias(ApigatewayApi::class, 'Google_Service_Apigateway_ApigatewayApi');
