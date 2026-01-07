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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2AiModel extends \Google\Model
{
  /**
   * Unspecified deployment platform.
   */
  public const DEPLOYMENT_PLATFORM_DEPLOYMENT_PLATFORM_UNSPECIFIED = 'DEPLOYMENT_PLATFORM_UNSPECIFIED';
  /**
   * Vertex AI.
   */
  public const DEPLOYMENT_PLATFORM_VERTEX_AI = 'VERTEX_AI';
  /**
   * Google Kubernetes Engine.
   */
  public const DEPLOYMENT_PLATFORM_GKE = 'GKE';
  /**
   * Google Compute Engine.
   */
  public const DEPLOYMENT_PLATFORM_GCE = 'GCE';
  /**
   * Fine tuned model.
   */
  public const DEPLOYMENT_PLATFORM_FINE_TUNED_MODEL = 'FINE_TUNED_MODEL';
  /**
   * The platform on which the model is deployed.
   *
   * @var string
   */
  public $deploymentPlatform;
  /**
   * The user defined display name of model. Ex. baseline-classification-model
   *
   * @var string
   */
  public $displayName;
  /**
   * The domain of the model, for example, “image-classification”.
   *
   * @var string
   */
  public $domain;
  /**
   * The name of the model library, for example, “transformers”.
   *
   * @var string
   */
  public $library;
  /**
   * The region in which the model is used, for example, “us-central1”.
   *
   * @var string
   */
  public $location;
  /**
   * The name of the AI model, for example, "gemini:1.0.0".
   *
   * @var string
   */
  public $name;
  /**
   * The publisher of the model, for example, “google” or “nvidia”.
   *
   * @var string
   */
  public $publisher;
  /**
   * The purpose of the model, for example, "Inteference" or "Training".
   *
   * @var string
   */
  public $usageCategory;

  /**
   * The platform on which the model is deployed.
   *
   * Accepted values: DEPLOYMENT_PLATFORM_UNSPECIFIED, VERTEX_AI, GKE, GCE,
   * FINE_TUNED_MODEL
   *
   * @param self::DEPLOYMENT_PLATFORM_* $deploymentPlatform
   */
  public function setDeploymentPlatform($deploymentPlatform)
  {
    $this->deploymentPlatform = $deploymentPlatform;
  }
  /**
   * @return self::DEPLOYMENT_PLATFORM_*
   */
  public function getDeploymentPlatform()
  {
    return $this->deploymentPlatform;
  }
  /**
   * The user defined display name of model. Ex. baseline-classification-model
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The domain of the model, for example, “image-classification”.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * The name of the model library, for example, “transformers”.
   *
   * @param string $library
   */
  public function setLibrary($library)
  {
    $this->library = $library;
  }
  /**
   * @return string
   */
  public function getLibrary()
  {
    return $this->library;
  }
  /**
   * The region in which the model is used, for example, “us-central1”.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The name of the AI model, for example, "gemini:1.0.0".
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
   * The publisher of the model, for example, “google” or “nvidia”.
   *
   * @param string $publisher
   */
  public function setPublisher($publisher)
  {
    $this->publisher = $publisher;
  }
  /**
   * @return string
   */
  public function getPublisher()
  {
    return $this->publisher;
  }
  /**
   * The purpose of the model, for example, "Inteference" or "Training".
   *
   * @param string $usageCategory
   */
  public function setUsageCategory($usageCategory)
  {
    $this->usageCategory = $usageCategory;
  }
  /**
   * @return string
   */
  public function getUsageCategory()
  {
    return $this->usageCategory;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2AiModel::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2AiModel');
