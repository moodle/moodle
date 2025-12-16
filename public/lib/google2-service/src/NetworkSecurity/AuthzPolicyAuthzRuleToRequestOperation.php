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

class AuthzPolicyAuthzRuleToRequestOperation extends \Google\Collection
{
  protected $collection_key = 'paths';
  protected $headerSetType = AuthzPolicyAuthzRuleToRequestOperationHeaderSet::class;
  protected $headerSetDataType = '';
  protected $hostsType = AuthzPolicyAuthzRuleStringMatch::class;
  protected $hostsDataType = 'array';
  /**
   * Optional. A list of HTTP methods to match against. Each entry must be a
   * valid HTTP method name (GET, PUT, POST, HEAD, PATCH, DELETE, OPTIONS). It
   * only allows exact match and is always case sensitive. Limited to 10 methods
   * per Authorization Policy.
   *
   * @var string[]
   */
  public $methods;
  protected $pathsType = AuthzPolicyAuthzRuleStringMatch::class;
  protected $pathsDataType = 'array';

  /**
   * Optional. A list of headers to match against in http header.
   *
   * @param AuthzPolicyAuthzRuleToRequestOperationHeaderSet $headerSet
   */
  public function setHeaderSet(AuthzPolicyAuthzRuleToRequestOperationHeaderSet $headerSet)
  {
    $this->headerSet = $headerSet;
  }
  /**
   * @return AuthzPolicyAuthzRuleToRequestOperationHeaderSet
   */
  public function getHeaderSet()
  {
    return $this->headerSet;
  }
  /**
   * Optional. A list of HTTP Hosts to match against. The match can be one of
   * exact, prefix, suffix, or contains (substring match). Matches are always
   * case sensitive unless the ignoreCase is set. Limited to 10 hosts per
   * Authorization Policy.
   *
   * @param AuthzPolicyAuthzRuleStringMatch[] $hosts
   */
  public function setHosts($hosts)
  {
    $this->hosts = $hosts;
  }
  /**
   * @return AuthzPolicyAuthzRuleStringMatch[]
   */
  public function getHosts()
  {
    return $this->hosts;
  }
  /**
   * Optional. A list of HTTP methods to match against. Each entry must be a
   * valid HTTP method name (GET, PUT, POST, HEAD, PATCH, DELETE, OPTIONS). It
   * only allows exact match and is always case sensitive. Limited to 10 methods
   * per Authorization Policy.
   *
   * @param string[] $methods
   */
  public function setMethods($methods)
  {
    $this->methods = $methods;
  }
  /**
   * @return string[]
   */
  public function getMethods()
  {
    return $this->methods;
  }
  /**
   * Optional. A list of paths to match against. The match can be one of exact,
   * prefix, suffix, or contains (substring match). Matches are always case
   * sensitive unless the ignoreCase is set. Limited to 10 paths per
   * Authorization Policy. Note that this path match includes the query
   * parameters. For gRPC services, this should be a fully-qualified name of the
   * form /package.service/method.
   *
   * @param AuthzPolicyAuthzRuleStringMatch[] $paths
   */
  public function setPaths($paths)
  {
    $this->paths = $paths;
  }
  /**
   * @return AuthzPolicyAuthzRuleStringMatch[]
   */
  public function getPaths()
  {
    return $this->paths;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthzPolicyAuthzRuleToRequestOperation::class, 'Google_Service_NetworkSecurity_AuthzPolicyAuthzRuleToRequestOperation');
