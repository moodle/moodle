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

class GoogleCloudDialogflowCxV3QueryParameters extends \Google\Collection
{
  protected $collection_key = 'sessionEntityTypes';
  /**
   * Configures whether sentiment analysis should be performed. If not provided,
   * sentiment analysis is not performed.
   *
   * @var bool
   */
  public $analyzeQueryTextSentiment;
  /**
   * The channel which this query is for. If specified, only the ResponseMessage
   * associated with the channel will be returned. If no ResponseMessage is
   * associated with the channel, it falls back to the ResponseMessage with
   * unspecified channel. If unspecified, the ResponseMessage with unspecified
   * channel will be returned.
   *
   * @var string
   */
  public $channel;
  /**
   * The unique identifier of the page to override the current page in the
   * session. Format: `projects//locations//agents//flows//pages/`. If
   * `current_page` is specified, the previous state of the session will be
   * ignored by Dialogflow, including the previous page and the previous session
   * parameters. In most cases, current_page and parameters should be configured
   * together to direct a session to a specific state.
   *
   * @var string
   */
  public $currentPage;
  /**
   * Optional. The unique identifier of the playbook to start or continue the
   * session with. If `current_playbook` is specified, the previous state of the
   * session will be ignored by Dialogflow. Format:
   * `projects//locations//agents//playbooks/`.
   *
   * @var string
   */
  public $currentPlaybook;
  /**
   * Whether to disable webhook calls for this request.
   *
   * @var bool
   */
  public $disableWebhook;
  /**
   * Optional. Information about the end-user to improve the relevance and
   * accuracy of generative answers. This will be interpreted and used by a
   * language model, so, for good results, the data should be self-descriptive,
   * and in a simple structure. Example: ```json { "subscription plan":
   * "Business Premium Plus", "devices owned": [ {"model": "Google Pixel 7"},
   * {"model": "Google Pixel Tablet"} ] } ```
   *
   * @var array[]
   */
  public $endUserMetadata;
  /**
   * A list of flow versions to override for the request. Format:
   * `projects//locations//agents//flows//versions/`. If version 1 of flow X is
   * included in this list, the traffic of flow X will go through version 1
   * regardless of the version configuration in the environment. Each flow can
   * have at most one version specified in this list.
   *
   * @var string[]
   */
  public $flowVersions;
  protected $geoLocationType = GoogleTypeLatLng::class;
  protected $geoLocationDataType = '';
  protected $llmModelSettingsType = GoogleCloudDialogflowCxV3LlmModelSettings::class;
  protected $llmModelSettingsDataType = '';
  /**
   * Scope for the parameters. If not specified, parameters will be treated as
   * session parameters. Parameters with custom scope will not be put into
   * session parameters. You can reference the parameters with custom scope in
   * the agent with the following format: $parameter-scope.params.parameter-id.
   *
   * @var string
   */
  public $parameterScope;
  /**
   * Additional parameters to be put into session parameters. To remove a
   * parameter from the session, clients should explicitly set the parameter
   * value to null. You can reference the session parameters in the agent with
   * the following format: $session.params.parameter-id. Depending on your
   * protocol or client library language, this is a map, associative array,
   * symbol table, dictionary, or JSON object composed of a collection of
   * (MapKey, MapValue) pairs: * MapKey type: string * MapKey value: parameter
   * name * MapValue type: If parameter's entity type is a composite entity then
   * use map, otherwise, depending on the parameter value type, it could be one
   * of string, number, boolean, null, list or map. * MapValue value: If
   * parameter's entity type is a composite entity then use map from composite
   * entity property names to property values, otherwise, use parameter value.
   *
   * @var array[]
   */
  public $parameters;
  /**
   * This field can be used to pass custom data into the webhook associated with
   * the agent. Arbitrary JSON objects are supported. Some integrations that
   * query a Dialogflow agent may provide additional information in the payload.
   * In particular, for the Dialogflow Phone Gateway integration, this field has
   * the form: ``` { "telephony": { "caller_id": "+18558363987" } } ```
   *
   * @var array[]
   */
  public $payload;
  /**
   * Optional. If set to true and data stores are involved in serving the
   * request then
   * DetectIntentResponse.query_result.data_store_connection_signals will be
   * filled with data that can help evaluations.
   *
   * @deprecated
   * @var bool
   */
  public $populateDataStoreConnectionSignals;
  protected $searchConfigType = GoogleCloudDialogflowCxV3SearchConfig::class;
  protected $searchConfigDataType = '';
  protected $sessionEntityTypesType = GoogleCloudDialogflowCxV3SessionEntityType::class;
  protected $sessionEntityTypesDataType = 'array';
  /**
   * Optional. Configure lifetime of the Dialogflow session. By default, a
   * Dialogflow session remains active and its data is stored for 30 minutes
   * after the last request is sent for the session. This value should be no
   * longer than 1 day.
   *
   * @var string
   */
  public $sessionTtl;
  /**
   * The time zone of this conversational query from the [time zone
   * database](https://www.iana.org/time-zones), e.g., America/New_York,
   * Europe/Paris. If not provided, the time zone specified in the agent is
   * used.
   *
   * @var string
   */
  public $timeZone;
  /**
   * This field can be used to pass HTTP headers for a webhook call. These
   * headers will be sent to webhook along with the headers that have been
   * configured through Dialogflow web console. The headers defined within this
   * field will overwrite the headers configured through Dialogflow console if
   * there is a conflict. Header names are case-insensitive. Google's specified
   * headers are not allowed. Including: "Host", "Content-Length", "Connection",
   * "From", "User-Agent", "Accept-Encoding", "If-Modified-Since", "If-None-
   * Match", "X-Forwarded-For", etc.
   *
   * @var string[]
   */
  public $webhookHeaders;

  /**
   * Configures whether sentiment analysis should be performed. If not provided,
   * sentiment analysis is not performed.
   *
   * @param bool $analyzeQueryTextSentiment
   */
  public function setAnalyzeQueryTextSentiment($analyzeQueryTextSentiment)
  {
    $this->analyzeQueryTextSentiment = $analyzeQueryTextSentiment;
  }
  /**
   * @return bool
   */
  public function getAnalyzeQueryTextSentiment()
  {
    return $this->analyzeQueryTextSentiment;
  }
  /**
   * The channel which this query is for. If specified, only the ResponseMessage
   * associated with the channel will be returned. If no ResponseMessage is
   * associated with the channel, it falls back to the ResponseMessage with
   * unspecified channel. If unspecified, the ResponseMessage with unspecified
   * channel will be returned.
   *
   * @param string $channel
   */
  public function setChannel($channel)
  {
    $this->channel = $channel;
  }
  /**
   * @return string
   */
  public function getChannel()
  {
    return $this->channel;
  }
  /**
   * The unique identifier of the page to override the current page in the
   * session. Format: `projects//locations//agents//flows//pages/`. If
   * `current_page` is specified, the previous state of the session will be
   * ignored by Dialogflow, including the previous page and the previous session
   * parameters. In most cases, current_page and parameters should be configured
   * together to direct a session to a specific state.
   *
   * @param string $currentPage
   */
  public function setCurrentPage($currentPage)
  {
    $this->currentPage = $currentPage;
  }
  /**
   * @return string
   */
  public function getCurrentPage()
  {
    return $this->currentPage;
  }
  /**
   * Optional. The unique identifier of the playbook to start or continue the
   * session with. If `current_playbook` is specified, the previous state of the
   * session will be ignored by Dialogflow. Format:
   * `projects//locations//agents//playbooks/`.
   *
   * @param string $currentPlaybook
   */
  public function setCurrentPlaybook($currentPlaybook)
  {
    $this->currentPlaybook = $currentPlaybook;
  }
  /**
   * @return string
   */
  public function getCurrentPlaybook()
  {
    return $this->currentPlaybook;
  }
  /**
   * Whether to disable webhook calls for this request.
   *
   * @param bool $disableWebhook
   */
  public function setDisableWebhook($disableWebhook)
  {
    $this->disableWebhook = $disableWebhook;
  }
  /**
   * @return bool
   */
  public function getDisableWebhook()
  {
    return $this->disableWebhook;
  }
  /**
   * Optional. Information about the end-user to improve the relevance and
   * accuracy of generative answers. This will be interpreted and used by a
   * language model, so, for good results, the data should be self-descriptive,
   * and in a simple structure. Example: ```json { "subscription plan":
   * "Business Premium Plus", "devices owned": [ {"model": "Google Pixel 7"},
   * {"model": "Google Pixel Tablet"} ] } ```
   *
   * @param array[] $endUserMetadata
   */
  public function setEndUserMetadata($endUserMetadata)
  {
    $this->endUserMetadata = $endUserMetadata;
  }
  /**
   * @return array[]
   */
  public function getEndUserMetadata()
  {
    return $this->endUserMetadata;
  }
  /**
   * A list of flow versions to override for the request. Format:
   * `projects//locations//agents//flows//versions/`. If version 1 of flow X is
   * included in this list, the traffic of flow X will go through version 1
   * regardless of the version configuration in the environment. Each flow can
   * have at most one version specified in this list.
   *
   * @param string[] $flowVersions
   */
  public function setFlowVersions($flowVersions)
  {
    $this->flowVersions = $flowVersions;
  }
  /**
   * @return string[]
   */
  public function getFlowVersions()
  {
    return $this->flowVersions;
  }
  /**
   * The geo location of this conversational query.
   *
   * @param GoogleTypeLatLng $geoLocation
   */
  public function setGeoLocation(GoogleTypeLatLng $geoLocation)
  {
    $this->geoLocation = $geoLocation;
  }
  /**
   * @return GoogleTypeLatLng
   */
  public function getGeoLocation()
  {
    return $this->geoLocation;
  }
  /**
   * Optional. Use the specified LLM model settings for processing the request.
   *
   * @param GoogleCloudDialogflowCxV3LlmModelSettings $llmModelSettings
   */
  public function setLlmModelSettings(GoogleCloudDialogflowCxV3LlmModelSettings $llmModelSettings)
  {
    $this->llmModelSettings = $llmModelSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3LlmModelSettings
   */
  public function getLlmModelSettings()
  {
    return $this->llmModelSettings;
  }
  /**
   * Scope for the parameters. If not specified, parameters will be treated as
   * session parameters. Parameters with custom scope will not be put into
   * session parameters. You can reference the parameters with custom scope in
   * the agent with the following format: $parameter-scope.params.parameter-id.
   *
   * @param string $parameterScope
   */
  public function setParameterScope($parameterScope)
  {
    $this->parameterScope = $parameterScope;
  }
  /**
   * @return string
   */
  public function getParameterScope()
  {
    return $this->parameterScope;
  }
  /**
   * Additional parameters to be put into session parameters. To remove a
   * parameter from the session, clients should explicitly set the parameter
   * value to null. You can reference the session parameters in the agent with
   * the following format: $session.params.parameter-id. Depending on your
   * protocol or client library language, this is a map, associative array,
   * symbol table, dictionary, or JSON object composed of a collection of
   * (MapKey, MapValue) pairs: * MapKey type: string * MapKey value: parameter
   * name * MapValue type: If parameter's entity type is a composite entity then
   * use map, otherwise, depending on the parameter value type, it could be one
   * of string, number, boolean, null, list or map. * MapValue value: If
   * parameter's entity type is a composite entity then use map from composite
   * entity property names to property values, otherwise, use parameter value.
   *
   * @param array[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return array[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * This field can be used to pass custom data into the webhook associated with
   * the agent. Arbitrary JSON objects are supported. Some integrations that
   * query a Dialogflow agent may provide additional information in the payload.
   * In particular, for the Dialogflow Phone Gateway integration, this field has
   * the form: ``` { "telephony": { "caller_id": "+18558363987" } } ```
   *
   * @param array[] $payload
   */
  public function setPayload($payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return array[]
   */
  public function getPayload()
  {
    return $this->payload;
  }
  /**
   * Optional. If set to true and data stores are involved in serving the
   * request then
   * DetectIntentResponse.query_result.data_store_connection_signals will be
   * filled with data that can help evaluations.
   *
   * @deprecated
   * @param bool $populateDataStoreConnectionSignals
   */
  public function setPopulateDataStoreConnectionSignals($populateDataStoreConnectionSignals)
  {
    $this->populateDataStoreConnectionSignals = $populateDataStoreConnectionSignals;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getPopulateDataStoreConnectionSignals()
  {
    return $this->populateDataStoreConnectionSignals;
  }
  /**
   * Optional. Search configuration for UCS search queries.
   *
   * @param GoogleCloudDialogflowCxV3SearchConfig $searchConfig
   */
  public function setSearchConfig(GoogleCloudDialogflowCxV3SearchConfig $searchConfig)
  {
    $this->searchConfig = $searchConfig;
  }
  /**
   * @return GoogleCloudDialogflowCxV3SearchConfig
   */
  public function getSearchConfig()
  {
    return $this->searchConfig;
  }
  /**
   * Additional session entity types to replace or extend developer entity types
   * with. The entity synonyms apply to all languages and persist for the
   * session of this query.
   *
   * @param GoogleCloudDialogflowCxV3SessionEntityType[] $sessionEntityTypes
   */
  public function setSessionEntityTypes($sessionEntityTypes)
  {
    $this->sessionEntityTypes = $sessionEntityTypes;
  }
  /**
   * @return GoogleCloudDialogflowCxV3SessionEntityType[]
   */
  public function getSessionEntityTypes()
  {
    return $this->sessionEntityTypes;
  }
  /**
   * Optional. Configure lifetime of the Dialogflow session. By default, a
   * Dialogflow session remains active and its data is stored for 30 minutes
   * after the last request is sent for the session. This value should be no
   * longer than 1 day.
   *
   * @param string $sessionTtl
   */
  public function setSessionTtl($sessionTtl)
  {
    $this->sessionTtl = $sessionTtl;
  }
  /**
   * @return string
   */
  public function getSessionTtl()
  {
    return $this->sessionTtl;
  }
  /**
   * The time zone of this conversational query from the [time zone
   * database](https://www.iana.org/time-zones), e.g., America/New_York,
   * Europe/Paris. If not provided, the time zone specified in the agent is
   * used.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  /**
   * This field can be used to pass HTTP headers for a webhook call. These
   * headers will be sent to webhook along with the headers that have been
   * configured through Dialogflow web console. The headers defined within this
   * field will overwrite the headers configured through Dialogflow console if
   * there is a conflict. Header names are case-insensitive. Google's specified
   * headers are not allowed. Including: "Host", "Content-Length", "Connection",
   * "From", "User-Agent", "Accept-Encoding", "If-Modified-Since", "If-None-
   * Match", "X-Forwarded-For", etc.
   *
   * @param string[] $webhookHeaders
   */
  public function setWebhookHeaders($webhookHeaders)
  {
    $this->webhookHeaders = $webhookHeaders;
  }
  /**
   * @return string[]
   */
  public function getWebhookHeaders()
  {
    return $this->webhookHeaders;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3QueryParameters::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3QueryParameters');
