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

class GoogleCloudDialogflowCxV3AdvancedSettings extends \Google\Model
{
  protected $audioExportGcsDestinationType = GoogleCloudDialogflowCxV3GcsDestination::class;
  protected $audioExportGcsDestinationDataType = '';
  protected $dtmfSettingsType = GoogleCloudDialogflowCxV3AdvancedSettingsDtmfSettings::class;
  protected $dtmfSettingsDataType = '';
  protected $loggingSettingsType = GoogleCloudDialogflowCxV3AdvancedSettingsLoggingSettings::class;
  protected $loggingSettingsDataType = '';
  protected $speechSettingsType = GoogleCloudDialogflowCxV3AdvancedSettingsSpeechSettings::class;
  protected $speechSettingsDataType = '';

  /**
   * If present, incoming audio is exported by Dialogflow to the configured
   * Google Cloud Storage destination. Exposed at the following levels: - Agent
   * level - Flow level
   *
   * @param GoogleCloudDialogflowCxV3GcsDestination $audioExportGcsDestination
   */
  public function setAudioExportGcsDestination(GoogleCloudDialogflowCxV3GcsDestination $audioExportGcsDestination)
  {
    $this->audioExportGcsDestination = $audioExportGcsDestination;
  }
  /**
   * @return GoogleCloudDialogflowCxV3GcsDestination
   */
  public function getAudioExportGcsDestination()
  {
    return $this->audioExportGcsDestination;
  }
  /**
   * Settings for DTMF. Exposed at the following levels: - Agent level - Flow
   * level - Page level - Parameter level.
   *
   * @param GoogleCloudDialogflowCxV3AdvancedSettingsDtmfSettings $dtmfSettings
   */
  public function setDtmfSettings(GoogleCloudDialogflowCxV3AdvancedSettingsDtmfSettings $dtmfSettings)
  {
    $this->dtmfSettings = $dtmfSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3AdvancedSettingsDtmfSettings
   */
  public function getDtmfSettings()
  {
    return $this->dtmfSettings;
  }
  /**
   * Settings for logging. Settings for Dialogflow History, Contact Center
   * messages, StackDriver logs, and speech logging. Exposed at the following
   * levels: - Agent level.
   *
   * @param GoogleCloudDialogflowCxV3AdvancedSettingsLoggingSettings $loggingSettings
   */
  public function setLoggingSettings(GoogleCloudDialogflowCxV3AdvancedSettingsLoggingSettings $loggingSettings)
  {
    $this->loggingSettings = $loggingSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3AdvancedSettingsLoggingSettings
   */
  public function getLoggingSettings()
  {
    return $this->loggingSettings;
  }
  /**
   * Settings for speech to text detection. Exposed at the following levels: -
   * Agent level - Flow level - Page level - Parameter level
   *
   * @param GoogleCloudDialogflowCxV3AdvancedSettingsSpeechSettings $speechSettings
   */
  public function setSpeechSettings(GoogleCloudDialogflowCxV3AdvancedSettingsSpeechSettings $speechSettings)
  {
    $this->speechSettings = $speechSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3AdvancedSettingsSpeechSettings
   */
  public function getSpeechSettings()
  {
    return $this->speechSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3AdvancedSettings::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3AdvancedSettings');
