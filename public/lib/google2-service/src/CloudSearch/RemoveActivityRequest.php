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

namespace Google\Service\CloudSearch;

class RemoveActivityRequest extends \Google\Model
{
  protected $requestOptionsType = RequestOptions::class;
  protected $requestOptionsDataType = '';
  protected $userActivityType = UserActivity::class;
  protected $userActivityDataType = '';

  /**
   * Request options, such as the search application and clientId.
   *
   * @param RequestOptions $requestOptions
   */
  public function setRequestOptions(RequestOptions $requestOptions)
  {
    $this->requestOptions = $requestOptions;
  }
  /**
   * @return RequestOptions
   */
  public function getRequestOptions()
  {
    return $this->requestOptions;
  }
  /**
   * User Activity containing the data to be deleted.
   *
   * @param UserActivity $userActivity
   */
  public function setUserActivity(UserActivity $userActivity)
  {
    $this->userActivity = $userActivity;
  }
  /**
   * @return UserActivity
   */
  public function getUserActivity()
  {
    return $this->userActivity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RemoveActivityRequest::class, 'Google_Service_CloudSearch_RemoveActivityRequest');
