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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2DataProfileLocation extends \Google\Model
{
  /**
   * The ID of the folder within an organization to scan.
   *
   * @var string
   */
  public $folderId;
  /**
   * The ID of an organization to scan.
   *
   * @var string
   */
  public $organizationId;

  /**
   * The ID of the folder within an organization to scan.
   *
   * @param string $folderId
   */
  public function setFolderId($folderId)
  {
    $this->folderId = $folderId;
  }
  /**
   * @return string
   */
  public function getFolderId()
  {
    return $this->folderId;
  }
  /**
   * The ID of an organization to scan.
   *
   * @param string $organizationId
   */
  public function setOrganizationId($organizationId)
  {
    $this->organizationId = $organizationId;
  }
  /**
   * @return string
   */
  public function getOrganizationId()
  {
    return $this->organizationId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DataProfileLocation::class, 'Google_Service_DLP_GooglePrivacyDlpV2DataProfileLocation');
