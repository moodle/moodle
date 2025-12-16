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

namespace Google\Service\CloudFunctions;

class GoogleCloudFunctionsV2Stage extends \Google\Collection
{
  /**
   * Not specified. Invalid name.
   */
  public const NAME_NAME_UNSPECIFIED = 'NAME_UNSPECIFIED';
  /**
   * Artifact Registry Stage
   */
  public const NAME_ARTIFACT_REGISTRY = 'ARTIFACT_REGISTRY';
  /**
   * Build Stage
   */
  public const NAME_BUILD = 'BUILD';
  /**
   * Service Stage
   */
  public const NAME_SERVICE = 'SERVICE';
  /**
   * Trigger Stage
   */
  public const NAME_TRIGGER = 'TRIGGER';
  /**
   * Service Rollback Stage
   */
  public const NAME_SERVICE_ROLLBACK = 'SERVICE_ROLLBACK';
  /**
   * Trigger Rollback Stage
   */
  public const NAME_TRIGGER_ROLLBACK = 'TRIGGER_ROLLBACK';
  /**
   * Not specified. Invalid state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Stage has not started.
   */
  public const STATE_NOT_STARTED = 'NOT_STARTED';
  /**
   * Stage is in progress.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Stage has completed.
   */
  public const STATE_COMPLETE = 'COMPLETE';
  protected $collection_key = 'stateMessages';
  /**
   * Message describing the Stage
   *
   * @var string
   */
  public $message;
  /**
   * Name of the Stage. This will be unique for each Stage.
   *
   * @var string
   */
  public $name;
  /**
   * Resource of the Stage
   *
   * @var string
   */
  public $resource;
  /**
   * Link to the current Stage resource
   *
   * @var string
   */
  public $resourceUri;
  /**
   * Current state of the Stage
   *
   * @var string
   */
  public $state;
  protected $stateMessagesType = GoogleCloudFunctionsV2StateMessage::class;
  protected $stateMessagesDataType = 'array';

  /**
   * Message describing the Stage
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Name of the Stage. This will be unique for each Stage.
   *
   * Accepted values: NAME_UNSPECIFIED, ARTIFACT_REGISTRY, BUILD, SERVICE,
   * TRIGGER, SERVICE_ROLLBACK, TRIGGER_ROLLBACK
   *
   * @param self::NAME_* $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return self::NAME_*
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Resource of the Stage
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * Link to the current Stage resource
   *
   * @param string $resourceUri
   */
  public function setResourceUri($resourceUri)
  {
    $this->resourceUri = $resourceUri;
  }
  /**
   * @return string
   */
  public function getResourceUri()
  {
    return $this->resourceUri;
  }
  /**
   * Current state of the Stage
   *
   * Accepted values: STATE_UNSPECIFIED, NOT_STARTED, IN_PROGRESS, COMPLETE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * State messages from the current Stage.
   *
   * @param GoogleCloudFunctionsV2StateMessage[] $stateMessages
   */
  public function setStateMessages($stateMessages)
  {
    $this->stateMessages = $stateMessages;
  }
  /**
   * @return GoogleCloudFunctionsV2StateMessage[]
   */
  public function getStateMessages()
  {
    return $this->stateMessages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudFunctionsV2Stage::class, 'Google_Service_CloudFunctions_GoogleCloudFunctionsV2Stage');
