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

namespace Google\Service\Dfareporting\Resource;

use Google\Service\Dfareporting\StudioCreative;

/**
 * The "studioCreatives" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dfareportingService = new Google\Service\Dfareporting(...);
 *   $studioCreatives = $dfareportingService->studioCreatives;
 *  </code>
 */
class StudioCreatives extends \Google\Service\Resource
{
  /**
   * Gets a studio creative by ID. (studioCreatives.get)
   *
   * @param string $studioCreativeId Required. Studio creative ID.
   * @param array $optParams Optional parameters.
   * @return StudioCreative
   * @throws \Google\Service\Exception
   */
  public function get($studioCreativeId, $optParams = [])
  {
    $params = ['studioCreativeId' => $studioCreativeId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], StudioCreative::class);
  }
  /**
   * Inserts a new studio creative. (studioCreatives.insert)
   *
   * @param StudioCreative $postBody
   * @param array $optParams Optional parameters.
   * @return StudioCreative
   * @throws \Google\Service\Exception
   */
  public function insert(StudioCreative $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], StudioCreative::class);
  }
  /**
   * Publish for a studio creative. (studioCreatives.publish)
   *
   * @param string $studioCreativeId Required. Studio creative ID.
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function publish($studioCreativeId, $optParams = [])
  {
    $params = ['studioCreativeId' => $studioCreativeId];
    $params = array_merge($params, $optParams);
    return $this->call('publish', [$params]);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StudioCreatives::class, 'Google_Service_Dfareporting_Resource_StudioCreatives');
