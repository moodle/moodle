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

class GoogleCloudDialogflowV2EventInput extends \Google\Model
{
  /**
   * Required. The language of this query. See [Language
   * Support](https://cloud.google.com/dialogflow/docs/reference/language) for a
   * list of the currently supported language codes. Note that queries in the
   * same session do not necessarily need to specify the same language. This
   * field is ignored when used in the context of a
   * WebhookResponse.followup_event_input field, because the language was
   * already defined in the originating detect intent request.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Required. The unique identifier of the event.
   *
   * @var string
   */
  public $name;
  /**
   * The collection of parameters associated with the event. Depending on your
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
   * Required. The language of this query. See [Language
   * Support](https://cloud.google.com/dialogflow/docs/reference/language) for a
   * list of the currently supported language codes. Note that queries in the
   * same session do not necessarily need to specify the same language. This
   * field is ignored when used in the context of a
   * WebhookResponse.followup_event_input field, because the language was
   * already defined in the originating detect intent request.
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
   * Required. The unique identifier of the event.
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
   * The collection of parameters associated with the event. Depending on your
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2EventInput::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2EventInput');
