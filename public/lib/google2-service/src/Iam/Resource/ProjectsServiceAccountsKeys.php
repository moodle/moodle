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

namespace Google\Service\Iam\Resource;

use Google\Service\Iam\CreateServiceAccountKeyRequest;
use Google\Service\Iam\DisableServiceAccountKeyRequest;
use Google\Service\Iam\EnableServiceAccountKeyRequest;
use Google\Service\Iam\IamEmpty;
use Google\Service\Iam\ListServiceAccountKeysResponse;
use Google\Service\Iam\ServiceAccountKey;
use Google\Service\Iam\UploadServiceAccountKeyRequest;

/**
 * The "keys" collection of methods.
 * Typical usage is:
 *  <code>
 *   $iamService = new Google\Service\Iam(...);
 *   $keys = $iamService->projects_serviceAccounts_keys;
 *  </code>
 */
class ProjectsServiceAccountsKeys extends \Google\Service\Resource
{
  /**
   * Creates a ServiceAccountKey. (keys.create)
   *
   * @param string $name Required. The resource name of the service account. Use
   * one of the following formats: *
   * `projects/{PROJECT_ID}/serviceAccounts/{EMAIL_ADDRESS}` *
   * `projects/{PROJECT_ID}/serviceAccounts/{UNIQUE_ID}` As an alternative, you
   * can use the `-` wildcard character instead of the project ID: *
   * `projects/-/serviceAccounts/{EMAIL_ADDRESS}` *
   * `projects/-/serviceAccounts/{UNIQUE_ID}` When possible, avoid using the `-`
   * wildcard character, because it can cause response messages to contain
   * misleading error codes. For example, if you try to access the service account
   * `projects/-/serviceAccounts/fake@example.com`, which does not exist, the
   * response contains an HTTP `403 Forbidden` error instead of a `404 Not Found`
   * error.
   * @param CreateServiceAccountKeyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ServiceAccountKey
   */
  public function create($name, CreateServiceAccountKeyRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], ServiceAccountKey::class);
  }
  /**
   * Deletes a ServiceAccountKey. Deleting a service account key does not revoke
   * short-lived credentials that have been issued based on the service account
   * key. (keys.delete)
   *
   * @param string $name Required. The resource name of the service account key.
   * Use one of the following formats: *
   * `projects/{PROJECT_ID}/serviceAccounts/{EMAIL_ADDRESS}/keys/{KEY_ID}` *
   * `projects/{PROJECT_ID}/serviceAccounts/{UNIQUE_ID}/keys/{KEY_ID}` As an
   * alternative, you can use the `-` wildcard character instead of the project
   * ID: * `projects/-/serviceAccounts/{EMAIL_ADDRESS}/keys/{KEY_ID}` *
   * `projects/-/serviceAccounts/{UNIQUE_ID}/keys/{KEY_ID}` When possible, avoid
   * using the `-` wildcard character, because it can cause response messages to
   * contain misleading error codes. For example, if you try to access the service
   * account key `projects/-/serviceAccounts/fake@example.com/keys/fake-key`,
   * which does not exist, the response contains an HTTP `403 Forbidden` error
   * instead of a `404 Not Found` error.
   * @param array $optParams Optional parameters.
   * @return IamEmpty
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], IamEmpty::class);
  }
  /**
   * Disable a ServiceAccountKey. A disabled service account key can be re-enabled
   * with EnableServiceAccountKey. (keys.disable)
   *
   * @param string $name Required. The resource name of the service account key.
   * Use one of the following formats: *
   * `projects/{PROJECT_ID}/serviceAccounts/{EMAIL_ADDRESS}/keys/{KEY_ID}` *
   * `projects/{PROJECT_ID}/serviceAccounts/{UNIQUE_ID}/keys/{KEY_ID}` As an
   * alternative, you can use the `-` wildcard character instead of the project
   * ID: * `projects/-/serviceAccounts/{EMAIL_ADDRESS}/keys/{KEY_ID}` *
   * `projects/-/serviceAccounts/{UNIQUE_ID}/keys/{KEY_ID}` When possible, avoid
   * using the `-` wildcard character, because it can cause response messages to
   * contain misleading error codes. For example, if you try to access the service
   * account key `projects/-/serviceAccounts/fake@example.com/keys/fake-key`,
   * which does not exist, the response contains an HTTP `403 Forbidden` error
   * instead of a `404 Not Found` error.
   * @param DisableServiceAccountKeyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return IamEmpty
   */
  public function disable($name, DisableServiceAccountKeyRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('disable', [$params], IamEmpty::class);
  }
  /**
   * Enable a ServiceAccountKey. (keys.enable)
   *
   * @param string $name Required. The resource name of the service account key.
   * Use one of the following formats: *
   * `projects/{PROJECT_ID}/serviceAccounts/{EMAIL_ADDRESS}/keys/{KEY_ID}` *
   * `projects/{PROJECT_ID}/serviceAccounts/{UNIQUE_ID}/keys/{KEY_ID}` As an
   * alternative, you can use the `-` wildcard character instead of the project
   * ID: * `projects/-/serviceAccounts/{EMAIL_ADDRESS}/keys/{KEY_ID}` *
   * `projects/-/serviceAccounts/{UNIQUE_ID}/keys/{KEY_ID}` When possible, avoid
   * using the `-` wildcard character, because it can cause response messages to
   * contain misleading error codes. For example, if you try to access the service
   * account key `projects/-/serviceAccounts/fake@example.com/keys/fake-key`,
   * which does not exist, the response contains an HTTP `403 Forbidden` error
   * instead of a `404 Not Found` error.
   * @param EnableServiceAccountKeyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return IamEmpty
   */
  public function enable($name, EnableServiceAccountKeyRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('enable', [$params], IamEmpty::class);
  }
  /**
   * Gets a ServiceAccountKey. (keys.get)
   *
   * @param string $name Required. The resource name of the service account key.
   * Use one of the following formats: *
   * `projects/{PROJECT_ID}/serviceAccounts/{EMAIL_ADDRESS}/keys/{KEY_ID}` *
   * `projects/{PROJECT_ID}/serviceAccounts/{UNIQUE_ID}/keys/{KEY_ID}` As an
   * alternative, you can use the `-` wildcard character instead of the project
   * ID: * `projects/-/serviceAccounts/{EMAIL_ADDRESS}/keys/{KEY_ID}` *
   * `projects/-/serviceAccounts/{UNIQUE_ID}/keys/{KEY_ID}` When possible, avoid
   * using the `-` wildcard character, because it can cause response messages to
   * contain misleading error codes. For example, if you try to access the service
   * account key `projects/-/serviceAccounts/fake@example.com/keys/fake-key`,
   * which does not exist, the response contains an HTTP `403 Forbidden` error
   * instead of a `404 Not Found` error.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string publicKeyType Optional. The output format of the public
   * key. The default is `TYPE_NONE`, which means that the public key is not
   * returned.
   * @return ServiceAccountKey
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ServiceAccountKey::class);
  }
  /**
   * Lists every ServiceAccountKey for a service account.
   * (keys.listProjectsServiceAccountsKeys)
   *
   * @param string $name Required. The resource name of the service account. Use
   * one of the following formats: *
   * `projects/{PROJECT_ID}/serviceAccounts/{EMAIL_ADDRESS}` *
   * `projects/{PROJECT_ID}/serviceAccounts/{UNIQUE_ID}` As an alternative, you
   * can use the `-` wildcard character instead of the project ID: *
   * `projects/-/serviceAccounts/{EMAIL_ADDRESS}` *
   * `projects/-/serviceAccounts/{UNIQUE_ID}` When possible, avoid using the `-`
   * wildcard character, because it can cause response messages to contain
   * misleading error codes. For example, if you try to access the service account
   * `projects/-/serviceAccounts/fake@example.com`, which does not exist, the
   * response contains an HTTP `403 Forbidden` error instead of a `404 Not Found`
   * error.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string keyTypes Filters the types of keys the user wants to
   * include in the list response. Duplicate key types are not allowed. If no key
   * type is provided, all keys are returned.
   * @return ListServiceAccountKeysResponse
   */
  public function listProjectsServiceAccountsKeys($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListServiceAccountKeysResponse::class);
  }
  /**
   * Uploads the public key portion of a key pair that you manage, and associates
   * the public key with a ServiceAccount. After you upload the public key, you
   * can use the private key from the key pair as a service account key.
   * (keys.upload)
   *
   * @param string $name The resource name of the service account key. Use one of
   * the following formats: *
   * `projects/{PROJECT_ID}/serviceAccounts/{EMAIL_ADDRESS}` *
   * `projects/{PROJECT_ID}/serviceAccounts/{UNIQUE_ID}` As an alternative, you
   * can use the `-` wildcard character instead of the project ID: *
   * `projects/-/serviceAccounts/{EMAIL_ADDRESS}` *
   * `projects/-/serviceAccounts/{UNIQUE_ID}` When possible, avoid using the `-`
   * wildcard character, because it can cause response messages to contain
   * misleading error codes. For example, if you try to access the service account
   * `projects/-/serviceAccounts/fake@example.com`, which does not exist, the
   * response contains an HTTP `403 Forbidden` error instead of a `404 Not Found`
   * error.
   * @param UploadServiceAccountKeyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ServiceAccountKey
   */
  public function upload($name, UploadServiceAccountKeyRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('upload', [$params], ServiceAccountKey::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsServiceAccountsKeys::class, 'Google_Service_Iam_Resource_ProjectsServiceAccountsKeys');
