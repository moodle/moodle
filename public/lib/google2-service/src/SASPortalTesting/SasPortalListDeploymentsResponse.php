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

namespace Google\Service\SASPortalTesting;

class SasPortalListDeploymentsResponse extends \Google\Collection
{
  protected $collection_key = 'deployments';
  protected $deploymentsType = SasPortalDeployment::class;
  protected $deploymentsDataType = 'array';
  /**
   * A pagination token returned from a previous call to ListDeployments that
   * indicates from where listing should continue. If the field is missing or
   * empty, it means there are no more deployments.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The deployments that match the request.
   *
   * @param SasPortalDeployment[] $deployments
   */
  public function setDeployments($deployments)
  {
    $this->deployments = $deployments;
  }
  /**
   * @return SasPortalDeployment[]
   */
  public function getDeployments()
  {
    return $this->deployments;
  }
  /**
   * A pagination token returned from a previous call to ListDeployments that
   * indicates from where listing should continue. If the field is missing or
   * empty, it means there are no more deployments.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SasPortalListDeploymentsResponse::class, 'Google_Service_SASPortalTesting_SasPortalListDeploymentsResponse');
