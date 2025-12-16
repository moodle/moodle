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

class GoogleCloudAiplatformV1NasJobOutputMultiTrialJobOutput extends \Google\Collection
{
  protected $collection_key = 'trainTrials';
  protected $searchTrialsType = GoogleCloudAiplatformV1NasTrial::class;
  protected $searchTrialsDataType = 'array';
  protected $trainTrialsType = GoogleCloudAiplatformV1NasTrial::class;
  protected $trainTrialsDataType = 'array';

  /**
   * Output only. List of NasTrials that were started as part of search stage.
   *
   * @param GoogleCloudAiplatformV1NasTrial[] $searchTrials
   */
  public function setSearchTrials($searchTrials)
  {
    $this->searchTrials = $searchTrials;
  }
  /**
   * @return GoogleCloudAiplatformV1NasTrial[]
   */
  public function getSearchTrials()
  {
    return $this->searchTrials;
  }
  /**
   * Output only. List of NasTrials that were started as part of train stage.
   *
   * @param GoogleCloudAiplatformV1NasTrial[] $trainTrials
   */
  public function setTrainTrials($trainTrials)
  {
    $this->trainTrials = $trainTrials;
  }
  /**
   * @return GoogleCloudAiplatformV1NasTrial[]
   */
  public function getTrainTrials()
  {
    return $this->trainTrials;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NasJobOutputMultiTrialJobOutput::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NasJobOutputMultiTrialJobOutput');
