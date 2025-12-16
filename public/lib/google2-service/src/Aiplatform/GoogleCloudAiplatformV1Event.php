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

class GoogleCloudAiplatformV1Event extends \Google\Model
{
  /**
   * Unspecified whether input or output of the Execution.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * An input of the Execution.
   */
  public const TYPE_INPUT = 'INPUT';
  /**
   * An output of the Execution.
   */
  public const TYPE_OUTPUT = 'OUTPUT';
  /**
   * Required. The relative resource name of the Artifact in the Event.
   *
   * @var string
   */
  public $artifact;
  /**
   * Output only. Time the Event occurred.
   *
   * @var string
   */
  public $eventTime;
  /**
   * Output only. The relative resource name of the Execution in the Event.
   *
   * @var string
   */
  public $execution;
  /**
   * The labels with user-defined metadata to annotate Events. Label keys and
   * values can be no longer than 64 characters (Unicode codepoints), can only
   * contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. No more than 64 user labels can be
   * associated with one Event (System labels are excluded). See
   * https://goo.gl/xmQnxf for more information and examples of labels. System
   * reserved label keys are prefixed with "aiplatform.googleapis.com/" and are
   * immutable.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. The type of the Event.
   *
   * @var string
   */
  public $type;

  /**
   * Required. The relative resource name of the Artifact in the Event.
   *
   * @param string $artifact
   */
  public function setArtifact($artifact)
  {
    $this->artifact = $artifact;
  }
  /**
   * @return string
   */
  public function getArtifact()
  {
    return $this->artifact;
  }
  /**
   * Output only. Time the Event occurred.
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * Output only. The relative resource name of the Execution in the Event.
   *
   * @param string $execution
   */
  public function setExecution($execution)
  {
    $this->execution = $execution;
  }
  /**
   * @return string
   */
  public function getExecution()
  {
    return $this->execution;
  }
  /**
   * The labels with user-defined metadata to annotate Events. Label keys and
   * values can be no longer than 64 characters (Unicode codepoints), can only
   * contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. No more than 64 user labels can be
   * associated with one Event (System labels are excluded). See
   * https://goo.gl/xmQnxf for more information and examples of labels. System
   * reserved label keys are prefixed with "aiplatform.googleapis.com/" and are
   * immutable.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Required. The type of the Event.
   *
   * Accepted values: TYPE_UNSPECIFIED, INPUT, OUTPUT
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Event::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Event');
