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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1ListDataProductsResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $dataProductsType = GoogleCloudDataplexV1DataProduct::class;
  protected $dataProductsDataType = 'array';
  /**
   * A token, which can be sent as page_token to retrieve the next page. If this
   * field is empty, then there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Unordered list. Locations that the service couldn't reach.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * The Data Products for the requested filter criteria.
   *
   * @param GoogleCloudDataplexV1DataProduct[] $dataProducts
   */
  public function setDataProducts($dataProducts)
  {
    $this->dataProducts = $dataProducts;
  }
  /**
   * @return GoogleCloudDataplexV1DataProduct[]
   */
  public function getDataProducts()
  {
    return $this->dataProducts;
  }
  /**
   * A token, which can be sent as page_token to retrieve the next page. If this
   * field is empty, then there are no subsequent pages.
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
   * Unordered list. Locations that the service couldn't reach.
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
class_alias(GoogleCloudDataplexV1ListDataProductsResponse::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1ListDataProductsResponse');
