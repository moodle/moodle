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

class GoogleCloudAiplatformV1GenerateMemoriesRequestVertexSessionSource extends \Google\Model
{
  /**
   * Optional. End time (exclusive) of the time range. If not set, the end time
   * is unbounded.
   *
   * @var string
   */
  public $endTime;
  /**
   * Required. The resource name of the Session to generate memories for.
   * Format: `projects/{project}/locations/{location}/reasoningEngines/{reasonin
   * g_engine}/sessions/{session}`
   *
   * @var string
   */
  public $session;
  /**
   * Optional. Time range to define which session events should be used to
   * generate memories. Start time (inclusive) of the time range. If not set,
   * the start time is unbounded.
   *
   * @var string
   */
  public $startTime;

  /**
   * Optional. End time (exclusive) of the time range. If not set, the end time
   * is unbounded.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Required. The resource name of the Session to generate memories for.
   * Format: `projects/{project}/locations/{location}/reasoningEngines/{reasonin
   * g_engine}/sessions/{session}`
   *
   * @param string $session
   */
  public function setSession($session)
  {
    $this->session = $session;
  }
  /**
   * @return string
   */
  public function getSession()
  {
    return $this->session;
  }
  /**
   * Optional. Time range to define which session events should be used to
   * generate memories. Start time (inclusive) of the time range. If not set,
   * the start time is unbounded.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GenerateMemoriesRequestVertexSessionSource::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GenerateMemoriesRequestVertexSessionSource');
