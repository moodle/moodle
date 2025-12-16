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

namespace Google\Service\Aiplatform\Resource;

use Google\Service\Aiplatform\GoogleCloudAiplatformV1AddTrialMeasurementRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1CheckTrialEarlyStoppingStateRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1CompleteTrialRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListOptimalTrialsRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListOptimalTrialsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListTrialsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1StopTrialRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1SuggestTrialsRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1Trial;
use Google\Service\Aiplatform\GoogleLongrunningOperation;
use Google\Service\Aiplatform\GoogleProtobufEmpty;

/**
 * The "trials" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $trials = $aiplatformService->projects_locations_studies_trials;
 *  </code>
 */
class ProjectsLocationsStudiesTrials extends \Google\Service\Resource
{
  /**
   * Adds a measurement of the objective metrics to a Trial. This measurement is
   * assumed to have been taken before the Trial is complete.
   * (trials.addTrialMeasurement)
   *
   * @param string $trialName Required. The name of the trial to add measurement.
   * Format:
   * `projects/{project}/locations/{location}/studies/{study}/trials/{trial}`
   * @param GoogleCloudAiplatformV1AddTrialMeasurementRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1Trial
   * @throws \Google\Service\Exception
   */
  public function addTrialMeasurement($trialName, GoogleCloudAiplatformV1AddTrialMeasurementRequest $postBody, $optParams = [])
  {
    $params = ['trialName' => $trialName, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('addTrialMeasurement', [$params], GoogleCloudAiplatformV1Trial::class);
  }
  /**
   * Checks whether a Trial should stop or not. Returns a long-running operation.
   * When the operation is successful, it will contain a
   * CheckTrialEarlyStoppingStateResponse. (trials.checkTrialEarlyStoppingState)
   *
   * @param string $trialName Required. The Trial's name. Format:
   * `projects/{project}/locations/{location}/studies/{study}/trials/{trial}`
   * @param GoogleCloudAiplatformV1CheckTrialEarlyStoppingStateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function checkTrialEarlyStoppingState($trialName, GoogleCloudAiplatformV1CheckTrialEarlyStoppingStateRequest $postBody, $optParams = [])
  {
    $params = ['trialName' => $trialName, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('checkTrialEarlyStoppingState', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Marks a Trial as complete. (trials.complete)
   *
   * @param string $name Required. The Trial's name. Format:
   * `projects/{project}/locations/{location}/studies/{study}/trials/{trial}`
   * @param GoogleCloudAiplatformV1CompleteTrialRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1Trial
   * @throws \Google\Service\Exception
   */
  public function complete($name, GoogleCloudAiplatformV1CompleteTrialRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('complete', [$params], GoogleCloudAiplatformV1Trial::class);
  }
  /**
   * Adds a user provided Trial to a Study. (trials.create)
   *
   * @param string $parent Required. The resource name of the Study to create the
   * Trial in. Format: `projects/{project}/locations/{location}/studies/{study}`
   * @param GoogleCloudAiplatformV1Trial $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1Trial
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1Trial $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudAiplatformV1Trial::class);
  }
  /**
   * Deletes a Trial. (trials.delete)
   *
   * @param string $name Required. The Trial's name. Format:
   * `projects/{project}/locations/{location}/studies/{study}/trials/{trial}`
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Gets a Trial. (trials.get)
   *
   * @param string $name Required. The name of the Trial resource. Format:
   * `projects/{project}/locations/{location}/studies/{study}/trials/{trial}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1Trial
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1Trial::class);
  }
  /**
   * Lists the Trials associated with a Study.
   * (trials.listProjectsLocationsStudiesTrials)
   *
   * @param string $parent Required. The resource name of the Study to list the
   * Trial from. Format: `projects/{project}/locations/{location}/studies/{study}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The number of Trials to retrieve per "page"
   * of results. If unspecified, the service will pick an appropriate default.
   * @opt_param string pageToken Optional. A page token to request the next page
   * of results. If unspecified, there are no subsequent pages.
   * @return GoogleCloudAiplatformV1ListTrialsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsStudiesTrials($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListTrialsResponse::class);
  }
  /**
   * Lists the pareto-optimal Trials for multi-objective Study or the optimal
   * Trials for single-objective Study. The definition of pareto-optimal can be
   * checked in wiki page. https://en.wikipedia.org/wiki/Pareto_efficiency
   * (trials.listOptimalTrials)
   *
   * @param string $parent Required. The name of the Study that the optimal Trial
   * belongs to.
   * @param GoogleCloudAiplatformV1ListOptimalTrialsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1ListOptimalTrialsResponse
   * @throws \Google\Service\Exception
   */
  public function listOptimalTrials($parent, GoogleCloudAiplatformV1ListOptimalTrialsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('listOptimalTrials', [$params], GoogleCloudAiplatformV1ListOptimalTrialsResponse::class);
  }
  /**
   * Stops a Trial. (trials.stop)
   *
   * @param string $name Required. The Trial's name. Format:
   * `projects/{project}/locations/{location}/studies/{study}/trials/{trial}`
   * @param GoogleCloudAiplatformV1StopTrialRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1Trial
   * @throws \Google\Service\Exception
   */
  public function stop($name, GoogleCloudAiplatformV1StopTrialRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('stop', [$params], GoogleCloudAiplatformV1Trial::class);
  }
  /**
   * Adds one or more Trials to a Study, with parameter values suggested by Vertex
   * AI Vizier. Returns a long-running operation associated with the generation of
   * Trial suggestions. When this long-running operation succeeds, it will contain
   * a SuggestTrialsResponse. (trials.suggest)
   *
   * @param string $parent Required. The project and location that the Study
   * belongs to. Format: `projects/{project}/locations/{location}/studies/{study}`
   * @param GoogleCloudAiplatformV1SuggestTrialsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function suggest($parent, GoogleCloudAiplatformV1SuggestTrialsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('suggest', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsStudiesTrials::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsStudiesTrials');
