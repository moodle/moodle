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

class UsableSubnetworksAggregatedList extends \Google\Collection
{
  protected $collection_key = 'unreachables';
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @var string
   */
  public $id;
  protected $itemsType = UsableSubnetwork::class;
  protected $itemsDataType = 'array';
  /**
   * Output only. [Output Only] Type of resource.
   * Alwayscompute#usableSubnetworksAggregatedList for aggregated lists of
   * usable subnetworks.
   *
   * @var string
   */
  public $kind;
  /**
   * [Output Only] This token allows you to get the next page of results for
   * list requests. If the number of results is larger thanmaxResults, use the
   * nextPageToken as a value for the query parameter pageToken in the next list
   * request. Subsequent list requests will have their own nextPageToken to
   * continue paging through the results. In special cases listUsable may return
   * 0 subnetworks andnextPageToken which still should be used to get the next
   * page of results.
   *
   * @var string
   */
  public $nextPageToken;
  protected $scopedWarningsType = SubnetworksScopedWarning::class;
  protected $scopedWarningsDataType = 'array';
  /**
   * Output only. [Output Only] Server-defined URL for this resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. [Output Only] Unreachable resources.
   *
   * @var string[]
   */
  public $unreachables;
  protected $warningType = UsableSubnetworksAggregatedListWarning::class;
  protected $warningDataType = '';

  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
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
   * [Output] A list of usable subnetwork URLs.
   *
   * @param UsableSubnetwork[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return UsableSubnetwork[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Output only. [Output Only] Type of resource.
   * Alwayscompute#usableSubnetworksAggregatedList for aggregated lists of
   * usable subnetworks.
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
   * [Output Only] This token allows you to get the next page of results for
   * list requests. If the number of results is larger thanmaxResults, use the
   * nextPageToken as a value for the query parameter pageToken in the next list
   * request. Subsequent list requests will have their own nextPageToken to
   * continue paging through the results. In special cases listUsable may return
   * 0 subnetworks andnextPageToken which still should be used to get the next
   * page of results.
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
   * Output only. [Output Only] Informational warning messages for failures
   * encountered from scopes.
   *
   * @param SubnetworksScopedWarning[] $scopedWarnings
   */
  public function setScopedWarnings($scopedWarnings)
  {
    $this->scopedWarnings = $scopedWarnings;
  }
  /**
   * @return SubnetworksScopedWarning[]
   */
  public function getScopedWarnings()
  {
    return $this->scopedWarnings;
  }
  /**
   * Output only. [Output Only] Server-defined URL for this resource.
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
   * Output only. [Output Only] Unreachable resources.
   *
   * @param string[] $unreachables
   */
  public function setUnreachables($unreachables)
  {
    $this->unreachables = $unreachables;
  }
  /**
   * @return string[]
   */
  public function getUnreachables()
  {
    return $this->unreachables;
  }
  /**
   * [Output Only] Informational warning message.
   *
   * @param UsableSubnetworksAggregatedListWarning $warning
   */
  public function setWarning(UsableSubnetworksAggregatedListWarning $warning)
  {
    $this->warning = $warning;
  }
  /**
   * @return UsableSubnetworksAggregatedListWarning
   */
  public function getWarning()
  {
    return $this->warning;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UsableSubnetworksAggregatedList::class, 'Google_Service_Compute_UsableSubnetworksAggregatedList');
