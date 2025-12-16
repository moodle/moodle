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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1Curation extends \Google\Collection
{
  /**
   * Default unspecified error code.
   */
  public const LAST_EXECUTION_ERROR_CODE_ERROR_CODE_UNSPECIFIED = 'ERROR_CODE_UNSPECIFIED';
  /**
   * The execution failed due to an internal error.
   */
  public const LAST_EXECUTION_ERROR_CODE_INTERNAL_ERROR = 'INTERNAL_ERROR';
  /**
   * The curation is not authorized to trigger the endpoint uri.
   */
  public const LAST_EXECUTION_ERROR_CODE_UNAUTHORIZED = 'UNAUTHORIZED';
  /**
   * Default unspecified state.
   */
  public const LAST_EXECUTION_STATE_LAST_EXECUTION_STATE_UNSPECIFIED = 'LAST_EXECUTION_STATE_UNSPECIFIED';
  /**
   * The last curation execution was successful.
   */
  public const LAST_EXECUTION_STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The last curation execution failed.
   */
  public const LAST_EXECUTION_STATE_FAILED = 'FAILED';
  protected $collection_key = 'pluginInstanceActions';
  /**
   * Output only. The time at which the curation was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The description of the curation.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the curation.
   *
   * @var string
   */
  public $displayName;
  protected $endpointType = GoogleCloudApihubV1Endpoint::class;
  protected $endpointDataType = '';
  /**
   * Output only. The error code of the last execution of the curation. The
   * error code is populated only when the last execution state is failed.
   *
   * @var string
   */
  public $lastExecutionErrorCode;
  /**
   * Output only. Error message describing the failure, if any, during the last
   * execution of the curation.
   *
   * @var string
   */
  public $lastExecutionErrorMessage;
  /**
   * Output only. The last execution state of the curation.
   *
   * @var string
   */
  public $lastExecutionState;
  /**
   * Identifier. The name of the curation. Format:
   * `projects/{project}/locations/{location}/curations/{curation}`
   *
   * @var string
   */
  public $name;
  protected $pluginInstanceActionsType = GoogleCloudApihubV1PluginInstanceActionID::class;
  protected $pluginInstanceActionsDataType = 'array';
  /**
   * Output only. The time at which the curation was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time at which the curation was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. The description of the curation.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The display name of the curation.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Required. The endpoint to be triggered for curation.
   *
   * @param GoogleCloudApihubV1Endpoint $endpoint
   */
  public function setEndpoint(GoogleCloudApihubV1Endpoint $endpoint)
  {
    $this->endpoint = $endpoint;
  }
  /**
   * @return GoogleCloudApihubV1Endpoint
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
  /**
   * Output only. The error code of the last execution of the curation. The
   * error code is populated only when the last execution state is failed.
   *
   * Accepted values: ERROR_CODE_UNSPECIFIED, INTERNAL_ERROR, UNAUTHORIZED
   *
   * @param self::LAST_EXECUTION_ERROR_CODE_* $lastExecutionErrorCode
   */
  public function setLastExecutionErrorCode($lastExecutionErrorCode)
  {
    $this->lastExecutionErrorCode = $lastExecutionErrorCode;
  }
  /**
   * @return self::LAST_EXECUTION_ERROR_CODE_*
   */
  public function getLastExecutionErrorCode()
  {
    return $this->lastExecutionErrorCode;
  }
  /**
   * Output only. Error message describing the failure, if any, during the last
   * execution of the curation.
   *
   * @param string $lastExecutionErrorMessage
   */
  public function setLastExecutionErrorMessage($lastExecutionErrorMessage)
  {
    $this->lastExecutionErrorMessage = $lastExecutionErrorMessage;
  }
  /**
   * @return string
   */
  public function getLastExecutionErrorMessage()
  {
    return $this->lastExecutionErrorMessage;
  }
  /**
   * Output only. The last execution state of the curation.
   *
   * Accepted values: LAST_EXECUTION_STATE_UNSPECIFIED, SUCCEEDED, FAILED
   *
   * @param self::LAST_EXECUTION_STATE_* $lastExecutionState
   */
  public function setLastExecutionState($lastExecutionState)
  {
    $this->lastExecutionState = $lastExecutionState;
  }
  /**
   * @return self::LAST_EXECUTION_STATE_*
   */
  public function getLastExecutionState()
  {
    return $this->lastExecutionState;
  }
  /**
   * Identifier. The name of the curation. Format:
   * `projects/{project}/locations/{location}/curations/{curation}`
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
   * Output only. The plugin instances and associated actions that are using the
   * curation. Note: A particular curation could be used by multiple plugin
   * instances or multiple actions in a plugin instance.
   *
   * @param GoogleCloudApihubV1PluginInstanceActionID[] $pluginInstanceActions
   */
  public function setPluginInstanceActions($pluginInstanceActions)
  {
    $this->pluginInstanceActions = $pluginInstanceActions;
  }
  /**
   * @return GoogleCloudApihubV1PluginInstanceActionID[]
   */
  public function getPluginInstanceActions()
  {
    return $this->pluginInstanceActions;
  }
  /**
   * Output only. The time at which the curation was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1Curation::class, 'Google_Service_APIhub_GoogleCloudApihubV1Curation');
