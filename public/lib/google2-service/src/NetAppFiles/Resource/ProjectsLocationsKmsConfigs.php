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

namespace Google\Service\NetAppFiles\Resource;

use Google\Service\NetAppFiles\EncryptVolumesRequest;
use Google\Service\NetAppFiles\KmsConfig;
use Google\Service\NetAppFiles\ListKmsConfigsResponse;
use Google\Service\NetAppFiles\Operation;
use Google\Service\NetAppFiles\VerifyKmsConfigRequest;
use Google\Service\NetAppFiles\VerifyKmsConfigResponse;

/**
 * The "kmsConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $netappService = new Google\Service\NetAppFiles(...);
 *   $kmsConfigs = $netappService->projects_locations_kmsConfigs;
 *  </code>
 */
class ProjectsLocationsKmsConfigs extends \Google\Service\Resource
{
  /**
   * Creates a new KMS config. (kmsConfigs.create)
   *
   * @param string $parent Required. Value for parent.
   * @param KmsConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string kmsConfigId Required. Id of the requesting KmsConfig. Must
   * be unique within the parent resource. Must contain only letters, numbers and
   * hyphen, with the first character a letter, the last a letter or a number, and
   * a 63 character maximum.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, KmsConfig $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Warning! This operation will permanently delete the Kms config.
   * (kmsConfigs.delete)
   *
   * @param string $name Required. Name of the KmsConfig.
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Encrypt the existing volumes without CMEK encryption with the desired the KMS
   * config for the whole region. (kmsConfigs.encrypt)
   *
   * @param string $name Required. Name of the KmsConfig.
   * @param EncryptVolumesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function encrypt($name, EncryptVolumesRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('encrypt', [$params], Operation::class);
  }
  /**
   * Returns the description of the specified KMS config by kms_config_id.
   * (kmsConfigs.get)
   *
   * @param string $name Required. Name of the KmsConfig
   * @param array $optParams Optional parameters.
   * @return KmsConfig
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], KmsConfig::class);
  }
  /**
   * Returns descriptions of all KMS configs owned by the caller.
   * (kmsConfigs.listProjectsLocationsKmsConfigs)
   *
   * @param string $parent Required. Parent value
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter List filter.
   * @opt_param string orderBy Sort results. Supported values are "name", "name
   * desc" or "" (unsorted).
   * @opt_param int pageSize The maximum number of items to return.
   * @opt_param string pageToken The next_page_token value to use if there are
   * additional results to retrieve for this list request.
   * @return ListKmsConfigsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsKmsConfigs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListKmsConfigsResponse::class);
  }
  /**
   * Updates the Kms config properties with the full spec (kmsConfigs.patch)
   *
   * @param string $name Identifier. Name of the KmsConfig.
   * @param KmsConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the KmsConfig resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, KmsConfig $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Verifies KMS config reachability. (kmsConfigs.verify)
   *
   * @param string $name Required. Name of the KMS Config to be verified.
   * @param VerifyKmsConfigRequest $postBody
   * @param array $optParams Optional parameters.
   * @return VerifyKmsConfigResponse
   * @throws \Google\Service\Exception
   */
  public function verify($name, VerifyKmsConfigRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('verify', [$params], VerifyKmsConfigResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsKmsConfigs::class, 'Google_Service_NetAppFiles_Resource_ProjectsLocationsKmsConfigs');
