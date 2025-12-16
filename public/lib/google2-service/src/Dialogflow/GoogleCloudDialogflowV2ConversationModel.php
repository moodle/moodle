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

class GoogleCloudDialogflowV2ConversationModel extends \Google\Collection
{
  /**
   * Should not be used, an un-set enum has this value by default.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Model being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Model is not deployed but ready to deploy.
   */
  public const STATE_UNDEPLOYED = 'UNDEPLOYED';
  /**
   * Model is deploying.
   */
  public const STATE_DEPLOYING = 'DEPLOYING';
  /**
   * Model is deployed and ready to use.
   */
  public const STATE_DEPLOYED = 'DEPLOYED';
  /**
   * Model is undeploying.
   */
  public const STATE_UNDEPLOYING = 'UNDEPLOYING';
  /**
   * Model is deleting.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Model is in error state. Not ready to deploy and use.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Model is being created but the training has not started, The model may
   * remain in this state until there is enough capacity to start training.
   */
  public const STATE_PENDING = 'PENDING';
  protected $collection_key = 'datasets';
  protected $articleSuggestionModelMetadataType = GoogleCloudDialogflowV2ArticleSuggestionModelMetadata::class;
  protected $articleSuggestionModelMetadataDataType = '';
  /**
   * Output only. Creation time of this model.
   *
   * @var string
   */
  public $createTime;
  protected $datasetsType = GoogleCloudDialogflowV2InputDataset::class;
  protected $datasetsDataType = 'array';
  /**
   * Required. The display name of the model. At most 64 bytes long.
   *
   * @var string
   */
  public $displayName;
  /**
   * Language code for the conversation model. If not specified, the language is
   * en-US. Language at ConversationModel should be set for all non en-us
   * languages. This should be a [BCP-47](https://www.rfc-
   * editor.org/rfc/bcp/bcp47.txt) language tag. Example: "en-US".
   *
   * @var string
   */
  public $languageCode;
  /**
   * ConversationModel resource name. Format: `projects//conversationModels/`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. A read only boolean field reflecting Zone Isolation status of
   * the model.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. A read only boolean field reflecting Zone Separation status of
   * the model.
   *
   * @var bool
   */
  public $satisfiesPzs;
  protected $smartReplyModelMetadataType = GoogleCloudDialogflowV2SmartReplyModelMetadata::class;
  protected $smartReplyModelMetadataDataType = '';
  /**
   * Output only. State of the model. A model can only serve prediction requests
   * after it gets deployed.
   *
   * @var string
   */
  public $state;

  /**
   * Metadata for article suggestion models.
   *
   * @param GoogleCloudDialogflowV2ArticleSuggestionModelMetadata $articleSuggestionModelMetadata
   */
  public function setArticleSuggestionModelMetadata(GoogleCloudDialogflowV2ArticleSuggestionModelMetadata $articleSuggestionModelMetadata)
  {
    $this->articleSuggestionModelMetadata = $articleSuggestionModelMetadata;
  }
  /**
   * @return GoogleCloudDialogflowV2ArticleSuggestionModelMetadata
   */
  public function getArticleSuggestionModelMetadata()
  {
    return $this->articleSuggestionModelMetadata;
  }
  /**
   * Output only. Creation time of this model.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Required. Datasets used to create model.
   *
   * @param GoogleCloudDialogflowV2InputDataset[] $datasets
   */
  public function setDatasets($datasets)
  {
    $this->datasets = $datasets;
  }
  /**
   * @return GoogleCloudDialogflowV2InputDataset[]
   */
  public function getDatasets()
  {
    return $this->datasets;
  }
  /**
   * Required. The display name of the model. At most 64 bytes long.
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
   * Language code for the conversation model. If not specified, the language is
   * en-US. Language at ConversationModel should be set for all non en-us
   * languages. This should be a [BCP-47](https://www.rfc-
   * editor.org/rfc/bcp/bcp47.txt) language tag. Example: "en-US".
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * ConversationModel resource name. Format: `projects//conversationModels/`
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
   * Output only. A read only boolean field reflecting Zone Isolation status of
   * the model.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. A read only boolean field reflecting Zone Separation status of
   * the model.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Metadata for smart reply models.
   *
   * @param GoogleCloudDialogflowV2SmartReplyModelMetadata $smartReplyModelMetadata
   */
  public function setSmartReplyModelMetadata(GoogleCloudDialogflowV2SmartReplyModelMetadata $smartReplyModelMetadata)
  {
    $this->smartReplyModelMetadata = $smartReplyModelMetadata;
  }
  /**
   * @return GoogleCloudDialogflowV2SmartReplyModelMetadata
   */
  public function getSmartReplyModelMetadata()
  {
    return $this->smartReplyModelMetadata;
  }
  /**
   * Output only. State of the model. A model can only serve prediction requests
   * after it gets deployed.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, UNDEPLOYED, DEPLOYING,
   * DEPLOYED, UNDEPLOYING, DELETING, FAILED, PENDING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2ConversationModel::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2ConversationModel');
