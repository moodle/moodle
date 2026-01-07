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

class GoogleCloudDialogflowCxV3beta1Intent extends \Google\Collection
{
  protected $collection_key = 'trainingPhrases';
  /**
   * Human readable description for better understanding an intent like its
   * scope, content, result etc. Maximum character limit: 140 characters.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The human-readable name of the intent, unique within the agent.
   *
   * @var string
   */
  public $displayName;
  /**
   * Indicates whether this is a fallback intent. Currently only default
   * fallback intent is allowed in the agent, which is added upon agent
   * creation. Adding training phrases to fallback intent is useful in the case
   * of requests that are mistakenly matched, since training phrases assigned to
   * fallback intents act as negative examples that triggers no-match event.
   *
   * @var bool
   */
  public $isFallback;
  /**
   * The key/value metadata to label an intent. Labels can contain lowercase
   * letters, digits and the symbols '-' and '_'. International characters are
   * allowed, including letters from unicase alphabets. Keys must start with a
   * letter. Keys and values can be no longer than 63 characters and no more
   * than 128 bytes. Prefix "sys-" is reserved for Dialogflow defined labels.
   * Currently allowed Dialogflow defined labels include: * sys-head * sys-
   * contextual The above labels do not require value. "sys-head" means the
   * intent is a head intent. "sys-contextual" means the intent is a contextual
   * intent.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The unique identifier of the intent. Required for the Intents.UpdateIntent
   * method. Intents.CreateIntent populates the name automatically. Format:
   * `projects//locations//agents//intents/`.
   *
   * @var string
   */
  public $name;
  protected $parametersType = GoogleCloudDialogflowCxV3beta1IntentParameter::class;
  protected $parametersDataType = 'array';
  /**
   * The priority of this intent. Higher numbers represent higher priorities. -
   * If the supplied value is unspecified or 0, the service translates the value
   * to 500,000, which corresponds to the `Normal` priority in the console. - If
   * the supplied value is negative, the intent is ignored in runtime detect
   * intent requests.
   *
   * @var int
   */
  public $priority;
  protected $trainingPhrasesType = GoogleCloudDialogflowCxV3beta1IntentTrainingPhrase::class;
  protected $trainingPhrasesDataType = 'array';

  /**
   * Human readable description for better understanding an intent like its
   * scope, content, result etc. Maximum character limit: 140 characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The human-readable name of the intent, unique within the agent.
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
   * Indicates whether this is a fallback intent. Currently only default
   * fallback intent is allowed in the agent, which is added upon agent
   * creation. Adding training phrases to fallback intent is useful in the case
   * of requests that are mistakenly matched, since training phrases assigned to
   * fallback intents act as negative examples that triggers no-match event.
   *
   * @param bool $isFallback
   */
  public function setIsFallback($isFallback)
  {
    $this->isFallback = $isFallback;
  }
  /**
   * @return bool
   */
  public function getIsFallback()
  {
    return $this->isFallback;
  }
  /**
   * The key/value metadata to label an intent. Labels can contain lowercase
   * letters, digits and the symbols '-' and '_'. International characters are
   * allowed, including letters from unicase alphabets. Keys must start with a
   * letter. Keys and values can be no longer than 63 characters and no more
   * than 128 bytes. Prefix "sys-" is reserved for Dialogflow defined labels.
   * Currently allowed Dialogflow defined labels include: * sys-head * sys-
   * contextual The above labels do not require value. "sys-head" means the
   * intent is a head intent. "sys-contextual" means the intent is a contextual
   * intent.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The unique identifier of the intent. Required for the Intents.UpdateIntent
   * method. Intents.CreateIntent populates the name automatically. Format:
   * `projects//locations//agents//intents/`.
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
   * The collection of parameters associated with the intent.
   *
   * @param GoogleCloudDialogflowCxV3beta1IntentParameter[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1IntentParameter[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * The priority of this intent. Higher numbers represent higher priorities. -
   * If the supplied value is unspecified or 0, the service translates the value
   * to 500,000, which corresponds to the `Normal` priority in the console. - If
   * the supplied value is negative, the intent is ignored in runtime detect
   * intent requests.
   *
   * @param int $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return int
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * The collection of training phrases the agent is trained on to identify the
   * intent.
   *
   * @param GoogleCloudDialogflowCxV3beta1IntentTrainingPhrase[] $trainingPhrases
   */
  public function setTrainingPhrases($trainingPhrases)
  {
    $this->trainingPhrases = $trainingPhrases;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1IntentTrainingPhrase[]
   */
  public function getTrainingPhrases()
  {
    return $this->trainingPhrases;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1Intent::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1Intent');
