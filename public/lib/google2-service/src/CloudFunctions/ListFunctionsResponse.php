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

namespace Google\Service\CloudFunctions;

class ListFunctionsResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $functionsType = CloudfunctionsFunction::class;
  protected $functionsDataType = 'array';
  /**
   * A token, which can be sent as `page_token` to retrieve the next page. If
   * this field is omitted, there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Locations that could not be reached. The response does not include any
   * functions from these locations.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * The functions that match the request.
   *
   * @param CloudfunctionsFunction[] $functions
   */
  public function setFunctions($functions)
  {
    $this->functions = $functions;
  }
  /**
   * @return CloudfunctionsFunction[]
   */
  public function getFunctions()
  {
    return $this->functions;
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
   * Locations that could not be reached. The response does not include any
   * functions from these locations.
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
class_alias(ListFunctionsResponse::class, 'Google_Service_CloudFunctions_ListFunctionsResponse');
