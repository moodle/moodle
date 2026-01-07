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

class ReservationSubBlocksListResponse extends \Google\Collection
{
  protected $collection_key = 'items';
  /**
   * Unique identifier for the resource; defined by the server.
   *
   * @var string
   */
  public $id;
  protected $itemsType = ReservationSubBlock::class;
  protected $itemsDataType = 'array';
  /**
   * Type of the resource. Alwayscompute#reservationSubBlock for a list of
   * reservation subBlocks.
   *
   * @var string
   */
  public $kind;
  /**
   * This token allows you to get the next page of results for list requests. If
   * the number of results is larger thanmaxResults, use the nextPageToken as a
   * value for the query parameter pageToken in the next list request.
   * Subsequent list requests will have their own nextPageToken to continue
   * paging through the results.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Server-defined URL for this resource.
   *
   * @var string
   */
  public $selfLink;
  protected $warningType = ReservationSubBlocksListResponseWarning::class;
  protected $warningDataType = '';

  /**
   * Unique identifier for the resource; defined by the server.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * A list of reservation subBlock resources.
   *
   * @param ReservationSubBlock[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return ReservationSubBlock[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Type of the resource. Alwayscompute#reservationSubBlock for a list of
   * reservation subBlocks.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * This token allows you to get the next page of results for list requests. If
   * the number of results is larger thanmaxResults, use the nextPageToken as a
   * value for the query parameter pageToken in the next list request.
   * Subsequent list requests will have their own nextPageToken to continue
   * paging through the results.
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
   * Server-defined URL for this resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Informational warning message.
   *
   * @param ReservationSubBlocksListResponseWarning $warning
   */
  public function setWarning(ReservationSubBlocksListResponseWarning $warning)
  {
    $this->warning = $warning;
  }
  /**
   * @return ReservationSubBlocksListResponseWarning
   */
  public function getWarning()
  {
    return $this->warning;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReservationSubBlocksListResponse::class, 'Google_Service_Compute_ReservationSubBlocksListResponse');
