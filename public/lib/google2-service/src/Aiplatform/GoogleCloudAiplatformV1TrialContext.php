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

class GoogleCloudAiplatformV1TrialContext extends \Google\Collection
{
  protected $collection_key = 'parameters';
  /**
   * A human-readable field which can store a description of this context. This
   * will become part of the resulting Trial's description field.
   *
   * @var string
   */
  public $description;
  protected $parametersType = GoogleCloudAiplatformV1TrialParameter::class;
  protected $parametersDataType = 'array';

  /**
   * A human-readable field which can store a description of this context. This
   * will become part of the resulting Trial's description field.
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
   * If/when a Trial is generated or selected from this Context, its Parameters
   * will match any parameters specified here. (I.e. if this context specifies
   * parameter name:'a' int_value:3, then a resulting Trial will have
   * int_value:3 for its parameter named 'a'.) Note that we first attempt to
   * match existing REQUESTED Trials with contexts, and if there are no matches,
   * we generate suggestions in the subspace defined by the parameters specified
   * here. NOTE: a Context without any Parameters matches the entire feasible
   * search space.
   *
   * @param GoogleCloudAiplatformV1TrialParameter[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleCloudAiplatformV1TrialParameter[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1TrialContext::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1TrialContext');
