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

namespace Google\Service\Contentwarehouse;

class CloudAiPlatformTenantresourceServiceAccountIdentity extends \Google\Model
{
  /**
   * Output only. The service account email that has been created.
   *
   * @var string
   */
  public $serviceAccountEmail;
  /**
   * Input/Output [Optional]. The tag that configures the service account, as
   * defined in google3/configs/production/cdpush/acl-zanzibar-cloud-
   * prod/activation_grants/activation_grants.gcl. Note: The default P4 service
   * account has the empty tag.
   *
   * @var string
   */
  public $tag;

  /**
   * Output only. The service account email that has been created.
   *
   * @param string $serviceAccountEmail
   */
  public function setServiceAccountEmail($serviceAccountEmail)
  {
    $this->serviceAccountEmail = $serviceAccountEmail;
  }
  /**
   * @return string
   */
  public function getServiceAccountEmail()
  {
    return $this->serviceAccountEmail;
  }
  /**
   * Input/Output [Optional]. The tag that configures the service account, as
   * defined in google3/configs/production/cdpush/acl-zanzibar-cloud-
   * prod/activation_grants/activation_grants.gcl. Note: The default P4 service
   * account has the empty tag.
   *
   * @param string $tag
   */
  public function setTag($tag)
  {
    $this->tag = $tag;
  }
  /**
   * @return string
   */
  public function getTag()
  {
    return $this->tag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiPlatformTenantresourceServiceAccountIdentity::class, 'Google_Service_Contentwarehouse_CloudAiPlatformTenantresourceServiceAccountIdentity');
