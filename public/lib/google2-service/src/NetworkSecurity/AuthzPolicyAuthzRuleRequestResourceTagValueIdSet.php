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

namespace Google\Service\NetworkSecurity;

class AuthzPolicyAuthzRuleRequestResourceTagValueIdSet extends \Google\Collection
{
  protected $collection_key = 'ids';
  /**
   * Required. A list of resource tag value permanent IDs to match against the
   * resource manager tags value associated with the source VM of a request. The
   * match follows AND semantics which means all the ids must match. Limited to
   * 5 ids in the Tag value id set.
   *
   * @var string[]
   */
  public $ids;

  /**
   * Required. A list of resource tag value permanent IDs to match against the
   * resource manager tags value associated with the source VM of a request. The
   * match follows AND semantics which means all the ids must match. Limited to
   * 5 ids in the Tag value id set.
   *
   * @param string[] $ids
   */
  public function setIds($ids)
  {
    $this->ids = $ids;
  }
  /**
   * @return string[]
   */
  public function getIds()
  {
    return $this->ids;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthzPolicyAuthzRuleRequestResourceTagValueIdSet::class, 'Google_Service_NetworkSecurity_AuthzPolicyAuthzRuleRequestResourceTagValueIdSet');
