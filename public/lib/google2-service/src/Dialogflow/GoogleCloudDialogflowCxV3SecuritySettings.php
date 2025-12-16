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

class GoogleCloudDialogflowCxV3SecuritySettings extends \Google\Collection
{
  /**
   * Don't redact any kind of data.
   */
  public const REDACTION_SCOPE_REDACTION_SCOPE_UNSPECIFIED = 'REDACTION_SCOPE_UNSPECIFIED';
  /**
   * On data to be written to disk or similar devices that are capable of
   * holding data even if power is disconnected. This includes data that are
   * temporarily saved on disk.
   */
  public const REDACTION_SCOPE_REDACT_DISK_STORAGE = 'REDACT_DISK_STORAGE';
  /**
   * Do not redact.
   */
  public const REDACTION_STRATEGY_REDACTION_STRATEGY_UNSPECIFIED = 'REDACTION_STRATEGY_UNSPECIFIED';
  /**
   * Call redaction service to clean up the data to be persisted.
   */
  public const REDACTION_STRATEGY_REDACT_WITH_SERVICE = 'REDACT_WITH_SERVICE';
  /**
   * Retains the persisted data with Dialogflow's internal default 365d TTLs.
   */
  public const RETENTION_STRATEGY_RETENTION_STRATEGY_UNSPECIFIED = 'RETENTION_STRATEGY_UNSPECIFIED';
  /**
   * Removes data when the conversation ends. If there is no Conversation
   * explicitly established, a default conversation ends when the corresponding
   * Dialogflow session ends.
   */
  public const RETENTION_STRATEGY_REMOVE_AFTER_CONVERSATION = 'REMOVE_AFTER_CONVERSATION';
  protected $collection_key = 'purgeDataTypes';
  protected $audioExportSettingsType = GoogleCloudDialogflowCxV3SecuritySettingsAudioExportSettings::class;
  protected $audioExportSettingsDataType = '';
  /**
   * [DLP](https://cloud.google.com/dlp/docs) deidentify template name. Use this
   * template to define de-identification configuration for the content. The
   * `DLP De-identify Templates Reader` role is needed on the Dialogflow service
   * identity service account (has the form `service-PROJECT_NUMBER@gcp-sa-
   * dialogflow.iam.gserviceaccount.com`) for your agent's project. If empty,
   * Dialogflow replaces sensitive info with `[redacted]` text. The template
   * name will have one of the following formats:
   * `projects//locations//deidentifyTemplates/` OR
   * `organizations//locations//deidentifyTemplates/` Note:
   * `deidentify_template` must be located in the same region as the
   * `SecuritySettings`.
   *
   * @var string
   */
  public $deidentifyTemplate;
  /**
   * Required. The human-readable name of the security settings, unique within
   * the location.
   *
   * @var string
   */
  public $displayName;
  protected $insightsExportSettingsType = GoogleCloudDialogflowCxV3SecuritySettingsInsightsExportSettings::class;
  protected $insightsExportSettingsDataType = '';
  /**
   * [DLP](https://cloud.google.com/dlp/docs) inspect template name. Use this
   * template to define inspect base settings. The `DLP Inspect Templates
   * Reader` role is needed on the Dialogflow service identity service account
   * (has the form `service-PROJECT_NUMBER@gcp-sa-
   * dialogflow.iam.gserviceaccount.com`) for your agent's project. If empty, we
   * use the default DLP inspect config. The template name will have one of the
   * following formats: `projects//locations//inspectTemplates/` OR
   * `organizations//locations//inspectTemplates/` Note: `inspect_template` must
   * be located in the same region as the `SecuritySettings`.
   *
   * @var string
   */
  public $inspectTemplate;
  /**
   * Resource name of the settings. Required for the
   * SecuritySettingsService.UpdateSecuritySettings method.
   * SecuritySettingsService.CreateSecuritySettings populates the name
   * automatically. Format: `projects//locations//securitySettings/`.
   *
   * @var string
   */
  public $name;
  /**
   * List of types of data to remove when retention settings triggers purge.
   *
   * @var string[]
   */
  public $purgeDataTypes;
  /**
   * Defines the data for which Dialogflow applies redaction. Dialogflow does
   * not redact data that it does not have access to – for example, Cloud
   * logging.
   *
   * @var string
   */
  public $redactionScope;
  /**
   * Strategy that defines how we do redaction.
   *
   * @var string
   */
  public $redactionStrategy;
  /**
   * Specifies the retention behavior defined by
   * SecuritySettings.RetentionStrategy.
   *
   * @var string
   */
  public $retentionStrategy;
  /**
   * Retains the data for the specified number of days. User must set a value
   * lower than Dialogflow's default 365d TTL (30 days for Agent Assist
   * traffic), higher value will be ignored and use default. Setting a value
   * higher than that has no effect. A missing value or setting to 0 also means
   * we use default TTL. When data retention configuration is changed, it only
   * applies to the data created after the change; the TTL of existing data
   * created before the change stays intact.
   *
   * @var int
   */
  public $retentionWindowDays;

  /**
   * Controls audio export settings for post-conversation analytics when
   * ingesting audio to conversations via Participants.AnalyzeContent or
   * Participants.StreamingAnalyzeContent. If retention_strategy is set to
   * REMOVE_AFTER_CONVERSATION or audio_export_settings.gcs_bucket is empty,
   * audio export is disabled. If audio export is enabled, audio is recorded and
   * saved to audio_export_settings.gcs_bucket, subject to retention policy of
   * audio_export_settings.gcs_bucket. This setting won't effect audio input for
   * implicit sessions via Sessions.DetectIntent or
   * Sessions.StreamingDetectIntent.
   *
   * @param GoogleCloudDialogflowCxV3SecuritySettingsAudioExportSettings $audioExportSettings
   */
  public function setAudioExportSettings(GoogleCloudDialogflowCxV3SecuritySettingsAudioExportSettings $audioExportSettings)
  {
    $this->audioExportSettings = $audioExportSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3SecuritySettingsAudioExportSettings
   */
  public function getAudioExportSettings()
  {
    return $this->audioExportSettings;
  }
  /**
   * [DLP](https://cloud.google.com/dlp/docs) deidentify template name. Use this
   * template to define de-identification configuration for the content. The
   * `DLP De-identify Templates Reader` role is needed on the Dialogflow service
   * identity service account (has the form `service-PROJECT_NUMBER@gcp-sa-
   * dialogflow.iam.gserviceaccount.com`) for your agent's project. If empty,
   * Dialogflow replaces sensitive info with `[redacted]` text. The template
   * name will have one of the following formats:
   * `projects//locations//deidentifyTemplates/` OR
   * `organizations//locations//deidentifyTemplates/` Note:
   * `deidentify_template` must be located in the same region as the
   * `SecuritySettings`.
   *
   * @param string $deidentifyTemplate
   */
  public function setDeidentifyTemplate($deidentifyTemplate)
  {
    $this->deidentifyTemplate = $deidentifyTemplate;
  }
  /**
   * @return string
   */
  public function getDeidentifyTemplate()
  {
    return $this->deidentifyTemplate;
  }
  /**
   * Required. The human-readable name of the security settings, unique within
   * the location.
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
   * Controls conversation exporting settings to Insights after conversation is
   * completed. If retention_strategy is set to REMOVE_AFTER_CONVERSATION,
   * Insights export is disabled no matter what you configure here.
   *
   * @param GoogleCloudDialogflowCxV3SecuritySettingsInsightsExportSettings $insightsExportSettings
   */
  public function setInsightsExportSettings(GoogleCloudDialogflowCxV3SecuritySettingsInsightsExportSettings $insightsExportSettings)
  {
    $this->insightsExportSettings = $insightsExportSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3SecuritySettingsInsightsExportSettings
   */
  public function getInsightsExportSettings()
  {
    return $this->insightsExportSettings;
  }
  /**
   * [DLP](https://cloud.google.com/dlp/docs) inspect template name. Use this
   * template to define inspect base settings. The `DLP Inspect Templates
   * Reader` role is needed on the Dialogflow service identity service account
   * (has the form `service-PROJECT_NUMBER@gcp-sa-
   * dialogflow.iam.gserviceaccount.com`) for your agent's project. If empty, we
   * use the default DLP inspect config. The template name will have one of the
   * following formats: `projects//locations//inspectTemplates/` OR
   * `organizations//locations//inspectTemplates/` Note: `inspect_template` must
   * be located in the same region as the `SecuritySettings`.
   *
   * @param string $inspectTemplate
   */
  public function setInspectTemplate($inspectTemplate)
  {
    $this->inspectTemplate = $inspectTemplate;
  }
  /**
   * @return string
   */
  public function getInspectTemplate()
  {
    return $this->inspectTemplate;
  }
  /**
   * Resource name of the settings. Required for the
   * SecuritySettingsService.UpdateSecuritySettings method.
   * SecuritySettingsService.CreateSecuritySettings populates the name
   * automatically. Format: `projects//locations//securitySettings/`.
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
   * List of types of data to remove when retention settings triggers purge.
   *
   * @param string[] $purgeDataTypes
   */
  public function setPurgeDataTypes($purgeDataTypes)
  {
    $this->purgeDataTypes = $purgeDataTypes;
  }
  /**
   * @return string[]
   */
  public function getPurgeDataTypes()
  {
    return $this->purgeDataTypes;
  }
  /**
   * Defines the data for which Dialogflow applies redaction. Dialogflow does
   * not redact data that it does not have access to – for example, Cloud
   * logging.
   *
   * Accepted values: REDACTION_SCOPE_UNSPECIFIED, REDACT_DISK_STORAGE
   *
   * @param self::REDACTION_SCOPE_* $redactionScope
   */
  public function setRedactionScope($redactionScope)
  {
    $this->redactionScope = $redactionScope;
  }
  /**
   * @return self::REDACTION_SCOPE_*
   */
  public function getRedactionScope()
  {
    return $this->redactionScope;
  }
  /**
   * Strategy that defines how we do redaction.
   *
   * Accepted values: REDACTION_STRATEGY_UNSPECIFIED, REDACT_WITH_SERVICE
   *
   * @param self::REDACTION_STRATEGY_* $redactionStrategy
   */
  public function setRedactionStrategy($redactionStrategy)
  {
    $this->redactionStrategy = $redactionStrategy;
  }
  /**
   * @return self::REDACTION_STRATEGY_*
   */
  public function getRedactionStrategy()
  {
    return $this->redactionStrategy;
  }
  /**
   * Specifies the retention behavior defined by
   * SecuritySettings.RetentionStrategy.
   *
   * Accepted values: RETENTION_STRATEGY_UNSPECIFIED, REMOVE_AFTER_CONVERSATION
   *
   * @param self::RETENTION_STRATEGY_* $retentionStrategy
   */
  public function setRetentionStrategy($retentionStrategy)
  {
    $this->retentionStrategy = $retentionStrategy;
  }
  /**
   * @return self::RETENTION_STRATEGY_*
   */
  public function getRetentionStrategy()
  {
    return $this->retentionStrategy;
  }
  /**
   * Retains the data for the specified number of days. User must set a value
   * lower than Dialogflow's default 365d TTL (30 days for Agent Assist
   * traffic), higher value will be ignored and use default. Setting a value
   * higher than that has no effect. A missing value or setting to 0 also means
   * we use default TTL. When data retention configuration is changed, it only
   * applies to the data created after the change; the TTL of existing data
   * created before the change stays intact.
   *
   * @param int $retentionWindowDays
   */
  public function setRetentionWindowDays($retentionWindowDays)
  {
    $this->retentionWindowDays = $retentionWindowDays;
  }
  /**
   * @return int
   */
  public function getRetentionWindowDays()
  {
    return $this->retentionWindowDays;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3SecuritySettings::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3SecuritySettings');
