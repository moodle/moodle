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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ComputeTokensResponse extends \Google\Collection
{
  protected $collection_key = 'tokensInfo';
  protected $tokensInfoType = GoogleCloudAiplatformV1TokensInfo::class;
  protected $tokensInfoDataType = 'array';

  /**
   * Lists of tokens info from the input. A ComputeTokensRequest could have
   * multiple instances with a prompt in each instance. We also need to return
   * lists of tokens info for the request with multiple instances.
   *
   * @param GoogleCloudAiplatformV1TokensInfo[] $tokensInfo
   */
  public function setTokensInfo($tokensInfo)
  {
    $this->tokensInfo = $tokensInfo;
  }
  /**
   * @return GoogleCloudAiplatformV1TokensInfo[]
   */
  public function getTokensInfo()
  {
    return $this->tokensInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ComputeTokensResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ComputeTokensResponse');
