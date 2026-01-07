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

class GoogleCloudAiplatformV1PublisherModel extends \Google\Collection
{
  /**
   * The model launch stage is unspecified.
   */
  public const LAUNCH_STAGE_LAUNCH_STAGE_UNSPECIFIED = 'LAUNCH_STAGE_UNSPECIFIED';
  /**
   * Used to indicate the PublisherModel is at Experimental launch stage,
   * available to a small set of customers.
   */
  public const LAUNCH_STAGE_EXPERIMENTAL = 'EXPERIMENTAL';
  /**
   * Used to indicate the PublisherModel is at Private Preview launch stage,
   * only available to a small set of customers, although a larger set of
   * customers than an Experimental launch. Previews are the first launch stage
   * used to get feedback from customers.
   */
  public const LAUNCH_STAGE_PRIVATE_PREVIEW = 'PRIVATE_PREVIEW';
  /**
   * Used to indicate the PublisherModel is at Public Preview launch stage,
   * available to all customers, although not supported for production
   * workloads.
   */
  public const LAUNCH_STAGE_PUBLIC_PREVIEW = 'PUBLIC_PREVIEW';
  /**
   * Used to indicate the PublisherModel is at GA launch stage, available to all
   * customers and ready for production workload.
   */
  public const LAUNCH_STAGE_GA = 'GA';
  /**
   * The open source category is unspecified, which should not be used.
   */
  public const OPEN_SOURCE_CATEGORY_OPEN_SOURCE_CATEGORY_UNSPECIFIED = 'OPEN_SOURCE_CATEGORY_UNSPECIFIED';
  /**
   * Used to indicate the PublisherModel is not open sourced.
   */
  public const OPEN_SOURCE_CATEGORY_PROPRIETARY = 'PROPRIETARY';
  /**
   * Used to indicate the PublisherModel is a Google-owned open source model w/
   * Google checkpoint.
   */
  public const OPEN_SOURCE_CATEGORY_GOOGLE_OWNED_OSS_WITH_GOOGLE_CHECKPOINT = 'GOOGLE_OWNED_OSS_WITH_GOOGLE_CHECKPOINT';
  /**
   * Used to indicate the PublisherModel is a 3p-owned open source model w/
   * Google checkpoint.
   */
  public const OPEN_SOURCE_CATEGORY_THIRD_PARTY_OWNED_OSS_WITH_GOOGLE_CHECKPOINT = 'THIRD_PARTY_OWNED_OSS_WITH_GOOGLE_CHECKPOINT';
  /**
   * Used to indicate the PublisherModel is a Google-owned pure open source
   * model.
   */
  public const OPEN_SOURCE_CATEGORY_GOOGLE_OWNED_OSS = 'GOOGLE_OWNED_OSS';
  /**
   * Used to indicate the PublisherModel is a 3p-owned pure open source model.
   */
  public const OPEN_SOURCE_CATEGORY_THIRD_PARTY_OWNED_OSS = 'THIRD_PARTY_OWNED_OSS';
  /**
   * The version state is unspecified.
   */
  public const VERSION_STATE_VERSION_STATE_UNSPECIFIED = 'VERSION_STATE_UNSPECIFIED';
  /**
   * Used to indicate the version is stable.
   */
  public const VERSION_STATE_VERSION_STATE_STABLE = 'VERSION_STATE_STABLE';
  /**
   * Used to indicate the version is unstable.
   */
  public const VERSION_STATE_VERSION_STATE_UNSTABLE = 'VERSION_STATE_UNSTABLE';
  protected $collection_key = 'frameworks';
  /**
   * Optional. Additional information about the model's Frameworks.
   *
   * @var string[]
   */
  public $frameworks;
  /**
   * Optional. Indicates the launch stage of the model.
   *
   * @var string
   */
  public $launchStage;
  /**
   * Output only. The resource name of the PublisherModel.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Indicates the open source category of the publisher model.
   *
   * @var string
   */
  public $openSourceCategory;
  protected $predictSchemataType = GoogleCloudAiplatformV1PredictSchemata::class;
  protected $predictSchemataDataType = '';
  /**
   * Optional. Output only. Immutable. Used to indicate this model has a
   * publisher model and provide the template of the publisher model resource
   * name.
   *
   * @var string
   */
  public $publisherModelTemplate;
  protected $supportedActionsType = GoogleCloudAiplatformV1PublisherModelCallToAction::class;
  protected $supportedActionsDataType = '';
  /**
   * Output only. Immutable. The version ID of the PublisherModel. A new version
   * is committed when a new model version is uploaded under an existing model
   * id. It is an auto-incrementing decimal number in string representation.
   *
   * @var string
   */
  public $versionId;
  /**
   * Optional. Indicates the state of the model version.
   *
   * @var string
   */
  public $versionState;

  /**
   * Optional. Additional information about the model's Frameworks.
   *
   * @param string[] $frameworks
   */
  public function setFrameworks($frameworks)
  {
    $this->frameworks = $frameworks;
  }
  /**
   * @return string[]
   */
  public function getFrameworks()
  {
    return $this->frameworks;
  }
  /**
   * Optional. Indicates the launch stage of the model.
   *
   * Accepted values: LAUNCH_STAGE_UNSPECIFIED, EXPERIMENTAL, PRIVATE_PREVIEW,
   * PUBLIC_PREVIEW, GA
   *
   * @param self::LAUNCH_STAGE_* $launchStage
   */
  public function setLaunchStage($launchStage)
  {
    $this->launchStage = $launchStage;
  }
  /**
   * @return self::LAUNCH_STAGE_*
   */
  public function getLaunchStage()
  {
    return $this->launchStage;
  }
  /**
   * Output only. The resource name of the PublisherModel.
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
   * Required. Indicates the open source category of the publisher model.
   *
   * Accepted values: OPEN_SOURCE_CATEGORY_UNSPECIFIED, PROPRIETARY,
   * GOOGLE_OWNED_OSS_WITH_GOOGLE_CHECKPOINT,
   * THIRD_PARTY_OWNED_OSS_WITH_GOOGLE_CHECKPOINT, GOOGLE_OWNED_OSS,
   * THIRD_PARTY_OWNED_OSS
   *
   * @param self::OPEN_SOURCE_CATEGORY_* $openSourceCategory
   */
  public function setOpenSourceCategory($openSourceCategory)
  {
    $this->openSourceCategory = $openSourceCategory;
  }
  /**
   * @return self::OPEN_SOURCE_CATEGORY_*
   */
  public function getOpenSourceCategory()
  {
    return $this->openSourceCategory;
  }
  /**
   * Optional. The schemata that describes formats of the PublisherModel's
   * predictions and explanations as given and returned via
   * PredictionService.Predict.
   *
   * @param GoogleCloudAiplatformV1PredictSchemata $predictSchemata
   */
  public function setPredictSchemata(GoogleCloudAiplatformV1PredictSchemata $predictSchemata)
  {
    $this->predictSchemata = $predictSchemata;
  }
  /**
   * @return GoogleCloudAiplatformV1PredictSchemata
   */
  public function getPredictSchemata()
  {
    return $this->predictSchemata;
  }
  /**
   * Optional. Output only. Immutable. Used to indicate this model has a
   * publisher model and provide the template of the publisher model resource
   * name.
   *
   * @param string $publisherModelTemplate
   */
  public function setPublisherModelTemplate($publisherModelTemplate)
  {
    $this->publisherModelTemplate = $publisherModelTemplate;
  }
  /**
   * @return string
   */
  public function getPublisherModelTemplate()
  {
    return $this->publisherModelTemplate;
  }
  /**
   * Optional. Supported call-to-action options.
   *
   * @param GoogleCloudAiplatformV1PublisherModelCallToAction $supportedActions
   */
  public function setSupportedActions(GoogleCloudAiplatformV1PublisherModelCallToAction $supportedActions)
  {
    $this->supportedActions = $supportedActions;
  }
  /**
   * @return GoogleCloudAiplatformV1PublisherModelCallToAction
   */
  public function getSupportedActions()
  {
    return $this->supportedActions;
  }
  /**
   * Output only. Immutable. The version ID of the PublisherModel. A new version
   * is committed when a new model version is uploaded under an existing model
   * id. It is an auto-incrementing decimal number in string representation.
   *
   * @param string $versionId
   */
  public function setVersionId($versionId)
  {
    $this->versionId = $versionId;
  }
  /**
   * @return string
   */
  public function getVersionId()
  {
    return $this->versionId;
  }
  /**
   * Optional. Indicates the state of the model version.
   *
   * Accepted values: VERSION_STATE_UNSPECIFIED, VERSION_STATE_STABLE,
   * VERSION_STATE_UNSTABLE
   *
   * @param self::VERSION_STATE_* $versionState
   */
  public function setVersionState($versionState)
  {
    $this->versionState = $versionState;
  }
  /**
   * @return self::VERSION_STATE_*
   */
  public function getVersionState()
  {
    return $this->versionState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PublisherModel::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PublisherModel');
