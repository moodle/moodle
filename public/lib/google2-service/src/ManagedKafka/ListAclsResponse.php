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

namespace Google\Service\ManagedKafka;

class ListAclsResponse extends \Google\Collection
{
  protected $collection_key = 'acls';
  protected $aclsType = Acl::class;
  protected $aclsDataType = 'array';
  /**
   * A token that can be sent as `page_token` to retrieve the next page of
   * results. If this field is omitted, there are no more results.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The list of acls in the requested parent. The order of the acls is
   * unspecified.
   *
   * @param Acl[] $acls
   */
  public function setAcls($acls)
  {
    $this->acls = $acls;
  }
  /**
   * @return Acl[]
   */
  public function getAcls()
  {
    return $this->acls;
  }
  /**
   * A token that can be sent as `page_token` to retrieve the next page of
   * results. If this field is omitted, there are no more results.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListAclsResponse::class, 'Google_Service_ManagedKafka_ListAclsResponse');
