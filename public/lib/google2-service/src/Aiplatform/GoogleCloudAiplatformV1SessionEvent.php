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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SessionEvent extends \Google\Model
{
  protected $actionsType = GoogleCloudAiplatformV1EventActions::class;
  protected $actionsDataType = '';
  /**
   * Required. The name of the agent that sent the event, or user.
   *
   * @var string
   */
  public $author;
  protected $contentType = GoogleCloudAiplatformV1Content::class;
  protected $contentDataType = '';
  /**
   * Optional. Error code if the response is an error. Code varies by model.
   *
   * @var string
   */
  public $errorCode;
  /**
   * Optional. Error message if the response is an error.
   *
   * @var string
   */
  public $errorMessage;
  protected $eventMetadataType = GoogleCloudAiplatformV1EventMetadata::class;
  protected $eventMetadataDataType = '';
  /**
   * Required. The invocation id of the event, multiple events can have the same
   * invocation id.
   *
   * @var string
   */
  public $invocationId;
  /**
   * Identifier. The resource name of the event. Format:`projects/{project}/loca
   * tions/{location}/reasoningEngines/{reasoning_engine}/sessions/{session}/eve
   * nts/{event}`.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Timestamp when the event was created on client side.
   *
   * @var string
   */
  public $timestamp;

  /**
   * Optional. Actions executed by the agent.
   *
   * @param GoogleCloudAiplatformV1EventActions $actions
   */
  public function setActions(GoogleCloudAiplatformV1EventActions $actions)
  {
    $this->actions = $actions;
  }
  /**
   * @return GoogleCloudAiplatformV1EventActions
   */
  public function getActions()
  {
    return $this->actions;
  }
  /**
   * Required. The name of the agent that sent the event, or user.
   *
   * @param string $author
   */
  public function setAuthor($author)
  {
    $this->author = $author;
  }
  /**
   * @return string
   */
  public function getAuthor()
  {
    return $this->author;
  }
  /**
   * Optional. Content of the event provided by the author.
   *
   * @param GoogleCloudAiplatformV1Content $content
   */
  public function setContent(GoogleCloudAiplatformV1Content $content)
  {
    $this->content = $content;
  }
  /**
   * @return GoogleCloudAiplatformV1Content
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Optional. Error code if the response is an error. Code varies by model.
   *
   * @param string $errorCode
   */
  public function setErrorCode($errorCode)
  {
    $this->errorCode = $errorCode;
  }
  /**
   * @return string
   */
  public function getErrorCode()
  {
    return $this->errorCode;
  }
  /**
   * Optional. Error message if the response is an error.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * Optional. Metadata relating to this event.
   *
   * @param GoogleCloudAiplatformV1EventMetadata $eventMetadata
   */
  public function setEventMetadata(GoogleCloudAiplatformV1EventMetadata $eventMetadata)
  {
    $this->eventMetadata = $eventMetadata;
  }
  /**
   * @return GoogleCloudAiplatformV1EventMetadata
   */
  public function getEventMetadata()
  {
    return $this->eventMetadata;
  }
  /**
   * Required. The invocation id of the event, multiple events can have the same
   * invocation id.
   *
   * @param string $invocationId
   */
  public function setInvocationId($invocationId)
  {
    $this->invocationId = $invocationId;
  }
  /**
   * @return string
   */
  public function getInvocationId()
  {
    return $this->invocationId;
  }
  /**
   * Identifier. The resource name of the event. Format:`projects/{project}/loca
   * tions/{location}/reasoningEngines/{reasoning_engine}/sessions/{session}/eve
   * nts/{event}`.
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
   * Required. Timestamp when the event was created on client side.
   *
   * @param string $timestamp
   */
  public function setTimestamp($timestamp)
  {
    $this->timestamp = $timestamp;
  }
  /**
   * @return string
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SessionEvent::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SessionEvent');
