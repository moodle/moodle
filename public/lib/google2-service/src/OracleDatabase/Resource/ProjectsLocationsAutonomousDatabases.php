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

namespace Google\Service\OracleDatabase\Resource;

use Google\Service\OracleDatabase\AutonomousDatabase;
use Google\Service\OracleDatabase\FailoverAutonomousDatabaseRequest;
use Google\Service\OracleDatabase\GenerateAutonomousDatabaseWalletRequest;
use Google\Service\OracleDatabase\GenerateAutonomousDatabaseWalletResponse;
use Google\Service\OracleDatabase\ListAutonomousDatabasesResponse;
use Google\Service\OracleDatabase\Operation;
use Google\Service\OracleDatabase\RestartAutonomousDatabaseRequest;
use Google\Service\OracleDatabase\RestoreAutonomousDatabaseRequest;
use Google\Service\OracleDatabase\StartAutonomousDatabaseRequest;
use Google\Service\OracleDatabase\StopAutonomousDatabaseRequest;
use Google\Service\OracleDatabase\SwitchoverAutonomousDatabaseRequest;

/**
 * The "autonomousDatabases" collection of methods.
 * Typical usage is:
 *  <code>
 *   $oracledatabaseService = new Google\Service\OracleDatabase(...);
 *   $autonomousDatabases = $oracledatabaseService->projects_locations_autonomousDatabases;
 *  </code>
 */
class ProjectsLocationsAutonomousDatabases extends \Google\Service\Resource
{
  /**
   * Creates a new Autonomous Database in a given project and location.
   * (autonomousDatabases.create)
   *
   * @param string $parent Required. The name of the parent in the following
   * format: projects/{project}/locations/{location}.
   * @param AutonomousDatabase $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string autonomousDatabaseId Required. The ID of the Autonomous
   * Database to create. This value is restricted to
   * (^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$) and must be a maximum of 63 characters in
   * length. The value must start with a letter and end with a letter or a number.
   * @opt_param string requestId Optional. An optional ID to identify the request.
   * This value is used to identify duplicate requests. If you make a request with
   * the same request ID and the original request is still in progress or
   * completed, the server ignores the second request. This prevents clients from
   * accidentally creating duplicate commitments. The request ID must be a valid
   * UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, AutonomousDatabase $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single Autonomous Database. (autonomousDatabases.delete)
   *
   * @param string $name Required. The name of the resource in the following
   * format: projects/{project}/locations/{location}/autonomousDatabases/{autonomo
   * us_database}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional ID to identify the request.
   * This value is used to identify duplicate requests. If you make a request with
   * the same request ID and the original request is still in progress or
   * completed, the server ignores the second request. This prevents clients from
   * accidentally creating duplicate commitments. The request ID must be a valid
   * UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
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
   * Initiates a failover to target autonomous database from the associated
   * primary database. (autonomousDatabases.failover)
   *
   * @param string $name Required. The name of the Autonomous Database in the
   * following format: projects/{project}/locations/{location}/autonomousDatabases
   * /{autonomous_database}.
   * @param FailoverAutonomousDatabaseRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function failover($name, FailoverAutonomousDatabaseRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('failover', [$params], Operation::class);
  }
  /**
   * Generates a wallet for an Autonomous Database.
   * (autonomousDatabases.generateWallet)
   *
   * @param string $name Required. The name of the Autonomous Database in the
   * following format: projects/{project}/locations/{location}/autonomousDatabases
   * /{autonomous_database}.
   * @param GenerateAutonomousDatabaseWalletRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GenerateAutonomousDatabaseWalletResponse
   * @throws \Google\Service\Exception
   */
  public function generateWallet($name, GenerateAutonomousDatabaseWalletRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('generateWallet', [$params], GenerateAutonomousDatabaseWalletResponse::class);
  }
  /**
   * Gets the details of a single Autonomous Database. (autonomousDatabases.get)
   *
   * @param string $name Required. The name of the Autonomous Database in the
   * following format: projects/{project}/locations/{location}/autonomousDatabases
   * /{autonomous_database}.
   * @param array $optParams Optional parameters.
   * @return AutonomousDatabase
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AutonomousDatabase::class);
  }
  /**
   * Lists the Autonomous Databases in a given project and location.
   * (autonomousDatabases.listProjectsLocationsAutonomousDatabases)
   *
   * @param string $parent Required. The parent value for the Autonomous Database
   * in the following format: projects/{project}/locations/{location}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression for filtering the results of
   * the request.
   * @opt_param string orderBy Optional. An expression for ordering the results of
   * the request.
   * @opt_param int pageSize Optional. The maximum number of items to return. If
   * unspecified, at most 50 Autonomous Database will be returned. The maximum
   * value is 1000; values above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListAutonomousDatabasesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAutonomousDatabases($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAutonomousDatabasesResponse::class);
  }
  /**
   * Updates the parameters of a single Autonomous Database.
   * (autonomousDatabases.patch)
   *
   * @param string $name Identifier. The name of the Autonomous Database resource
   * in the following format: projects/{project}/locations/{region}/autonomousData
   * bases/{autonomous_database}
   * @param AutonomousDatabase $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional ID to identify the request.
   * This value is used to identify duplicate requests. If you make a request with
   * the same request ID and the original request is still in progress or
   * completed, the server ignores the second request. This prevents clients from
   * accidentally creating duplicate commitments. The request ID must be a valid
   * UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the Exadata resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, AutonomousDatabase $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Restarts an Autonomous Database. (autonomousDatabases.restart)
   *
   * @param string $name Required. The name of the Autonomous Database in the
   * following format: projects/{project}/locations/{location}/autonomousDatabases
   * /{autonomous_database}.
   * @param RestartAutonomousDatabaseRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function restart($name, RestartAutonomousDatabaseRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restart', [$params], Operation::class);
  }
  /**
   * Restores a single Autonomous Database. (autonomousDatabases.restore)
   *
   * @param string $name Required. The name of the Autonomous Database in the
   * following format: projects/{project}/locations/{location}/autonomousDatabases
   * /{autonomous_database}.
   * @param RestoreAutonomousDatabaseRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function restore($name, RestoreAutonomousDatabaseRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restore', [$params], Operation::class);
  }
  /**
   * Starts an Autonomous Database. (autonomousDatabases.start)
   *
   * @param string $name Required. The name of the Autonomous Database in the
   * following format: projects/{project}/locations/{location}/autonomousDatabases
   * /{autonomous_database}.
   * @param StartAutonomousDatabaseRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function start($name, StartAutonomousDatabaseRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('start', [$params], Operation::class);
  }
  /**
   * Stops an Autonomous Database. (autonomousDatabases.stop)
   *
   * @param string $name Required. The name of the Autonomous Database in the
   * following format: projects/{project}/locations/{location}/autonomousDatabases
   * /{autonomous_database}.
   * @param StopAutonomousDatabaseRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function stop($name, StopAutonomousDatabaseRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('stop', [$params], Operation::class);
  }
  /**
   * Initiates a switchover of specified autonomous database to the associated
   * peer database. (autonomousDatabases.switchover)
   *
   * @param string $name Required. The name of the Autonomous Database in the
   * following format: projects/{project}/locations/{location}/autonomousDatabases
   * /{autonomous_database}.
   * @param SwitchoverAutonomousDatabaseRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function switchover($name, SwitchoverAutonomousDatabaseRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('switchover', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAutonomousDatabases::class, 'Google_Service_OracleDatabase_Resource_ProjectsLocationsAutonomousDatabases');
