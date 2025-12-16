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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1SearchRelatedAccountGroupMembershipsRequest extends \Google\Model
{
  /**
   * Optional. The unique stable account identifier used to search connections.
   * The identifier should correspond to an `account_id` provided in a previous
   * `CreateAssessment` or `AnnotateAssessment` call. Either hashed_account_id
   * or account_id must be set, but not both.
   *
   * @var string
   */
  public $accountId;
  /**
   * Optional. Deprecated: use `account_id` instead. The unique stable hashed
   * account identifier used to search connections. The identifier should
   * correspond to a `hashed_account_id` provided in a previous
   * `CreateAssessment` or `AnnotateAssessment` call. Either hashed_account_id
   * or account_id must be set, but not both.
   *
   * @deprecated
   * @var string
   */
  public $hashedAccountId;
  /**
   * Optional. The maximum number of groups to return. The service might return
   * fewer than this value. If unspecified, at most 50 groups are returned. The
   * maximum value is 1000; values above 1000 are coerced to 1000.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Optional. A page token, received from a previous
   * `SearchRelatedAccountGroupMemberships` call. Provide this to retrieve the
   * subsequent page. When paginating, all other parameters provided to
   * `SearchRelatedAccountGroupMemberships` must match the call that provided
   * the page token.
   *
   * @var string
   */
  public $pageToken;

  /**
   * Optional. The unique stable account identifier used to search connections.
   * The identifier should correspond to an `account_id` provided in a previous
   * `CreateAssessment` or `AnnotateAssessment` call. Either hashed_account_id
   * or account_id must be set, but not both.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * Optional. Deprecated: use `account_id` instead. The unique stable hashed
   * account identifier used to search connections. The identifier should
   * correspond to a `hashed_account_id` provided in a previous
   * `CreateAssessment` or `AnnotateAssessment` call. Either hashed_account_id
   * or account_id must be set, but not both.
   *
   * @deprecated
   * @param string $hashedAccountId
   */
  public function setHashedAccountId($hashedAccountId)
  {
    $this->hashedAccountId = $hashedAccountId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getHashedAccountId()
  {
    return $this->hashedAccountId;
  }
  /**
   * Optional. The maximum number of groups to return. The service might return
   * fewer than this value. If unspecified, at most 50 groups are returned. The
   * maximum value is 1000; values above 1000 are coerced to 1000.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * Optional. A page token, received from a previous
   * `SearchRelatedAccountGroupMemberships` call. Provide this to retrieve the
   * subsequent page. When paginating, all other parameters provided to
   * `SearchRelatedAccountGroupMemberships` must match the call that provided
   * the page token.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1SearchRelatedAccountGroupMembershipsRequest::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1SearchRelatedAccountGroupMembershipsRequest');
