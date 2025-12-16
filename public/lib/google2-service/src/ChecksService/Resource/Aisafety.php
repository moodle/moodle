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

namespace Google\Service\ChecksService\Resource;

use Google\Service\ChecksService\GoogleChecksAisafetyV1alphaClassifyContentRequest;
use Google\Service\ChecksService\GoogleChecksAisafetyV1alphaClassifyContentResponse;

/**
 * The "aisafety" collection of methods.
 * Typical usage is:
 *  <code>
 *   $checksService = new Google\Service\ChecksService(...);
 *   $aisafety = $checksService->aisafety;
 *  </code>
 */
class Aisafety extends \Google\Service\Resource
{
  /**
   * Analyze a piece of content with the provided set of policies.
   * (aisafety.classifyContent)
   *
   * @param GoogleChecksAisafetyV1alphaClassifyContentRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleChecksAisafetyV1alphaClassifyContentResponse
   * @throws \Google\Service\Exception
   */
  public function classifyContent(GoogleChecksAisafetyV1alphaClassifyContentRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('classifyContent', [$params], GoogleChecksAisafetyV1alphaClassifyContentResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Aisafety::class, 'Google_Service_ChecksService_Resource_Aisafety');
