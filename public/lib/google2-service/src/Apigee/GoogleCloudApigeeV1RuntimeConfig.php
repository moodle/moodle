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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1RuntimeConfig extends \Google\Model
{
  /**
   * Cloud Storage bucket used for uploading Analytics records.
   *
   * @var string
   */
  public $analyticsBucket;
  /**
   * Name of the resource in the following format:
   * `organizations/{org}/runtimeConfig`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Tenant project ID associated with the Apigee organization. The
   * tenant project is used to host Google-managed resources that are dedicated
   * to this Apigee organization. Clients have limited access to resources
   * within the tenant project used to support Apigee runtime instances. Access
   * to the tenant project is managed using SetSyncAuthorization. It can be
   * empty if the tenant project hasn't been created yet.
   *
   * @var string
   */
  public $tenantProjectId;
  /**
   * Cloud Storage bucket used for uploading Trace records.
   *
   * @var string
   */
  public $traceBucket;

  /**
   * Cloud Storage bucket used for uploading Analytics records.
   *
   * @param string $analyticsBucket
   */
  public function setAnalyticsBucket($analyticsBucket)
  {
    $this->analyticsBucket = $analyticsBucket;
  }
  /**
   * @return string
   */
  public function getAnalyticsBucket()
  {
    return $this->analyticsBucket;
  }
  /**
   * Name of the resource in the following format:
   * `organizations/{org}/runtimeConfig`.
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
  /**
   * Output only. Tenant project ID associated with the Apigee organization. The
   * tenant project is used to host Google-managed resources that are dedicated
   * to this Apigee organization. Clients have limited access to resources
   * within the tenant project used to support Apigee runtime instances. Access
   * to the tenant project is managed using SetSyncAuthorization. It can be
   * empty if the tenant project hasn't been created yet.
   *
   * @param string $tenantProjectId
   */
  public function setTenantProjectId($tenantProjectId)
  {
    $this->tenantProjectId = $tenantProjectId;
  }
  /**
   * @return string
   */
  public function getTenantProjectId()
  {
    return $this->tenantProjectId;
  }
  /**
   * Cloud Storage bucket used for uploading Trace records.
   *
   * @param string $traceBucket
   */
  public function setTraceBucket($traceBucket)
  {
    $this->traceBucket = $traceBucket;
  }
  /**
   * @return string
   */
  public function getTraceBucket()
  {
    return $this->traceBucket;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1RuntimeConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1RuntimeConfig');
