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

namespace Google\Service\CloudKMS\Resource;

use Google\Service\CloudKMS\KeyAccessJustificationsPolicyConfig;
use Google\Service\CloudKMS\ShowEffectiveAutokeyConfigResponse;
use Google\Service\CloudKMS\ShowEffectiveKeyAccessJustificationsEnrollmentConfigResponse;
use Google\Service\CloudKMS\ShowEffectiveKeyAccessJustificationsPolicyConfigResponse;

/**
 * The "projects" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudkmsService = new Google\Service\CloudKMS(...);
 *   $projects = $cloudkmsService->projects;
 *  </code>
 */
class Projects extends \Google\Service\Resource
{
  /**
   * Gets the KeyAccessJustificationsPolicyConfig for a given organization,
   * folder, or project. (projects.getKajPolicyConfig)
   *
   * @param string $name Required. The name of the
   * KeyAccessJustificationsPolicyConfig to get.
   * @param array $optParams Optional parameters.
   * @return KeyAccessJustificationsPolicyConfig
   * @throws \Google\Service\Exception
   */
  public function getKajPolicyConfig($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getKajPolicyConfig', [$params], KeyAccessJustificationsPolicyConfig::class);
  }
  /**
   * Returns the effective Cloud KMS Autokey configuration for a given project.
   * (projects.showEffectiveAutokeyConfig)
   *
   * @param string $parent Required. Name of the resource project to the show
   * effective Cloud KMS Autokey configuration for. This may be helpful for
   * interrogating the effect of nested folder configurations on a given resource
   * project.
   * @param array $optParams Optional parameters.
   * @return ShowEffectiveAutokeyConfigResponse
   * @throws \Google\Service\Exception
   */
  public function showEffectiveAutokeyConfig($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('showEffectiveAutokeyConfig', [$params], ShowEffectiveAutokeyConfigResponse::class);
  }
  /**
   * Returns the KeyAccessJustificationsEnrollmentConfig of the resource closest
   * to the given project in hierarchy.
   * (projects.showEffectiveKeyAccessJustificationsEnrollmentConfig)
   *
   * @param string $project Required. The number or id of the project to get the
   * effective KeyAccessJustificationsEnrollmentConfig for.
   * @param array $optParams Optional parameters.
   * @return ShowEffectiveKeyAccessJustificationsEnrollmentConfigResponse
   * @throws \Google\Service\Exception
   */
  public function showEffectiveKeyAccessJustificationsEnrollmentConfig($project, $optParams = [])
  {
    $params = ['project' => $project];
    $params = array_merge($params, $optParams);
    return $this->call('showEffectiveKeyAccessJustificationsEnrollmentConfig', [$params], ShowEffectiveKeyAccessJustificationsEnrollmentConfigResponse::class);
  }
  /**
   * Returns the KeyAccessJustificationsPolicyConfig of the resource closest to
   * the given project in hierarchy.
   * (projects.showEffectiveKeyAccessJustificationsPolicyConfig)
   *
   * @param string $project Required. The number or id of the project to get the
   * effective KeyAccessJustificationsPolicyConfig. In the format of
   * "projects/{|}"
   * @param array $optParams Optional parameters.
   * @return ShowEffectiveKeyAccessJustificationsPolicyConfigResponse
   * @throws \Google\Service\Exception
   */
  public function showEffectiveKeyAccessJustificationsPolicyConfig($project, $optParams = [])
  {
    $params = ['project' => $project];
    $params = array_merge($params, $optParams);
    return $this->call('showEffectiveKeyAccessJustificationsPolicyConfig', [$params], ShowEffectiveKeyAccessJustificationsPolicyConfigResponse::class);
  }
  /**
   * Updates the KeyAccessJustificationsPolicyConfig for a given organization,
   * folder, or project. (projects.updateKajPolicyConfig)
   *
   * @param string $name Identifier. The resource name for this
   * KeyAccessJustificationsPolicyConfig in the format of
   * "{organizations|folders|projects}/kajPolicyConfig".
   * @param KeyAccessJustificationsPolicyConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update.
   * @return KeyAccessJustificationsPolicyConfig
   * @throws \Google\Service\Exception
   */
  public function updateKajPolicyConfig($name, KeyAccessJustificationsPolicyConfig $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateKajPolicyConfig', [$params], KeyAccessJustificationsPolicyConfig::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Projects::class, 'Google_Service_CloudKMS_Resource_Projects');
