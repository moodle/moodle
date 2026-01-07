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

class ListMeshRouteViewsResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $meshRouteViewsType = MeshRouteView::class;
  protected $meshRouteViewsDataType = 'array';
  /**
   * A token, which can be sent as `page_token` to retrieve the next page. If
   * this field is omitted, there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Unreachable resources. Populated when the request attempts to list all
   * resources across all supported locations, while some locations are
   * temporarily unavailable.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * List of MeshRouteView resources.
   *
   * @param MeshRouteView[] $meshRouteViews
   */
  public function setMeshRouteViews($meshRouteViews)
  {
    $this->meshRouteViews = $meshRouteViews;
  }
  /**
   * @return MeshRouteView[]
   */
  public function getMeshRouteViews()
  {
    return $this->meshRouteViews;
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
   * Unreachable resources. Populated when the request attempts to list all
   * resources across all supported locations, while some locations are
   * temporarily unavailable.
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
class_alias(ListMeshRouteViewsResponse::class, 'Google_Service_NetworkServices_ListMeshRouteViewsResponse');
