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

class RoutersListRoutePolicies extends \Google\Collection
{
  protected $collection_key = 'unreachables';
  /**
   * @var string
   */
  public $etag;
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of resource.
   * Alwayscompute#routersListRoutePolicies for lists of route policies.
   *
   * @var string
   */
  public $kind;
  /**
   * [Output Only] This token allows you to get the next page of results for
   * list requests. If the number of results is larger thanmaxResults, use the
   * nextPageToken as a value for the query parameter pageToken in the next list
   * request. Subsequent list requests will have their own nextPageToken to
   * continue paging through the results.
   *
   * @var string
   */
  public $nextPageToken;
  protected $resultType = RoutePolicy::class;
  protected $resultDataType = 'array';
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
  protected $warningType = RoutersListRoutePoliciesWarning::class;
  protected $warningDataType = '';

  /**
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
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
   * Output only. [Output Only] Type of resource.
   * Alwayscompute#routersListRoutePolicies for lists of route policies.
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
   * continue paging through the results.
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
   * [Output Only] A list of route policies.
   *
   * @param RoutePolicy[] $result
   */
  public function setResult($result)
  {
    $this->result = $result;
  }
  /**
   * @return RoutePolicy[]
   */
  public function getResult()
  {
    return $this->result;
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
   * @param RoutersListRoutePoliciesWarning $warning
   */
  public function setWarning(RoutersListRoutePoliciesWarning $warning)
  {
    $this->warning = $warning;
  }
  /**
   * @return RoutersListRoutePoliciesWarning
   */
  public function getWarning()
  {
    return $this->warning;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RoutersListRoutePolicies::class, 'Google_Service_Compute_RoutersListRoutePolicies');
