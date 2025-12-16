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

class GoogleCloudDiscoveryengineV1alphaBatchUpdateUserLicensesResponse extends \Google\Collection
{
  protected $collection_key = 'userLicenses';
  protected $errorSamplesType = GoogleRpcStatus::class;
  protected $errorSamplesDataType = 'array';
  protected $userLicensesType = GoogleCloudDiscoveryengineV1alphaUserLicense::class;
  protected $userLicensesDataType = 'array';

  /**
   * A sample of errors encountered while processing the request.
   *
   * @param GoogleRpcStatus[] $errorSamples
   */
  public function setErrorSamples($errorSamples)
  {
    $this->errorSamples = $errorSamples;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getErrorSamples()
  {
    return $this->errorSamples;
  }
  /**
   * UserLicenses successfully updated.
   *
   * @param GoogleCloudDiscoveryengineV1alphaUserLicense[] $userLicenses
   */
  public function setUserLicenses($userLicenses)
  {
    $this->userLicenses = $userLicenses;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaUserLicense[]
   */
  public function getUserLicenses()
  {
    return $this->userLicenses;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaBatchUpdateUserLicensesResponse::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaBatchUpdateUserLicensesResponse');
