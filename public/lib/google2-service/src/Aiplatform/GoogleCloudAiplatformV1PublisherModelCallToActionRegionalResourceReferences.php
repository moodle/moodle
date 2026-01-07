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

class GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences extends \Google\Model
{
  /**
   * Optional. For notebook resource. When set to true, the Colab Enterprise
   * link will be disabled in the "open notebook" dialog in UI.
   *
   * @var bool
   */
  public $colabNotebookDisabled;
  protected $referencesType = GoogleCloudAiplatformV1PublisherModelResourceReference::class;
  protected $referencesDataType = 'map';
  /**
   * Optional. Description of the resource.
   *
   * @var string
   */
  public $resourceDescription;
  /**
   * Optional. Title of the resource.
   *
   * @var string
   */
  public $resourceTitle;
  /**
   * Optional. Use case (CUJ) of the resource.
   *
   * @var string
   */
  public $resourceUseCase;
  /**
   * Optional. For notebook resource, whether the notebook supports Workbench.
   *
   * @var bool
   */
  public $supportsWorkbench;
  /**
   * Required.
   *
   * @var string
   */
  public $title;

  /**
   * Optional. For notebook resource. When set to true, the Colab Enterprise
   * link will be disabled in the "open notebook" dialog in UI.
   *
   * @param bool $colabNotebookDisabled
   */
  public function setColabNotebookDisabled($colabNotebookDisabled)
  {
    $this->colabNotebookDisabled = $colabNotebookDisabled;
  }
  /**
   * @return bool
   */
  public function getColabNotebookDisabled()
  {
    return $this->colabNotebookDisabled;
  }
  /**
   * Required.
   *
   * @param GoogleCloudAiplatformV1PublisherModelResourceReference[] $references
   */
  public function setReferences($references)
  {
    $this->references = $references;
  }
  /**
   * @return GoogleCloudAiplatformV1PublisherModelResourceReference[]
   */
  public function getReferences()
  {
    return $this->references;
  }
  /**
   * Optional. Description of the resource.
   *
   * @param string $resourceDescription
   */
  public function setResourceDescription($resourceDescription)
  {
    $this->resourceDescription = $resourceDescription;
  }
  /**
   * @return string
   */
  public function getResourceDescription()
  {
    return $this->resourceDescription;
  }
  /**
   * Optional. Title of the resource.
   *
   * @param string $resourceTitle
   */
  public function setResourceTitle($resourceTitle)
  {
    $this->resourceTitle = $resourceTitle;
  }
  /**
   * @return string
   */
  public function getResourceTitle()
  {
    return $this->resourceTitle;
  }
  /**
   * Optional. Use case (CUJ) of the resource.
   *
   * @param string $resourceUseCase
   */
  public function setResourceUseCase($resourceUseCase)
  {
    $this->resourceUseCase = $resourceUseCase;
  }
  /**
   * @return string
   */
  public function getResourceUseCase()
  {
    return $this->resourceUseCase;
  }
  /**
   * Optional. For notebook resource, whether the notebook supports Workbench.
   *
   * @param bool $supportsWorkbench
   */
  public function setSupportsWorkbench($supportsWorkbench)
  {
    $this->supportsWorkbench = $supportsWorkbench;
  }
  /**
   * @return bool
   */
  public function getSupportsWorkbench()
  {
    return $this->supportsWorkbench;
  }
  /**
   * Required.
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
class_alias(GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PublisherModelCallToActionRegionalResourceReferences');
