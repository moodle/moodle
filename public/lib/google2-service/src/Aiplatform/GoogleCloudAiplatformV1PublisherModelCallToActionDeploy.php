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

class GoogleCloudAiplatformV1PublisherModelCallToActionDeploy extends \Google\Model
{
  /**
   * Optional. The path to the directory containing the Model artifact and any
   * of its supporting files.
   *
   * @var string
   */
  public $artifactUri;
  protected $automaticResourcesType = GoogleCloudAiplatformV1AutomaticResources::class;
  protected $automaticResourcesDataType = '';
  protected $containerSpecType = GoogleCloudAiplatformV1ModelContainerSpec::class;
  protected $containerSpecDataType = '';
  protected $dedicatedResourcesType = GoogleCloudAiplatformV1DedicatedResources::class;
  protected $dedicatedResourcesDataType = '';
  protected $deployMetadataType = GoogleCloudAiplatformV1PublisherModelCallToActionDeployDeployMetadata::class;
  protected $deployMetadataDataType = '';
  /**
   * Optional. The name of the deploy task (e.g., "text to image generation").
   *
   * @var string
   */
  public $deployTaskName;
  protected $largeModelReferenceType = GoogleCloudAiplatformV1LargeModelReference::class;
  protected $largeModelReferenceDataType = '';
  /**
   * Optional. Default model display name.
   *
   * @var string
   */
  public $modelDisplayName;
  /**
   * Optional. The signed URI for ephemeral Cloud Storage access to model
   * artifact.
   *
   * @var string
   */
  public $publicArtifactUri;
  /**
   * The resource name of the shared DeploymentResourcePool to deploy on.
   * Format: `projects/{project}/locations/{location}/deploymentResourcePools/{d
   * eployment_resource_pool}`
   *
   * @var string
   */
  public $sharedResources;
  /**
   * Required. The title of the regional resource reference.
   *
   * @var string
   */
  public $title;

  /**
   * Optional. The path to the directory containing the Model artifact and any
   * of its supporting files.
   *
   * @param string $artifactUri
   */
  public function setArtifactUri($artifactUri)
  {
    $this->artifactUri = $artifactUri;
  }
  /**
   * @return string
   */
  public function getArtifactUri()
  {
    return $this->artifactUri;
  }
  /**
   * A description of resources that to large degree are decided by Vertex AI,
   * and require only a modest additional configuration.
   *
   * @param GoogleCloudAiplatformV1AutomaticResources $automaticResources
   */
  public function setAutomaticResources(GoogleCloudAiplatformV1AutomaticResources $automaticResources)
  {
    $this->automaticResources = $automaticResources;
  }
  /**
   * @return GoogleCloudAiplatformV1AutomaticResources
   */
  public function getAutomaticResources()
  {
    return $this->automaticResources;
  }
  /**
   * Optional. The specification of the container that is to be used when
   * deploying this Model in Vertex AI. Not present for Large Models.
   *
   * @param GoogleCloudAiplatformV1ModelContainerSpec $containerSpec
   */
  public function setContainerSpec(GoogleCloudAiplatformV1ModelContainerSpec $containerSpec)
  {
    $this->containerSpec = $containerSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelContainerSpec
   */
  public function getContainerSpec()
  {
    return $this->containerSpec;
  }
  /**
   * A description of resources that are dedicated to the DeployedModel, and
   * that need a higher degree of manual configuration.
   *
   * @param GoogleCloudAiplatformV1DedicatedResources $dedicatedResources
   */
  public function setDedicatedResources(GoogleCloudAiplatformV1DedicatedResources $dedicatedResources)
  {
    $this->dedicatedResources = $dedicatedResources;
  }
  /**
   * @return GoogleCloudAiplatformV1DedicatedResources
   */
  public function getDedicatedResources()
  {
    return $this->dedicatedResources;
  }
  /**
   * Optional. Metadata information about this deployment config.
   *
   * @param GoogleCloudAiplatformV1PublisherModelCallToActionDeployDeployMetadata $deployMetadata
   */
  public function setDeployMetadata(GoogleCloudAiplatformV1PublisherModelCallToActionDeployDeployMetadata $deployMetadata)
  {
    $this->deployMetadata = $deployMetadata;
  }
  /**
   * @return GoogleCloudAiplatformV1PublisherModelCallToActionDeployDeployMetadata
   */
  public function getDeployMetadata()
  {
    return $this->deployMetadata;
  }
  /**
   * Optional. The name of the deploy task (e.g., "text to image generation").
   *
   * @param string $deployTaskName
   */
  public function setDeployTaskName($deployTaskName)
  {
    $this->deployTaskName = $deployTaskName;
  }
  /**
   * @return string
   */
  public function getDeployTaskName()
  {
    return $this->deployTaskName;
  }
  /**
   * Optional. Large model reference. When this is set, model_artifact_spec is
   * not needed.
   *
   * @param GoogleCloudAiplatformV1LargeModelReference $largeModelReference
   */
  public function setLargeModelReference(GoogleCloudAiplatformV1LargeModelReference $largeModelReference)
  {
    $this->largeModelReference = $largeModelReference;
  }
  /**
   * @return GoogleCloudAiplatformV1LargeModelReference
   */
  public function getLargeModelReference()
  {
    return $this->largeModelReference;
  }
  /**
   * Optional. Default model display name.
   *
   * @param string $modelDisplayName
   */
  public function setModelDisplayName($modelDisplayName)
  {
    $this->modelDisplayName = $modelDisplayName;
  }
  /**
   * @return string
   */
  public function getModelDisplayName()
  {
    return $this->modelDisplayName;
  }
  /**
   * Optional. The signed URI for ephemeral Cloud Storage access to model
   * artifact.
   *
   * @param string $publicArtifactUri
   */
  public function setPublicArtifactUri($publicArtifactUri)
  {
    $this->publicArtifactUri = $publicArtifactUri;
  }
  /**
   * @return string
   */
  public function getPublicArtifactUri()
  {
    return $this->publicArtifactUri;
  }
  /**
   * The resource name of the shared DeploymentResourcePool to deploy on.
   * Format: `projects/{project}/locations/{location}/deploymentResourcePools/{d
   * eployment_resource_pool}`
   *
   * @param string $sharedResources
   */
  public function setSharedResources($sharedResources)
  {
    $this->sharedResources = $sharedResources;
  }
  /**
   * @return string
   */
  public function getSharedResources()
  {
    return $this->sharedResources;
  }
  /**
   * Required. The title of the regional resource reference.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PublisherModelCallToActionDeploy::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PublisherModelCallToActionDeploy');
