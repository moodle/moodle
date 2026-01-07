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

namespace Google\Service\ServiceUsage;

class ContentSecurityPolicy extends \Google\Model
{
  protected $mcpContentSecurityType = ContentSecurity::class;
  protected $mcpContentSecurityDataType = '';
  /**
   * Output only. The resource name of the policy. Only the `default` policy is
   * supported. We allow the following formats:
   * `projects/{PROJECT_NUMBER}/contentSecurityPolicies/default`,
   * `projects/{PROJECT_ID}/contentSecurityPolicies/default`, We only support
   * project level content security policy for now.
   *
   * @var string
   */
  public $name;

  /**
   * mcp_content_security contains the content security related settings at
   * resource level for MCP traffic.
   *
   * @param ContentSecurity $mcpContentSecurity
   */
  public function setMcpContentSecurity(ContentSecurity $mcpContentSecurity)
  {
    $this->mcpContentSecurity = $mcpContentSecurity;
  }
  /**
   * @return ContentSecurity
   */
  public function getMcpContentSecurity()
  {
    return $this->mcpContentSecurity;
  }
  /**
   * Output only. The resource name of the policy. Only the `default` policy is
   * supported. We allow the following formats:
   * `projects/{PROJECT_NUMBER}/contentSecurityPolicies/default`,
   * `projects/{PROJECT_ID}/contentSecurityPolicies/default`, We only support
   * project level content security policy for now.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContentSecurityPolicy::class, 'Google_Service_ServiceUsage_ContentSecurityPolicy');
