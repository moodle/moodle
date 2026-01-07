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

class GoogleCloudDialogflowCxV3beta1FormParameter extends \Google\Model
{
  protected $advancedSettingsType = GoogleCloudDialogflowCxV3beta1AdvancedSettings::class;
  protected $advancedSettingsDataType = '';
  /**
   * The default value of an optional parameter. If the parameter is required,
   * the default value will be ignored.
   *
   * @var array
   */
  public $defaultValue;
  /**
   * Required. The human-readable name of the parameter, unique within the form.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. The entity type of the parameter. Format:
   * `projects/-/locations/-/agents/-/entityTypes/` for system entity types (for
   * example, `projects/-/locations/-/agents/-/entityTypes/sys.date`), or
   * `projects//locations//agents//entityTypes/` for developer entity types.
   *
   * @var string
   */
  public $entityType;
  protected $fillBehaviorType = GoogleCloudDialogflowCxV3beta1FormParameterFillBehavior::class;
  protected $fillBehaviorDataType = '';
  /**
   * Indicates whether the parameter represents a list of values.
   *
   * @var bool
   */
  public $isList;
  /**
   * Indicates whether the parameter content should be redacted in log. If
   * redaction is enabled, the parameter content will be replaced by parameter
   * name during logging. Note: the parameter content is subject to redaction if
   * either parameter level redaction or entity type level redaction is enabled.
   *
   * @var bool
   */
  public $redact;
  /**
   * Indicates whether the parameter is required. Optional parameters will not
   * trigger prompts; however, they are filled if the user specifies them.
   * Required parameters must be filled before form filling concludes.
   *
   * @var bool
   */
  public $required;

  /**
   * Hierarchical advanced settings for this parameter. The settings exposed at
   * the lower level overrides the settings exposed at the higher level.
   *
   * @param GoogleCloudDialogflowCxV3beta1AdvancedSettings $advancedSettings
   */
  public function setAdvancedSettings(GoogleCloudDialogflowCxV3beta1AdvancedSettings $advancedSettings)
  {
    $this->advancedSettings = $advancedSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1AdvancedSettings
   */
  public function getAdvancedSettings()
  {
    return $this->advancedSettings;
  }
  /**
   * The default value of an optional parameter. If the parameter is required,
   * the default value will be ignored.
   *
   * @param array $defaultValue
   */
  public function setDefaultValue($defaultValue)
  {
    $this->defaultValue = $defaultValue;
  }
  /**
   * @return array
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }
  /**
   * Required. The human-readable name of the parameter, unique within the form.
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
   * Required. The entity type of the parameter. Format:
   * `projects/-/locations/-/agents/-/entityTypes/` for system entity types (for
   * example, `projects/-/locations/-/agents/-/entityTypes/sys.date`), or
   * `projects//locations//agents//entityTypes/` for developer entity types.
   *
   * @param string $entityType
   */
  public function setEntityType($entityType)
  {
    $this->entityType = $entityType;
  }
  /**
   * @return string
   */
  public function getEntityType()
  {
    return $this->entityType;
  }
  /**
   * Required. Defines fill behavior for the parameter.
   *
   * @param GoogleCloudDialogflowCxV3beta1FormParameterFillBehavior $fillBehavior
   */
  public function setFillBehavior(GoogleCloudDialogflowCxV3beta1FormParameterFillBehavior $fillBehavior)
  {
    $this->fillBehavior = $fillBehavior;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1FormParameterFillBehavior
   */
  public function getFillBehavior()
  {
    return $this->fillBehavior;
  }
  /**
   * Indicates whether the parameter represents a list of values.
   *
   * @param bool $isList
   */
  public function setIsList($isList)
  {
    $this->isList = $isList;
  }
  /**
   * @return bool
   */
  public function getIsList()
  {
    return $this->isList;
  }
  /**
   * Indicates whether the parameter content should be redacted in log. If
   * redaction is enabled, the parameter content will be replaced by parameter
   * name during logging. Note: the parameter content is subject to redaction if
   * either parameter level redaction or entity type level redaction is enabled.
   *
   * @param bool $redact
   */
  public function setRedact($redact)
  {
    $this->redact = $redact;
  }
  /**
   * @return bool
   */
  public function getRedact()
  {
    return $this->redact;
  }
  /**
   * Indicates whether the parameter is required. Optional parameters will not
   * trigger prompts; however, they are filled if the user specifies them.
   * Required parameters must be filled before form filling concludes.
   *
   * @param bool $required
   */
  public function setRequired($required)
  {
    $this->required = $required;
  }
  /**
   * @return bool
   */
  public function getRequired()
  {
    return $this->required;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1FormParameter::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1FormParameter');
