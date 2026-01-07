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

namespace Google\Service\NetworkServices;

class ListLbEdgeExtensionsResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $lbEdgeExtensionsType = LbEdgeExtension::class;
  protected $lbEdgeExtensionsDataType = 'array';
  /**
   * A token identifying a page of results that the server returns.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Locations that could not be reached.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * The list of `LbEdgeExtension` resources.
   *
   * @param LbEdgeExtension[] $lbEdgeExtensions
   */
  public function setLbEdgeExtensions($lbEdgeExtensions)
  {
    $this->lbEdgeExtensions = $lbEdgeExtensions;
  }
  /**
   * @return LbEdgeExtension[]
   */
  public function getLbEdgeExtensions()
  {
    return $this->lbEdgeExtensions;
  }
  /**
   * A token identifying a page of results that the server returns.
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
   * Locations that could not be reached.
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
class_alias(ListLbEdgeExtensionsResponse::class, 'Google_Service_NetworkServices_ListLbEdgeExtensionsResponse');
