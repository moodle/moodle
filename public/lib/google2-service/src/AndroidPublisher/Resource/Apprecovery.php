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

namespace Google\Service\AndroidPublisher\Resource;

use Google\Service\AndroidPublisher\AddTargetingRequest;
use Google\Service\AndroidPublisher\AddTargetingResponse;
use Google\Service\AndroidPublisher\AppRecoveryAction;
use Google\Service\AndroidPublisher\CancelAppRecoveryRequest;
use Google\Service\AndroidPublisher\CancelAppRecoveryResponse;
use Google\Service\AndroidPublisher\CreateDraftAppRecoveryRequest;
use Google\Service\AndroidPublisher\DeployAppRecoveryRequest;
use Google\Service\AndroidPublisher\DeployAppRecoveryResponse;
use Google\Service\AndroidPublisher\ListAppRecoveriesResponse;

/**
 * The "apprecovery" collection of methods.
 * Typical usage is:
 *  <code>
 *   $androidpublisherService = new Google\Service\AndroidPublisher(...);
 *   $apprecovery = $androidpublisherService->apprecovery;
 *  </code>
 */
class Apprecovery extends \Google\Service\Resource
{
  /**
   * Incrementally update targeting for a recovery action. Note that only the
   * criteria selected during the creation of recovery action can be expanded.
   * (apprecovery.addTargeting)
   *
   * @param string $packageName Required. Package name of the app for which
   * recovery action is to be updated.
   * @param string $appRecoveryId Required. ID corresponding to the app recovery
   * action.
   * @param AddTargetingRequest $postBody
   * @param array $optParams Optional parameters.
   * @return AddTargetingResponse
   * @throws \Google\Service\Exception
   */
  public function addTargeting($packageName, $appRecoveryId, AddTargetingRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'appRecoveryId' => $appRecoveryId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('addTargeting', [$params], AddTargetingResponse::class);
  }
  /**
   * Cancel an already executing app recovery action. Note that this action
   * changes status of the recovery action to CANCELED. (apprecovery.cancel)
   *
   * @param string $packageName Required. Package name of the app for which
   * recovery action cancellation is requested.
   * @param string $appRecoveryId Required. ID corresponding to the app recovery
   * action.
   * @param CancelAppRecoveryRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CancelAppRecoveryResponse
   * @throws \Google\Service\Exception
   */
  public function cancel($packageName, $appRecoveryId, CancelAppRecoveryRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'appRecoveryId' => $appRecoveryId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], CancelAppRecoveryResponse::class);
  }
  /**
   * Create an app recovery action with recovery status as DRAFT. Note that this
   * action does not execute the recovery action. (apprecovery.create)
   *
   * @param string $packageName Required. Package name of the app on which
   * recovery action is performed.
   * @param CreateDraftAppRecoveryRequest $postBody
   * @param array $optParams Optional parameters.
   * @return AppRecoveryAction
   * @throws \Google\Service\Exception
   */
  public function create($packageName, CreateDraftAppRecoveryRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], AppRecoveryAction::class);
  }
  /**
   * Deploy an already created app recovery action with recovery status DRAFT.
   * Note that this action activates the recovery action for all targeted users
   * and changes its status to ACTIVE. (apprecovery.deploy)
   *
   * @param string $packageName Required. Package name of the app for which
   * recovery action is deployed.
   * @param string $appRecoveryId Required. ID corresponding to the app recovery
   * action to deploy.
   * @param DeployAppRecoveryRequest $postBody
   * @param array $optParams Optional parameters.
   * @return DeployAppRecoveryResponse
   * @throws \Google\Service\Exception
   */
  public function deploy($packageName, $appRecoveryId, DeployAppRecoveryRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'appRecoveryId' => $appRecoveryId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('deploy', [$params], DeployAppRecoveryResponse::class);
  }
  /**
   * List all app recovery action resources associated with a particular package
   * name and app version. (apprecovery.listApprecovery)
   *
   * @param string $packageName Required. Package name of the app for which list
   * of recovery actions is requested.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string versionCode Required. Version code targeted by the list of
   * recovery actions.
   * @return ListAppRecoveriesResponse
   * @throws \Google\Service\Exception
   */
  public function listApprecovery($packageName, $optParams = [])
  {
    $params = ['packageName' => $packageName];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAppRecoveriesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Apprecovery::class, 'Google_Service_AndroidPublisher_Resource_Apprecovery');
