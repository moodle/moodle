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

namespace Google\Service\PolicySimulator;

class GoogleCloudPolicysimulatorV1ReplayResult extends \Google\Model
{
  protected $accessTupleType = GoogleCloudPolicysimulatorV1AccessTuple::class;
  protected $accessTupleDataType = '';
  protected $diffType = GoogleCloudPolicysimulatorV1ReplayDiff::class;
  protected $diffDataType = '';
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  protected $lastSeenDateType = GoogleTypeDate::class;
  protected $lastSeenDateDataType = '';
  /**
   * The resource name of the `ReplayResult`, in the following format:
   * `{projects|folders|organizations}/{resource-
   * id}/locations/global/replays/{replay-id}/results/{replay-result-id}`, where
   * `{resource-id}` is the ID of the project, folder, or organization that owns
   * the Replay. Example: `projects/my-example-project/locations/global/replays/
   * 506a5f7f-38ce-4d7d-8e03-479ce1833c36/results/1234`
   *
   * @var string
   */
  public $name;
  /**
   * The Replay that the access tuple was included in.
   *
   * @var string
   */
  public $parent;

  /**
   * The access tuple that was replayed. This field includes information about
   * the principal, resource, and permission that were involved in the access
   * attempt.
   *
   * @param GoogleCloudPolicysimulatorV1AccessTuple $accessTuple
   */
  public function setAccessTuple(GoogleCloudPolicysimulatorV1AccessTuple $accessTuple)
  {
    $this->accessTuple = $accessTuple;
  }
  /**
   * @return GoogleCloudPolicysimulatorV1AccessTuple
   */
  public function getAccessTuple()
  {
    return $this->accessTuple;
  }
  /**
   * The difference between the principal's access under the current (baseline)
   * policies and the principal's access under the proposed (simulated)
   * policies. This field is only included for access tuples that were
   * successfully replayed and had different results under the current policies
   * and the proposed policies.
   *
   * @param GoogleCloudPolicysimulatorV1ReplayDiff $diff
   */
  public function setDiff(GoogleCloudPolicysimulatorV1ReplayDiff $diff)
  {
    $this->diff = $diff;
  }
  /**
   * @return GoogleCloudPolicysimulatorV1ReplayDiff
   */
  public function getDiff()
  {
    return $this->diff;
  }
  /**
   * The error that caused the access tuple replay to fail. This field is only
   * included for access tuples that were not replayed successfully.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * The latest date this access tuple was seen in the logs.
   *
   * @param GoogleTypeDate $lastSeenDate
   */
  public function setLastSeenDate(GoogleTypeDate $lastSeenDate)
  {
    $this->lastSeenDate = $lastSeenDate;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getLastSeenDate()
  {
    return $this->lastSeenDate;
  }
  /**
   * The resource name of the `ReplayResult`, in the following format:
   * `{projects|folders|organizations}/{resource-
   * id}/locations/global/replays/{replay-id}/results/{replay-result-id}`, where
   * `{resource-id}` is the ID of the project, folder, or organization that owns
   * the Replay. Example: `projects/my-example-project/locations/global/replays/
   * 506a5f7f-38ce-4d7d-8e03-479ce1833c36/results/1234`
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
   * The Replay that the access tuple was included in.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicysimulatorV1ReplayResult::class, 'Google_Service_PolicySimulator_GoogleCloudPolicysimulatorV1ReplayResult');
