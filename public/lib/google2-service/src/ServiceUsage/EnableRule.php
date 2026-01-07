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

class EnableRule extends \Google\Collection
{
  /**
   * Unspecified enable type, which means enabled as both client and resource
   * project.
   */
  public const ENABLE_TYPE_ENABLE_TYPE_UNSPECIFIED = 'ENABLE_TYPE_UNSPECIFIED';
  /**
   * Enable all clients under the CRM node specified by `ConsumerPolicy.name` to
   * use the listed services. A client can be an API key, an OAuth client, or a
   * service account.
   */
  public const ENABLE_TYPE_CLIENT = 'CLIENT';
  /**
   * Enable resources in the list services to be created and used under the CRM
   * node specified by the `ConsumerPolicy.name`.
   */
  public const ENABLE_TYPE_RESOURCE = 'RESOURCE';
  /**
   * Activation made by Service Usage v1 API. This will be how consumers
   * differentiate between policy changes made by v1 and v2 clients and
   * understand what is actually possible based on those different policies.
   */
  public const ENABLE_TYPE_V1_COMPATIBLE = 'V1_COMPATIBLE';
  protected $collection_key = 'values';
  /**
   * Client and resource project enable type.
   *
   * @var string
   */
  public $enableType;
  /**
   * DEPRECATED: Please use field `values`. Service group should have prefix
   * `groups/`. The names of the service groups that are enabled (Not
   * Implemented). Example: `groups/googleServices`.
   *
   * @deprecated
   * @var string[]
   */
  public $groups;
  /**
   * DEPRECATED: Please use field `values`. Service should have prefix
   * `services/`. The names of the services that are enabled. Example:
   * `storage.googleapis.com`.
   *
   * @deprecated
   * @var string[]
   */
  public $services;
  /**
   * The names of the services or service groups that are enabled. Example:
   * `services/storage.googleapis.com`, `groups/googleServices`,
   * `groups/allServices`.
   *
   * @var string[]
   */
  public $values;

  /**
   * Client and resource project enable type.
   *
   * Accepted values: ENABLE_TYPE_UNSPECIFIED, CLIENT, RESOURCE, V1_COMPATIBLE
   *
   * @param self::ENABLE_TYPE_* $enableType
   */
  public function setEnableType($enableType)
  {
    $this->enableType = $enableType;
  }
  /**
   * @return self::ENABLE_TYPE_*
   */
  public function getEnableType()
  {
    return $this->enableType;
  }
  /**
   * DEPRECATED: Please use field `values`. Service group should have prefix
   * `groups/`. The names of the service groups that are enabled (Not
   * Implemented). Example: `groups/googleServices`.
   *
   * @deprecated
   * @param string[] $groups
   */
  public function setGroups($groups)
  {
    $this->groups = $groups;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getGroups()
  {
    return $this->groups;
  }
  /**
   * DEPRECATED: Please use field `values`. Service should have prefix
   * `services/`. The names of the services that are enabled. Example:
   * `storage.googleapis.com`.
   *
   * @deprecated
   * @param string[] $services
   */
  public function setServices($services)
  {
    $this->services = $services;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getServices()
  {
    return $this->services;
  }
  /**
   * The names of the services or service groups that are enabled. Example:
   * `services/storage.googleapis.com`, `groups/googleServices`,
   * `groups/allServices`.
   *
   * @param string[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return string[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnableRule::class, 'Google_Service_ServiceUsage_EnableRule');
