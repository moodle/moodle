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

namespace Google\Service\Walletobjects;

class FlightObjectListResponse extends \Google\Collection
{
  protected $collection_key = 'resources';
  protected $paginationType = Pagination::class;
  protected $paginationDataType = '';
  protected $resourcesType = FlightObject::class;
  protected $resourcesDataType = 'array';

  /**
   * Pagination of the response.
   *
   * @param Pagination $pagination
   */
  public function setPagination(Pagination $pagination)
  {
    $this->pagination = $pagination;
  }
  /**
   * @return Pagination
   */
  public function getPagination()
  {
    return $this->pagination;
  }
  /**
   * Resources corresponding to the list request.
   *
   * @param FlightObject[] $resources
   */
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return FlightObject[]
   */
  public function getResources()
  {
    return $this->resources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FlightObjectListResponse::class, 'Google_Service_Walletobjects_FlightObjectListResponse');
