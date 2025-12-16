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

class GoogleCloudAiplatformV1NasTrialDetail extends \Google\Model
{
  /**
   * Output only. Resource name of the NasTrialDetail.
   *
   * @var string
   */
  public $name;
  /**
   * The parameters for the NasJob NasTrial.
   *
   * @var string
   */
  public $parameters;
  protected $searchTrialType = GoogleCloudAiplatformV1NasTrial::class;
  protected $searchTrialDataType = '';
  protected $trainTrialType = GoogleCloudAiplatformV1NasTrial::class;
  protected $trainTrialDataType = '';

  /**
   * Output only. Resource name of the NasTrialDetail.
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
   * The parameters for the NasJob NasTrial.
   *
   * @param string $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return string
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * The requested search NasTrial.
   *
   * @param GoogleCloudAiplatformV1NasTrial $searchTrial
   */
  public function setSearchTrial(GoogleCloudAiplatformV1NasTrial $searchTrial)
  {
    $this->searchTrial = $searchTrial;
  }
  /**
   * @return GoogleCloudAiplatformV1NasTrial
   */
  public function getSearchTrial()
  {
    return $this->searchTrial;
  }
  /**
   * The train NasTrial corresponding to search_trial. Only populated if
   * search_trial is used for training.
   *
   * @param GoogleCloudAiplatformV1NasTrial $trainTrial
   */
  public function setTrainTrial(GoogleCloudAiplatformV1NasTrial $trainTrial)
  {
    $this->trainTrial = $trainTrial;
  }
  /**
   * @return GoogleCloudAiplatformV1NasTrial
   */
  public function getTrainTrial()
  {
    return $this->trainTrial;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NasTrialDetail::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NasTrialDetail');
