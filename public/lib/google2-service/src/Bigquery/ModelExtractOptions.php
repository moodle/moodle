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

namespace Google\Service\Bigquery;

class ModelExtractOptions extends \Google\Model
{
  /**
   * The 1-based ID of the trial to be exported from a hyperparameter tuning
   * model. If not specified, the trial with id = [Model](https://cloud.google.c
   * om/bigquery/docs/reference/rest/v2/models#resource:-model).defaultTrialId
   * is exported. This field is ignored for models not trained with
   * hyperparameter tuning.
   *
   * @var string
   */
  public $trialId;

  /**
   * The 1-based ID of the trial to be exported from a hyperparameter tuning
   * model. If not specified, the trial with id = [Model](https://cloud.google.c
   * om/bigquery/docs/reference/rest/v2/models#resource:-model).defaultTrialId
   * is exported. This field is ignored for models not trained with
   * hyperparameter tuning.
   *
   * @param string $trialId
   */
  public function setTrialId($trialId)
  {
    $this->trialId = $trialId;
  }
  /**
   * @return string
   */
  public function getTrialId()
  {
    return $this->trialId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ModelExtractOptions::class, 'Google_Service_Bigquery_ModelExtractOptions');
