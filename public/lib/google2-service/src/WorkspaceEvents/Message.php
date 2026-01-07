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

namespace Google\Service\WorkspaceEvents;

class Message extends \Google\Collection
{
  public const ROLE_ROLE_UNSPECIFIED = 'ROLE_UNSPECIFIED';
  /**
   * USER role refers to communication from the client to the server.
   */
  public const ROLE_ROLE_USER = 'ROLE_USER';
  /**
   * AGENT role refers to communication from the server to the client.
   */
  public const ROLE_ROLE_AGENT = 'ROLE_AGENT';
  protected $collection_key = 'extensions';
  protected $contentType = Part::class;
  protected $contentDataType = 'array';
  /**
   * The context id of the message. This is optional and if set, the message
   * will be associated with the given context.
   *
   * @var string
   */
  public $contextId;
  /**
   * The URIs of extensions that are present or contributed to this Message.
   *
   * @var string[]
   */
  public $extensions;
  /**
   * The unique identifier (e.g. UUID)of the message. This is required and
   * created by the message creator.
   *
   * @var string
   */
  public $messageId;
  /**
   * protolint:enable REPEATED_FIELD_NAMES_PLURALIZED Any optional metadata to
   * provide along with the message.
   *
   * @var array[]
   */
  public $metadata;
  /**
   * A role for the message.
   *
   * @var string
   */
  public $role;
  /**
   * The task id of the message. This is optional and if set, the message will
   * be associated with the given task.
   *
   * @var string
   */
  public $taskId;

  /**
   * protolint:disable REPEATED_FIELD_NAMES_PLURALIZED Content is the container
   * of the message content.
   *
   * @param Part[] $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return Part[]
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * The context id of the message. This is optional and if set, the message
   * will be associated with the given context.
   *
   * @param string $contextId
   */
  public function setContextId($contextId)
  {
    $this->contextId = $contextId;
  }
  /**
   * @return string
   */
  public function getContextId()
  {
    return $this->contextId;
  }
  /**
   * The URIs of extensions that are present or contributed to this Message.
   *
   * @param string[] $extensions
   */
  public function setExtensions($extensions)
  {
    $this->extensions = $extensions;
  }
  /**
   * @return string[]
   */
  public function getExtensions()
  {
    return $this->extensions;
  }
  /**
   * The unique identifier (e.g. UUID)of the message. This is required and
   * created by the message creator.
   *
   * @param string $messageId
   */
  public function setMessageId($messageId)
  {
    $this->messageId = $messageId;
  }
  /**
   * @return string
   */
  public function getMessageId()
  {
    return $this->messageId;
  }
  /**
   * protolint:enable REPEATED_FIELD_NAMES_PLURALIZED Any optional metadata to
   * provide along with the message.
   *
   * @param array[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * A role for the message.
   *
   * Accepted values: ROLE_UNSPECIFIED, ROLE_USER, ROLE_AGENT
   *
   * @param self::ROLE_* $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return self::ROLE_*
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * The task id of the message. This is optional and if set, the message will
   * be associated with the given task.
   *
   * @param string $taskId
   */
  public function setTaskId($taskId)
  {
    $this->taskId = $taskId;
  }
  /**
   * @return string
   */
  public function getTaskId()
  {
    return $this->taskId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Message::class, 'Google_Service_WorkspaceEvents_Message');
