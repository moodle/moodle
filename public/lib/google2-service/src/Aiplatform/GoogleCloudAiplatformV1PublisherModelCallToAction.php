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

class GoogleCloudAiplatformV1PublisherModelCallToAction extends \Google\Model
{
  protected $createApplicationType = GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences::class;
  protected $createApplicationDataType = '';
  protected $deployType = GoogleCloudAiplatformV1PublisherModelCallToActionDeploy::class;
  protected $deployDataType = '';
  protected $deployGkeType = GoogleCloudAiplatformV1PublisherModelCallToActionDeployGke::class;
  protected $deployGkeDataType = '';
  protected $multiDeployVertexType = GoogleCloudAiplatformV1PublisherModelCallToActionDeployVertex::class;
  protected $multiDeployVertexDataType = '';
  protected $openEvaluationPipelineType = GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences::class;
  protected $openEvaluationPipelineDataType = '';
  protected $openFineTuningPipelineType = GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences::class;
  protected $openFineTuningPipelineDataType = '';
  protected $openFineTuningPipelinesType = GoogleCloudAiplatformV1PublisherModelCallToActionOpenFineTuningPipelines::class;
  protected $openFineTuningPipelinesDataType = '';
  protected $openGenerationAiStudioType = GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences::class;
  protected $openGenerationAiStudioDataType = '';
  protected $openGenieType = GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences::class;
  protected $openGenieDataType = '';
  protected $openNotebookType = GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences::class;
  protected $openNotebookDataType = '';
  protected $openNotebooksType = GoogleCloudAiplatformV1PublisherModelCallToActionOpenNotebooks::class;
  protected $openNotebooksDataType = '';
  protected $openPromptTuningPipelineType = GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences::class;
  protected $openPromptTuningPipelineDataType = '';
  protected $requestAccessType = GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences::class;
  protected $requestAccessDataType = '';
  protected $viewRestApiType = GoogleCloudAiplatformV1PublisherModelCallToActionViewRestApi::class;
  protected $viewRestApiDataType = '';

  /**
   * Optional. Create application using the PublisherModel.
   *
   * @param GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences $createApplication
   */
  public function setCreateApplication(GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences $createApplication)
  {
    $this->createApplication = $createApplication;
  }
  /**
   * @return GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences
   */
  public function getCreateApplication()
  {
    return $this->createApplication;
  }
  /**
   * Optional. Deploy the PublisherModel to Vertex Endpoint.
   *
   * @param GoogleCloudAiplatformV1PublisherModelCallToActionDeploy $deploy
   */
  public function setDeploy(GoogleCloudAiplatformV1PublisherModelCallToActionDeploy $deploy)
  {
    $this->deploy = $deploy;
  }
  /**
   * @return GoogleCloudAiplatformV1PublisherModelCallToActionDeploy
   */
  public function getDeploy()
  {
    return $this->deploy;
  }
  /**
   * Optional. Deploy PublisherModel to Google Kubernetes Engine.
   *
   * @param GoogleCloudAiplatformV1PublisherModelCallToActionDeployGke $deployGke
   */
  public function setDeployGke(GoogleCloudAiplatformV1PublisherModelCallToActionDeployGke $deployGke)
  {
    $this->deployGke = $deployGke;
  }
  /**
   * @return GoogleCloudAiplatformV1PublisherModelCallToActionDeployGke
   */
  public function getDeployGke()
  {
    return $this->deployGke;
  }
  /**
   * Optional. Multiple setups to deploy the PublisherModel to Vertex Endpoint.
   *
   * @param GoogleCloudAiplatformV1PublisherModelCallToActionDeployVertex $multiDeployVertex
   */
  public function setMultiDeployVertex(GoogleCloudAiplatformV1PublisherModelCallToActionDeployVertex $multiDeployVertex)
  {
    $this->multiDeployVertex = $multiDeployVertex;
  }
  /**
   * @return GoogleCloudAiplatformV1PublisherModelCallToActionDeployVertex
   */
  public function getMultiDeployVertex()
  {
    return $this->multiDeployVertex;
  }
  /**
   * Optional. Open evaluation pipeline of the PublisherModel.
   *
   * @param GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences $openEvaluationPipeline
   */
  public function setOpenEvaluationPipeline(GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences $openEvaluationPipeline)
  {
    $this->openEvaluationPipeline = $openEvaluationPipeline;
  }
  /**
   * @return GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences
   */
  public function getOpenEvaluationPipeline()
  {
    return $this->openEvaluationPipeline;
  }
  /**
   * Optional. Open fine-tuning pipeline of the PublisherModel.
   *
   * @param GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences $openFineTuningPipeline
   */
  public function setOpenFineTuningPipeline(GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences $openFineTuningPipeline)
  {
    $this->openFineTuningPipeline = $openFineTuningPipeline;
  }
  /**
   * @return GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences
   */
  public function getOpenFineTuningPipeline()
  {
    return $this->openFineTuningPipeline;
  }
  /**
   * Optional. Open fine-tuning pipelines of the PublisherModel.
   *
   * @param GoogleCloudAiplatformV1PublisherModelCallToActionOpenFineTuningPipelines $openFineTuningPipelines
   */
  public function setOpenFineTuningPipelines(GoogleCloudAiplatformV1PublisherModelCallToActionOpenFineTuningPipelines $openFineTuningPipelines)
  {
    $this->openFineTuningPipelines = $openFineTuningPipelines;
  }
  /**
   * @return GoogleCloudAiplatformV1PublisherModelCallToActionOpenFineTuningPipelines
   */
  public function getOpenFineTuningPipelines()
  {
    return $this->openFineTuningPipelines;
  }
  /**
   * Optional. Open in Generation AI Studio.
   *
   * @param GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences $openGenerationAiStudio
   */
  public function setOpenGenerationAiStudio(GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences $openGenerationAiStudio)
  {
    $this->openGenerationAiStudio = $openGenerationAiStudio;
  }
  /**
   * @return GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences
   */
  public function getOpenGenerationAiStudio()
  {
    return $this->openGenerationAiStudio;
  }
  /**
   * Optional. Open Genie / Playground.
   *
   * @param GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences $openGenie
   */
  public function setOpenGenie(GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences $openGenie)
  {
    $this->openGenie = $openGenie;
  }
  /**
   * @return GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences
   */
  public function getOpenGenie()
  {
    return $this->openGenie;
  }
  /**
   * Optional. Open notebook of the PublisherModel.
   *
   * @param GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences $openNotebook
   */
  public function setOpenNotebook(GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences $openNotebook)
  {
    $this->openNotebook = $openNotebook;
  }
  /**
   * @return GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences
   */
  public function getOpenNotebook()
  {
    return $this->openNotebook;
  }
  /**
   * Optional. Open notebooks of the PublisherModel.
   *
   * @param GoogleCloudAiplatformV1PublisherModelCallToActionOpenNotebooks $openNotebooks
   */
  public function setOpenNotebooks(GoogleCloudAiplatformV1PublisherModelCallToActionOpenNotebooks $openNotebooks)
  {
    $this->openNotebooks = $openNotebooks;
  }
  /**
   * @return GoogleCloudAiplatformV1PublisherModelCallToActionOpenNotebooks
   */
  public function getOpenNotebooks()
  {
    return $this->openNotebooks;
  }
  /**
   * Optional. Open prompt-tuning pipeline of the PublisherModel.
   *
   * @param GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences $openPromptTuningPipeline
   */
  public function setOpenPromptTuningPipeline(GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences $openPromptTuningPipeline)
  {
    $this->openPromptTuningPipeline = $openPromptTuningPipeline;
  }
  /**
   * @return GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences
   */
  public function getOpenPromptTuningPipeline()
  {
    return $this->openPromptTuningPipeline;
  }
  /**
   * Optional. Request for access.
   *
   * @param GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences $requestAccess
   */
  public function setRequestAccess(GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences $requestAccess)
  {
    $this->requestAccess = $requestAccess;
  }
  /**
   * @return GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences
   */
  public function getRequestAccess()
  {
    return $this->requestAccess;
  }
  /**
   * Optional. To view Rest API docs.
   *
   * @param GoogleCloudAiplatformV1PublisherModelCallToActionViewRestApi $viewRestApi
   */
  public function setViewRestApi(GoogleCloudAiplatformV1PublisherModelCallToActionViewRestApi $viewRestApi)
  {
    $this->viewRestApi = $viewRestApi;
  }
  /**
   * @return GoogleCloudAiplatformV1PublisherModelCallToActionViewRestApi
   */
  public function getViewRestApi()
  {
    return $this->viewRestApi;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PublisherModelCallToAction::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PublisherModelCallToAction');
