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

namespace Google\Service\Config;

class ListTerraformVersionsResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  /**
   * Token to be supplied to the next ListTerraformVersions request via
   * `page_token` to obtain the next set of results.
   *
   * @var string
   */
  public $nextPageToken;
  protected $terraformVersionsType = TerraformVersion::class;
  protected $terraformVersionsDataType = 'array';
  /**
   * Unreachable resources, if any.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * Token to be supplied to the next ListTerraformVersions request via
   * `page_token` to obtain the next set of results.
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
   * List of TerraformVersions.
   *
   * @param TerraformVersion[] $terraformVersions
   */
  public function setTerraformVersions($terraformVersions)
  {
    $this->terraformVersions = $terraformVersions;
  }
  /**
   * @return TerraformVersion[]
   */
  public function getTerraformVersions()
  {
    return $this->terraformVersions;
  }
  /**
   * Unreachable resources, if any.
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListTerraformVersionsResponse::class, 'Google_Service_Config_ListTerraformVersionsResponse');
