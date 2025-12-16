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

namespace Google\Service\BigQueryConnectionService;

class SalesforceDataCloudProperties extends \Google\Model
{
  /**
   * Output only. A unique Google-owned and Google-generated service account
   * identity for the connection.
   *
   * @var string
   */
  public $identity;
  /**
   * The URL to the user's Salesforce DataCloud instance.
   *
   * @var string
   */
  public $instanceUri;
  /**
   * The ID of the user's Salesforce tenant.
   *
   * @var string
   */
  public $tenantId;

  /**
   * Output only. A unique Google-owned and Google-generated service account
   * identity for the connection.
   *
   * @param string $identity
   */
  public function setIdentity($identity)
  {
    $this->identity = $identity;
  }
  /**
   * @return string
   */
  public function getIdentity()
  {
    return $this->identity;
  }
  /**
   * The URL to the user's Salesforce DataCloud instance.
   *
   * @param string $instanceUri
   */
  public function setInstanceUri($instanceUri)
  {
    $this->instanceUri = $instanceUri;
  }
  /**
   * @return string
   */
  public function getInstanceUri()
  {
    return $this->instanceUri;
  }
  /**
   * The ID of the user's Salesforce tenant.
   *
   * @param string $tenantId
   */
  public function setTenantId($tenantId)
  {
    $this->tenantId = $tenantId;
  }
  /**
   * @return string
   */
  public function getTenantId()
  {
    return $this->tenantId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SalesforceDataCloudProperties::class, 'Google_Service_BigQueryConnectionService_SalesforceDataCloudProperties');
