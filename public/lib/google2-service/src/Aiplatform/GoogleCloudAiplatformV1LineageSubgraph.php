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

class GoogleCloudAiplatformV1LineageSubgraph extends \Google\Collection
{
  protected $collection_key = 'executions';
  protected $artifactsType = GoogleCloudAiplatformV1Artifact::class;
  protected $artifactsDataType = 'array';
  protected $eventsType = GoogleCloudAiplatformV1Event::class;
  protected $eventsDataType = 'array';
  protected $executionsType = GoogleCloudAiplatformV1Execution::class;
  protected $executionsDataType = 'array';

  /**
   * The Artifact nodes in the subgraph.
   *
   * @param GoogleCloudAiplatformV1Artifact[] $artifacts
   */
  public function setArtifacts($artifacts)
  {
    $this->artifacts = $artifacts;
  }
  /**
   * @return GoogleCloudAiplatformV1Artifact[]
   */
  public function getArtifacts()
  {
    return $this->artifacts;
  }
  /**
   * The Event edges between Artifacts and Executions in the subgraph.
   *
   * @param GoogleCloudAiplatformV1Event[] $events
   */
  public function setEvents($events)
  {
    $this->events = $events;
  }
  /**
   * @return GoogleCloudAiplatformV1Event[]
   */
  public function getEvents()
  {
    return $this->events;
  }
  /**
   * The Execution nodes in the subgraph.
   *
   * @param GoogleCloudAiplatformV1Execution[] $executions
   */
  public function setExecutions($executions)
  {
    $this->executions = $executions;
  }
  /**
   * @return GoogleCloudAiplatformV1Execution[]
   */
  public function getExecutions()
  {
    return $this->executions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1LineageSubgraph::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1LineageSubgraph');
