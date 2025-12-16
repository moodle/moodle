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

namespace Google\Service\RecaptchaEnterprise\Resource;

use Google\Service\RecaptchaEnterprise\GoogleCloudRecaptchaenterpriseV1AddIpOverrideRequest;
use Google\Service\RecaptchaEnterprise\GoogleCloudRecaptchaenterpriseV1AddIpOverrideResponse;
use Google\Service\RecaptchaEnterprise\GoogleCloudRecaptchaenterpriseV1Key;
use Google\Service\RecaptchaEnterprise\GoogleCloudRecaptchaenterpriseV1ListIpOverridesResponse;
use Google\Service\RecaptchaEnterprise\GoogleCloudRecaptchaenterpriseV1ListKeysResponse;
use Google\Service\RecaptchaEnterprise\GoogleCloudRecaptchaenterpriseV1Metrics;
use Google\Service\RecaptchaEnterprise\GoogleCloudRecaptchaenterpriseV1MigrateKeyRequest;
use Google\Service\RecaptchaEnterprise\GoogleCloudRecaptchaenterpriseV1RemoveIpOverrideRequest;
use Google\Service\RecaptchaEnterprise\GoogleCloudRecaptchaenterpriseV1RemoveIpOverrideResponse;
use Google\Service\RecaptchaEnterprise\GoogleCloudRecaptchaenterpriseV1RetrieveLegacySecretKeyResponse;
use Google\Service\RecaptchaEnterprise\GoogleProtobufEmpty;

/**
 * The "keys" collection of methods.
 * Typical usage is:
 *  <code>
 *   $recaptchaenterpriseService = new Google\Service\RecaptchaEnterprise(...);
 *   $keys = $recaptchaenterpriseService->projects_keys;
 *  </code>
 */
class ProjectsKeys extends \Google\Service\Resource
{
  /**
   * Adds an IP override to a key. The following restrictions hold: * The maximum
   * number of IP overrides per key is 1000. * For any conflict (such as IP
   * already exists or IP part of an existing IP range), an error is returned.
   * (keys.addIpOverride)
   *
   * @param string $name Required. The name of the key to which the IP override is
   * added, in the format `projects/{project}/keys/{key}`.
   * @param GoogleCloudRecaptchaenterpriseV1AddIpOverrideRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRecaptchaenterpriseV1AddIpOverrideResponse
   * @throws \Google\Service\Exception
   */
  public function addIpOverride($name, GoogleCloudRecaptchaenterpriseV1AddIpOverrideRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('addIpOverride', [$params], GoogleCloudRecaptchaenterpriseV1AddIpOverrideResponse::class);
  }
  /**
   * Creates a new reCAPTCHA Enterprise key. (keys.create)
   *
   * @param string $parent Required. The name of the project in which the key is
   * created, in the format `projects/{project}`.
   * @param GoogleCloudRecaptchaenterpriseV1Key $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRecaptchaenterpriseV1Key
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudRecaptchaenterpriseV1Key $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudRecaptchaenterpriseV1Key::class);
  }
  /**
   * Deletes the specified key. (keys.delete)
   *
   * @param string $name Required. The name of the key to be deleted, in the
   * format `projects/{project}/keys/{key}`.
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Returns the specified key. (keys.get)
   *
   * @param string $name Required. The name of the requested key, in the format
   * `projects/{project}/keys/{key}`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRecaptchaenterpriseV1Key
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudRecaptchaenterpriseV1Key::class);
  }
  /**
   * Get some aggregated metrics for a Key. This data can be used to build
   * dashboards. (keys.getMetrics)
   *
   * @param string $name Required. The name of the requested metrics, in the
   * format `projects/{project}/keys/{key}/metrics`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRecaptchaenterpriseV1Metrics
   * @throws \Google\Service\Exception
   */
  public function getMetrics($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getMetrics', [$params], GoogleCloudRecaptchaenterpriseV1Metrics::class);
  }
  /**
   * Returns the list of all keys that belong to a project.
   * (keys.listProjectsKeys)
   *
   * @param string $parent Required. The name of the project that contains the
   * keys that is listed, in the format `projects/{project}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of keys to return.
   * Default is 10. Max limit is 1000.
   * @opt_param string pageToken Optional. The next_page_token value returned from
   * a previous. ListKeysRequest, if any.
   * @return GoogleCloudRecaptchaenterpriseV1ListKeysResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsKeys($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudRecaptchaenterpriseV1ListKeysResponse::class);
  }
  /**
   * Lists all IP overrides for a key. (keys.listIpOverrides)
   *
   * @param string $parent Required. The parent key for which the IP overrides are
   * listed, in the format `projects/{project}/keys/{key}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of overrides to return.
   * Default is 10. Max limit is 100. If the number of overrides is less than the
   * page_size, all overrides are returned. If the page size is more than 100, it
   * is coerced to 100.
   * @opt_param string pageToken Optional. The next_page_token value returned from
   * a previous ListIpOverridesRequest, if any.
   * @return GoogleCloudRecaptchaenterpriseV1ListIpOverridesResponse
   * @throws \Google\Service\Exception
   */
  public function listIpOverrides($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('listIpOverrides', [$params], GoogleCloudRecaptchaenterpriseV1ListIpOverridesResponse::class);
  }
  /**
   * Migrates an existing key from reCAPTCHA to reCAPTCHA Enterprise. Once a key
   * is migrated, it can be used from either product. SiteVerify requests are
   * billed as CreateAssessment calls. You must be authenticated as one of the
   * current owners of the reCAPTCHA Key, and your user must have the reCAPTCHA
   * Enterprise Admin IAM role in the destination project. (keys.migrate)
   *
   * @param string $name Required. The name of the key to be migrated, in the
   * format `projects/{project}/keys/{key}`.
   * @param GoogleCloudRecaptchaenterpriseV1MigrateKeyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRecaptchaenterpriseV1Key
   * @throws \Google\Service\Exception
   */
  public function migrate($name, GoogleCloudRecaptchaenterpriseV1MigrateKeyRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('migrate', [$params], GoogleCloudRecaptchaenterpriseV1Key::class);
  }
  /**
   * Updates the specified key. (keys.patch)
   *
   * @param string $name Identifier. The resource name for the Key in the format
   * `projects/{project}/keys/{key}`.
   * @param GoogleCloudRecaptchaenterpriseV1Key $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The mask to control which fields of
   * the key get updated. If the mask is not present, all fields are updated.
   * @return GoogleCloudRecaptchaenterpriseV1Key
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudRecaptchaenterpriseV1Key $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudRecaptchaenterpriseV1Key::class);
  }
  /**
   * Removes an IP override from a key. The following restrictions hold: * If the
   * IP isn't found in an existing IP override, a `NOT_FOUND` error is returned. *
   * If the IP is found in an existing IP override, but the override type does not
   * match, a `NOT_FOUND` error is returned. (keys.removeIpOverride)
   *
   * @param string $name Required. The name of the key from which the IP override
   * is removed, in the format `projects/{project}/keys/{key}`.
   * @param GoogleCloudRecaptchaenterpriseV1RemoveIpOverrideRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRecaptchaenterpriseV1RemoveIpOverrideResponse
   * @throws \Google\Service\Exception
   */
  public function removeIpOverride($name, GoogleCloudRecaptchaenterpriseV1RemoveIpOverrideRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('removeIpOverride', [$params], GoogleCloudRecaptchaenterpriseV1RemoveIpOverrideResponse::class);
  }
  /**
   * Returns the secret key related to the specified public key. You must use the
   * legacy secret key only in a 3rd party integration with legacy reCAPTCHA.
   * (keys.retrieveLegacySecretKey)
   *
   * @param string $key Required. The public key name linked to the requested
   * secret key in the format `projects/{project}/keys/{key}`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRecaptchaenterpriseV1RetrieveLegacySecretKeyResponse
   * @throws \Google\Service\Exception
   */
  public function retrieveLegacySecretKey($key, $optParams = [])
  {
    $params = ['key' => $key];
    $params = array_merge($params, $optParams);
    return $this->call('retrieveLegacySecretKey', [$params], GoogleCloudRecaptchaenterpriseV1RetrieveLegacySecretKeyResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsKeys::class, 'Google_Service_RecaptchaEnterprise_Resource_ProjectsKeys');
