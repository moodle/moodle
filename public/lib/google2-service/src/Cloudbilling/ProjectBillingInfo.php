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

namespace Google\Service\Cloudbilling;

class ProjectBillingInfo extends \Google\Model
{
  /**
   * The resource name of the billing account associated with the project, if
   * any. For example, `billingAccounts/012345-567890-ABCDEF`.
   *
   * @var string
   */
  public $billingAccountName;
  /**
   * Output only. True if the project is associated with an open billing
   * account, to which usage on the project is charged. False if the project is
   * associated with a closed billing account, or no billing account at all, and
   * therefore cannot use paid services.
   *
   * @var bool
   */
  public $billingEnabled;
  /**
   * Output only. The resource name for the `ProjectBillingInfo`; has the form
   * `projects/{project_id}/billingInfo`. For example, the resource name for the
   * billing information for project `tokyo-rain-123` would be `projects/tokyo-
   * rain-123/billingInfo`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The ID of the project that this `ProjectBillingInfo`
   * represents, such as `tokyo-rain-123`. This is a convenience field so that
   * you don't need to parse the `name` field to obtain a project ID.
   *
   * @var string
   */
  public $projectId;

  /**
   * The resource name of the billing account associated with the project, if
   * any. For example, `billingAccounts/012345-567890-ABCDEF`.
   *
   * @param string $billingAccountName
   */
  public function setBillingAccountName($billingAccountName)
  {
    $this->billingAccountName = $billingAccountName;
  }
  /**
   * @return string
   */
  public function getBillingAccountName()
  {
    return $this->billingAccountName;
  }
  /**
   * Output only. True if the project is associated with an open billing
   * account, to which usage on the project is charged. False if the project is
   * associated with a closed billing account, or no billing account at all, and
   * therefore cannot use paid services.
   *
   * @param bool $billingEnabled
   */
  public function setBillingEnabled($billingEnabled)
  {
    $this->billingEnabled = $billingEnabled;
  }
  /**
   * @return bool
   */
  public function getBillingEnabled()
  {
    return $this->billingEnabled;
  }
  /**
   * Output only. The resource name for the `ProjectBillingInfo`; has the form
   * `projects/{project_id}/billingInfo`. For example, the resource name for the
   * billing information for project `tokyo-rain-123` would be `projects/tokyo-
   * rain-123/billingInfo`.
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
   * Output only. The ID of the project that this `ProjectBillingInfo`
   * represents, such as `tokyo-rain-123`. This is a convenience field so that
   * you don't need to parse the `name` field to obtain a project ID.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectBillingInfo::class, 'Google_Service_Cloudbilling_ProjectBillingInfo');
