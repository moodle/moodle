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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1BatchUpdateUserLicensesRequest extends \Google\Model
{
  /**
   * Optional. If true, if user licenses removed associated license config, the
   * user license will be deleted. By default which is false, the user license
   * will be updated to unassigned state.
   *
   * @var bool
   */
  public $deleteUnassignedUserLicenses;
  protected $inlineSourceType = GoogleCloudDiscoveryengineV1BatchUpdateUserLicensesRequestInlineSource::class;
  protected $inlineSourceDataType = '';

  /**
   * Optional. If true, if user licenses removed associated license config, the
   * user license will be deleted. By default which is false, the user license
   * will be updated to unassigned state.
   *
   * @param bool $deleteUnassignedUserLicenses
   */
  public function setDeleteUnassignedUserLicenses($deleteUnassignedUserLicenses)
  {
    $this->deleteUnassignedUserLicenses = $deleteUnassignedUserLicenses;
  }
  /**
   * @return bool
   */
  public function getDeleteUnassignedUserLicenses()
  {
    return $this->deleteUnassignedUserLicenses;
  }
  /**
   * The inline source for the input content for document embeddings.
   *
   * @param GoogleCloudDiscoveryengineV1BatchUpdateUserLicensesRequestInlineSource $inlineSource
   */
  public function setInlineSource(GoogleCloudDiscoveryengineV1BatchUpdateUserLicensesRequestInlineSource $inlineSource)
  {
    $this->inlineSource = $inlineSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1BatchUpdateUserLicensesRequestInlineSource
   */
  public function getInlineSource()
  {
    return $this->inlineSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1BatchUpdateUserLicensesRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1BatchUpdateUserLicensesRequest');
