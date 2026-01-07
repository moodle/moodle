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

namespace Google\Service\Compute;

class ServiceAttachmentConsumerProjectLimit extends \Google\Model
{
  /**
   * The value of the limit to set. For endpoint_url, the limit should be no
   * more than 1.
   *
   * @var string
   */
  public $connectionLimit;
  /**
   * The network URL for the network to set the limit for.
   *
   * @var string
   */
  public $networkUrl;
  /**
   * The project id or number for the project to set the limit for.
   *
   * @var string
   */
  public $projectIdOrNum;

  /**
   * The value of the limit to set. For endpoint_url, the limit should be no
   * more than 1.
   *
   * @param string $connectionLimit
   */
  public function setConnectionLimit($connectionLimit)
  {
    $this->connectionLimit = $connectionLimit;
  }
  /**
   * @return string
   */
  public function getConnectionLimit()
  {
    return $this->connectionLimit;
  }
  /**
   * The network URL for the network to set the limit for.
   *
   * @param string $networkUrl
   */
  public function setNetworkUrl($networkUrl)
  {
    $this->networkUrl = $networkUrl;
  }
  /**
   * @return string
   */
  public function getNetworkUrl()
  {
    return $this->networkUrl;
  }
  /**
   * The project id or number for the project to set the limit for.
   *
   * @param string $projectIdOrNum
   */
  public function setProjectIdOrNum($projectIdOrNum)
  {
    $this->projectIdOrNum = $projectIdOrNum;
  }
  /**
   * @return string
   */
  public function getProjectIdOrNum()
  {
    return $this->projectIdOrNum;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceAttachmentConsumerProjectLimit::class, 'Google_Service_Compute_ServiceAttachmentConsumerProjectLimit');
