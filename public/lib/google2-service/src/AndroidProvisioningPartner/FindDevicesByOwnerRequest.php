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

namespace Google\Service\AndroidProvisioningPartner;

class FindDevicesByOwnerRequest extends \Google\Collection
{
  /**
   * Unspecified section type.
   */
  public const SECTION_TYPE_SECTION_TYPE_UNSPECIFIED = 'SECTION_TYPE_UNSPECIFIED';
  /**
   * SIM-lock section type.
   */
  public const SECTION_TYPE_SECTION_TYPE_SIM_LOCK = 'SECTION_TYPE_SIM_LOCK';
  /**
   * Zero-touch enrollment section type.
   */
  public const SECTION_TYPE_SECTION_TYPE_ZERO_TOUCH = 'SECTION_TYPE_ZERO_TOUCH';
  protected $collection_key = 'googleWorkspaceCustomerId';
  /**
   * The list of customer IDs to search for.
   *
   * @var string[]
   */
  public $customerId;
  /**
   * The list of IDs of Google Workspace accounts to search for.
   *
   * @var string[]
   */
  public $googleWorkspaceCustomerId;
  /**
   * Required. The maximum number of devices to show in a page of results. Must
   * be between 1 and 100 inclusive.
   *
   * @var string
   */
  public $limit;
  /**
   * A token specifying which result page to return.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Required. The section type of the device's provisioning record.
   *
   * @var string
   */
  public $sectionType;

  /**
   * The list of customer IDs to search for.
   *
   * @param string[] $customerId
   */
  public function setCustomerId($customerId)
  {
    $this->customerId = $customerId;
  }
  /**
   * @return string[]
   */
  public function getCustomerId()
  {
    return $this->customerId;
  }
  /**
   * The list of IDs of Google Workspace accounts to search for.
   *
   * @param string[] $googleWorkspaceCustomerId
   */
  public function setGoogleWorkspaceCustomerId($googleWorkspaceCustomerId)
  {
    $this->googleWorkspaceCustomerId = $googleWorkspaceCustomerId;
  }
  /**
   * @return string[]
   */
  public function getGoogleWorkspaceCustomerId()
  {
    return $this->googleWorkspaceCustomerId;
  }
  /**
   * Required. The maximum number of devices to show in a page of results. Must
   * be between 1 and 100 inclusive.
   *
   * @param string $limit
   */
  public function setLimit($limit)
  {
    $this->limit = $limit;
  }
  /**
   * @return string
   */
  public function getLimit()
  {
    return $this->limit;
  }
  /**
   * A token specifying which result page to return.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * Required. The section type of the device's provisioning record.
   *
   * Accepted values: SECTION_TYPE_UNSPECIFIED, SECTION_TYPE_SIM_LOCK,
   * SECTION_TYPE_ZERO_TOUCH
   *
   * @param self::SECTION_TYPE_* $sectionType
   */
  public function setSectionType($sectionType)
  {
    $this->sectionType = $sectionType;
  }
  /**
   * @return self::SECTION_TYPE_*
   */
  public function getSectionType()
  {
    return $this->sectionType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FindDevicesByOwnerRequest::class, 'Google_Service_AndroidProvisioningPartner_FindDevicesByOwnerRequest');
