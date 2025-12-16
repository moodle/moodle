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

class GoogleCloudIntegrationsV1alphaListIntegrationVersionsResponse extends \Google\Collection
{
  protected $collection_key = 'integrationVersions';
  protected $integrationVersionsType = GoogleCloudIntegrationsV1alphaIntegrationVersion::class;
  protected $integrationVersionsDataType = 'array';
  /**
   * A token, which can be sent as `page_token` to retrieve the next page. If
   * this field is omitted, there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Whether the user has no permission on the version or not.
   *
   * @var bool
   */
  public $noPermission;

  /**
   * The integrations which match the request.
   *
   * @param GoogleCloudIntegrationsV1alphaIntegrationVersion[] $integrationVersions
   */
  public function setIntegrationVersions($integrationVersions)
  {
    $this->integrationVersions = $integrationVersions;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaIntegrationVersion[]
   */
  public function getIntegrationVersions()
  {
    return $this->integrationVersions;
  }
  /**
   * A token, which can be sent as `page_token` to retrieve the next page. If
   * this field is omitted, there are no subsequent pages.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * Whether the user has no permission on the version or not.
   *
   * @param bool $noPermission
   */
  public function setNoPermission($noPermission)
  {
    $this->noPermission = $noPermission;
  }
  /**
   * @return bool
   */
  public function getNoPermission()
  {
    return $this->noPermission;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaListIntegrationVersionsResponse::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaListIntegrationVersionsResponse');
