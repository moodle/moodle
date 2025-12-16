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

class GoogleCloudApigeeV1SyncAuthorization extends \Google\Collection
{
  protected $collection_key = 'identities';
  /**
   * Entity tag (ETag) used for optimistic concurrency control as a way to help
   * prevent simultaneous updates from overwriting each other. For example, when
   * you call [getSyncAuthorization](organizations/getSyncAuthorization) an ETag
   * is returned in the response. Pass that ETag when calling the
   * [setSyncAuthorization](organizations/setSyncAuthorization) to ensure that
   * you are updating the correct version. If you don't pass the ETag in the
   * call to `setSyncAuthorization`, then the existing authorization is
   * overwritten indiscriminately. **Note**: We strongly recommend that you use
   * the ETag in the read-modify-write cycle to avoid race conditions.
   *
   * @var string
   */
  public $etag;
  /**
   * Required. Array of service accounts to grant access to control plane
   * resources, each specified using the following format: `serviceAccount:`
   * service-account-name. The service-account-name is formatted like an email
   * address. For example: `my-synchronizer-manager-
   * service_account@my_project_id.iam.gserviceaccount.com` You might specify
   * multiple service accounts, for example, if you have multiple environments
   * and wish to assign a unique service account to each one. The service
   * accounts must have **Apigee Synchronizer Manager** role. See also [Create
   * service accounts](https://cloud.google.com/apigee/docs/hybrid/latest/sa-
   * about#create-the-service-accounts).
   *
   * @var string[]
   */
  public $identities;

  /**
   * Entity tag (ETag) used for optimistic concurrency control as a way to help
   * prevent simultaneous updates from overwriting each other. For example, when
   * you call [getSyncAuthorization](organizations/getSyncAuthorization) an ETag
   * is returned in the response. Pass that ETag when calling the
   * [setSyncAuthorization](organizations/setSyncAuthorization) to ensure that
   * you are updating the correct version. If you don't pass the ETag in the
   * call to `setSyncAuthorization`, then the existing authorization is
   * overwritten indiscriminately. **Note**: We strongly recommend that you use
   * the ETag in the read-modify-write cycle to avoid race conditions.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Required. Array of service accounts to grant access to control plane
   * resources, each specified using the following format: `serviceAccount:`
   * service-account-name. The service-account-name is formatted like an email
   * address. For example: `my-synchronizer-manager-
   * service_account@my_project_id.iam.gserviceaccount.com` You might specify
   * multiple service accounts, for example, if you have multiple environments
   * and wish to assign a unique service account to each one. The service
   * accounts must have **Apigee Synchronizer Manager** role. See also [Create
   * service accounts](https://cloud.google.com/apigee/docs/hybrid/latest/sa-
   * about#create-the-service-accounts).
   *
   * @param string[] $identities
   */
  public function setIdentities($identities)
  {
    $this->identities = $identities;
  }
  /**
   * @return string[]
   */
  public function getIdentities()
  {
    return $this->identities;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SyncAuthorization::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SyncAuthorization');
