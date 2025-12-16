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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1beta1EvaluationJobAlertConfig extends \Google\Model
{
  /**
   * Required. An email address to send alerts to.
   *
   * @var string
   */
  public $email;
  /**
   * Required. A number between 0 and 1 that describes a minimum mean average
   * precision threshold. When the evaluation job runs, if it calculates that
   * your model version's predictions from the recent interval have
   * meanAveragePrecision below this threshold, then it sends an alert to your
   * specified email.
   *
   * @var 
   */
  public $minAcceptableMeanAveragePrecision;

  /**
   * Required. An email address to send alerts to.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  public function setMinAcceptableMeanAveragePrecision($minAcceptableMeanAveragePrecision)
  {
    $this->minAcceptableMeanAveragePrecision = $minAcceptableMeanAveragePrecision;
  }
  public function getMinAcceptableMeanAveragePrecision()
  {
    return $this->minAcceptableMeanAveragePrecision;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1EvaluationJobAlertConfig::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1EvaluationJobAlertConfig');
