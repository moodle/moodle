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

class AuthzPolicyAuthzRuleRequestResource extends \Google\Model
{
  protected $iamServiceAccountType = AuthzPolicyAuthzRuleStringMatch::class;
  protected $iamServiceAccountDataType = '';
  protected $tagValueIdSetType = AuthzPolicyAuthzRuleRequestResourceTagValueIdSet::class;
  protected $tagValueIdSetDataType = '';

  /**
   * Optional. An IAM service account to match against the source service
   * account of the VM sending the request.
   *
   * @param AuthzPolicyAuthzRuleStringMatch $iamServiceAccount
   */
  public function setIamServiceAccount(AuthzPolicyAuthzRuleStringMatch $iamServiceAccount)
  {
    $this->iamServiceAccount = $iamServiceAccount;
  }
  /**
   * @return AuthzPolicyAuthzRuleStringMatch
   */
  public function getIamServiceAccount()
  {
    return $this->iamServiceAccount;
  }
  /**
   * Optional. A list of resource tag value permanent IDs to match against the
   * resource manager tags value associated with the source VM of a request.
   *
   * @param AuthzPolicyAuthzRuleRequestResourceTagValueIdSet $tagValueIdSet
   */
  public function setTagValueIdSet(AuthzPolicyAuthzRuleRequestResourceTagValueIdSet $tagValueIdSet)
  {
    $this->tagValueIdSet = $tagValueIdSet;
  }
  /**
   * @return AuthzPolicyAuthzRuleRequestResourceTagValueIdSet
   */
  public function getTagValueIdSet()
  {
    return $this->tagValueIdSet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthzPolicyAuthzRuleRequestResource::class, 'Google_Service_NetworkSecurity_AuthzPolicyAuthzRuleRequestResource');
