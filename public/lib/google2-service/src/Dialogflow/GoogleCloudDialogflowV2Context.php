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

class GoogleCloudDialogflowV2Context extends \Google\Model
{
  /**
   * Optional. The number of conversational query requests after which the
   * context expires. The default is `0`. If set to `0`, the context expires
   * immediately. Contexts expire automatically after 20 minutes if there are no
   * matching queries.
   *
   * @var int
   */
  public $lifespanCount;
  /**
   * Required. The unique identifier of the context. Format:
   * `projects//agent/sessions//contexts/`, or
   * `projects//agent/environments//users//sessions//contexts/`. The `Context
   * ID` is always converted to lowercase, may only contain characters in
   * `a-zA-Z0-9_-%` and may be at most 250 bytes long. If `Environment ID` is
   * not specified, we assume default 'draft' environment. If `User ID` is not
   * specified, we assume default '-' user. The following context names are
   * reserved for internal use by Dialogflow. You should not use these contexts
   * or create contexts with these names: * `__system_counters__` *
   * `*_id_dialog_context` * `*_dialog_params_size`
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The collection of parameters associated with this context.
   * Depending on your protocol or client library language, this is a map,
   * associative array, symbol table, dictionary, or JSON object composed of a
   * collection of (MapKey, MapValue) pairs: * MapKey type: string * MapKey
   * value: parameter name * MapValue type: If parameter's entity type is a
   * composite entity then use map, otherwise, depending on the parameter value
   * type, it could be one of string, number, boolean, null, list or map. *
   * MapValue value: If parameter's entity type is a composite entity then use
   * map from composite entity property names to property values, otherwise, use
   * parameter value.
   *
   * @var array[]
   */
  public $parameters;

  /**
   * Optional. The number of conversational query requests after which the
   * context expires. The default is `0`. If set to `0`, the context expires
   * immediately. Contexts expire automatically after 20 minutes if there are no
   * matching queries.
   *
   * @param int $lifespanCount
   */
  public function setLifespanCount($lifespanCount)
  {
    $this->lifespanCount = $lifespanCount;
  }
  /**
   * @return int
   */
  public function getLifespanCount()
  {
    return $this->lifespanCount;
  }
  /**
   * Required. The unique identifier of the context. Format:
   * `projects//agent/sessions//contexts/`, or
   * `projects//agent/environments//users//sessions//contexts/`. The `Context
   * ID` is always converted to lowercase, may only contain characters in
   * `a-zA-Z0-9_-%` and may be at most 250 bytes long. If `Environment ID` is
   * not specified, we assume default 'draft' environment. If `User ID` is not
   * specified, we assume default '-' user. The following context names are
   * reserved for internal use by Dialogflow. You should not use these contexts
   * or create contexts with these names: * `__system_counters__` *
   * `*_id_dialog_context` * `*_dialog_params_size`
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
   * Optional. The collection of parameters associated with this context.
   * Depending on your protocol or client library language, this is a map,
   * associative array, symbol table, dictionary, or JSON object composed of a
   * collection of (MapKey, MapValue) pairs: * MapKey type: string * MapKey
   * value: parameter name * MapValue type: If parameter's entity type is a
   * composite entity then use map, otherwise, depending on the parameter value
   * type, it could be one of string, number, boolean, null, list or map. *
   * MapValue value: If parameter's entity type is a composite entity then use
   * map from composite entity property names to property values, otherwise, use
   * parameter value.
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
class_alias(GoogleCloudDialogflowV2Context::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2Context');
