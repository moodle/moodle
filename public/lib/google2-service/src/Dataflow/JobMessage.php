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

namespace Google\Service\Dataflow;

class JobMessage extends \Google\Model
{
  /**
   * The message importance isn't specified, or is unknown.
   */
  public const MESSAGE_IMPORTANCE_JOB_MESSAGE_IMPORTANCE_UNKNOWN = 'JOB_MESSAGE_IMPORTANCE_UNKNOWN';
  /**
   * The message is at the 'debug' level: typically only useful for software
   * engineers working on the code the job is running. Typically, Dataflow
   * pipeline runners do not display log messages at this level by default.
   */
  public const MESSAGE_IMPORTANCE_JOB_MESSAGE_DEBUG = 'JOB_MESSAGE_DEBUG';
  /**
   * The message is at the 'detailed' level: somewhat verbose, but potentially
   * useful to users. Typically, Dataflow pipeline runners do not display log
   * messages at this level by default. These messages are displayed by default
   * in the Dataflow monitoring UI.
   */
  public const MESSAGE_IMPORTANCE_JOB_MESSAGE_DETAILED = 'JOB_MESSAGE_DETAILED';
  /**
   * The message is at the 'basic' level: useful for keeping track of the
   * execution of a Dataflow pipeline. Typically, Dataflow pipeline runners
   * display log messages at this level by default, and these messages are
   * displayed by default in the Dataflow monitoring UI.
   */
  public const MESSAGE_IMPORTANCE_JOB_MESSAGE_BASIC = 'JOB_MESSAGE_BASIC';
  /**
   * The message is at the 'warning' level: indicating a condition pertaining to
   * a job which may require human intervention. Typically, Dataflow pipeline
   * runners display log messages at this level by default, and these messages
   * are displayed by default in the Dataflow monitoring UI.
   */
  public const MESSAGE_IMPORTANCE_JOB_MESSAGE_WARNING = 'JOB_MESSAGE_WARNING';
  /**
   * The message is at the 'error' level: indicating a condition preventing a
   * job from succeeding. Typically, Dataflow pipeline runners display log
   * messages at this level by default, and these messages are displayed by
   * default in the Dataflow monitoring UI.
   */
  public const MESSAGE_IMPORTANCE_JOB_MESSAGE_ERROR = 'JOB_MESSAGE_ERROR';
  /**
   * Deprecated.
   *
   * @var string
   */
  public $id;
  /**
   * Importance level of the message.
   *
   * @var string
   */
  public $messageImportance;
  /**
   * The text of the message.
   *
   * @var string
   */
  public $messageText;
  /**
   * The timestamp of the message.
   *
   * @var string
   */
  public $time;

  /**
   * Deprecated.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Importance level of the message.
   *
   * Accepted values: JOB_MESSAGE_IMPORTANCE_UNKNOWN, JOB_MESSAGE_DEBUG,
   * JOB_MESSAGE_DETAILED, JOB_MESSAGE_BASIC, JOB_MESSAGE_WARNING,
   * JOB_MESSAGE_ERROR
   *
   * @param self::MESSAGE_IMPORTANCE_* $messageImportance
   */
  public function setMessageImportance($messageImportance)
  {
    $this->messageImportance = $messageImportance;
  }
  /**
   * @return self::MESSAGE_IMPORTANCE_*
   */
  public function getMessageImportance()
  {
    return $this->messageImportance;
  }
  /**
   * The text of the message.
   *
   * @param string $messageText
   */
  public function setMessageText($messageText)
  {
    $this->messageText = $messageText;
  }
  /**
   * @return string
   */
  public function getMessageText()
  {
    return $this->messageText;
  }
  /**
   * The timestamp of the message.
   *
   * @param string $time
   */
  public function setTime($time)
  {
    $this->time = $time;
  }
  /**
   * @return string
   */
  public function getTime()
  {
    return $this->time;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobMessage::class, 'Google_Service_Dataflow_JobMessage');
