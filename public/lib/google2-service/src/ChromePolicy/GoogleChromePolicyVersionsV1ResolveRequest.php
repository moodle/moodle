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

namespace Google\Service\ChromePolicy;

class GoogleChromePolicyVersionsV1ResolveRequest extends \Google\Model
{
  /**
   * The maximum number of policies to return, defaults to 100 and has a maximum
   * of 1000.
   *
   * @var int
   */
  public $pageSize;
  /**
   * The page token used to retrieve a specific page of the request.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Required. The schema filter to apply to the resolve request. Specify a
   * schema name to view a particular schema, for example:
   * chrome.users.ShowLogoutButton Wildcards are supported, but only in the leaf
   * portion of the schema name. Wildcards cannot be used in namespace directly.
   * Please read https://developers.google.com/chrome/policy/guides/policy-
   * schemas for details on schema namespaces. For example: Valid:
   * "chrome.users.*", "chrome.users.apps.*", "chrome.printers.*" Invalid: "*",
   * "*.users", "chrome.*", "chrome.*.apps.*"
   *
   * @var string
   */
  public $policySchemaFilter;
  protected $policyTargetKeyType = GoogleChromePolicyVersionsV1PolicyTargetKey::class;
  protected $policyTargetKeyDataType = '';

  /**
   * The maximum number of policies to return, defaults to 100 and has a maximum
   * of 1000.
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
   * The page token used to retrieve a specific page of the request.
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
  /**
   * Required. The schema filter to apply to the resolve request. Specify a
   * schema name to view a particular schema, for example:
   * chrome.users.ShowLogoutButton Wildcards are supported, but only in the leaf
   * portion of the schema name. Wildcards cannot be used in namespace directly.
   * Please read https://developers.google.com/chrome/policy/guides/policy-
   * schemas for details on schema namespaces. For example: Valid:
   * "chrome.users.*", "chrome.users.apps.*", "chrome.printers.*" Invalid: "*",
   * "*.users", "chrome.*", "chrome.*.apps.*"
   *
   * @param string $policySchemaFilter
   */
  public function setPolicySchemaFilter($policySchemaFilter)
  {
    $this->policySchemaFilter = $policySchemaFilter;
  }
  /**
   * @return string
   */
  public function getPolicySchemaFilter()
  {
    return $this->policySchemaFilter;
  }
  /**
   * Required. The key of the target resource on which the policies should be
   * resolved.
   *
   * @param GoogleChromePolicyVersionsV1PolicyTargetKey $policyTargetKey
   */
  public function setPolicyTargetKey(GoogleChromePolicyVersionsV1PolicyTargetKey $policyTargetKey)
  {
    $this->policyTargetKey = $policyTargetKey;
  }
  /**
   * @return GoogleChromePolicyVersionsV1PolicyTargetKey
   */
  public function getPolicyTargetKey()
  {
    return $this->policyTargetKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromePolicyVersionsV1ResolveRequest::class, 'Google_Service_ChromePolicy_GoogleChromePolicyVersionsV1ResolveRequest');
