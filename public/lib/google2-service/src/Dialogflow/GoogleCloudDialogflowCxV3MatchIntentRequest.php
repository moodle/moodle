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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3MatchIntentRequest extends \Google\Model
{
  /**
   * Persist session parameter changes from `query_params`.
   *
   * @var bool
   */
  public $persistParameterChanges;
  protected $queryInputType = GoogleCloudDialogflowCxV3QueryInput::class;
  protected $queryInputDataType = '';
  protected $queryParamsType = GoogleCloudDialogflowCxV3QueryParameters::class;
  protected $queryParamsDataType = '';

  /**
   * Persist session parameter changes from `query_params`.
   *
   * @param bool $persistParameterChanges
   */
  public function setPersistParameterChanges($persistParameterChanges)
  {
    $this->persistParameterChanges = $persistParameterChanges;
  }
  /**
   * @return bool
   */
  public function getPersistParameterChanges()
  {
    return $this->persistParameterChanges;
  }
  /**
   * Required. The input specification.
   *
   * @param GoogleCloudDialogflowCxV3QueryInput $queryInput
   */
  public function setQueryInput(GoogleCloudDialogflowCxV3QueryInput $queryInput)
  {
    $this->queryInput = $queryInput;
  }
  /**
   * @return GoogleCloudDialogflowCxV3QueryInput
   */
  public function getQueryInput()
  {
    return $this->queryInput;
  }
  /**
   * The parameters of this query.
   *
   * @param GoogleCloudDialogflowCxV3QueryParameters $queryParams
   */
  public function setQueryParams(GoogleCloudDialogflowCxV3QueryParameters $queryParams)
  {
    $this->queryParams = $queryParams;
  }
  /**
   * @return GoogleCloudDialogflowCxV3QueryParameters
   */
  public function getQueryParams()
  {
    return $this->queryParams;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3MatchIntentRequest::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3MatchIntentRequest');
