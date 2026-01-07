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

class GoogleCloudSecuritycenterV2IssueResourceAzureMetadataAzureSubscription extends \Google\Model
{
  /**
   * The Azure subscription display name of the resource associated with the
   * issue.
   *
   * @var string
   */
  public $displayName;
  /**
   * The Azure subscription ID of the resource associated with the issue.
   *
   * @var string
   */
  public $id;

  /**
   * The Azure subscription display name of the resource associated with the
   * issue.
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
   * The Azure subscription ID of the resource associated with the issue.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2IssueResourceAzureMetadataAzureSubscription::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2IssueResourceAzureMetadataAzureSubscription');
