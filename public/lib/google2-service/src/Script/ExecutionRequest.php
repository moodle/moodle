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

namespace Google\Service\Script;

class ExecutionRequest extends \Google\Collection
{
  protected $collection_key = 'parameters';
  /**
   * If `true` and the user is an owner of the script, the script runs at the
   * most recently saved version rather than the version deployed for use with
   * the Apps Script API. Optional; default is `false`.
   *
   * @var bool
   */
  public $devMode;
  /**
   * The name of the function to execute in the given script. The name does not
   * include parentheses or parameters. It can reference a function in an
   * included library such as `Library.libFunction1`.
   *
   * @var string
   */
  public $function;
  /**
   * The parameters to be passed to the function being executed. The object type
   * for each parameter should match the expected type in Apps Script.
   * Parameters cannot be Apps Script-specific object types (such as a
   * `Document` or a `Calendar`); they can only be primitive types such as
   * `string`, `number`, `array`, `object`, or `boolean`. Optional.
   *
   * @var array[]
   */
  public $parameters;
  /**
   * *Deprecated*. For use with Android add-ons only. An ID that represents the
   * user's current session in the Android app for Google Docs or Sheets,
   * included as extra data in the
   * [Intent](https://developer.android.com/guide/components/intents-
   * filters.html) that launches the add-on. When an Android add-on is run with
   * a session state, it gains the privileges of a
   * [bound](https://developers.google.com/apps-script/guides/bound) script—that
   * is, it can access information like the user's current cursor position (in
   * Docs) or selected cell (in Sheets). To retrieve the state, call `Intent.get
   * StringExtra("com.google.android.apps.docs.addons.SessionState")`. Optional.
   *
   * @var string
   */
  public $sessionState;

  /**
   * If `true` and the user is an owner of the script, the script runs at the
   * most recently saved version rather than the version deployed for use with
   * the Apps Script API. Optional; default is `false`.
   *
   * @param bool $devMode
   */
  public function setDevMode($devMode)
  {
    $this->devMode = $devMode;
  }
  /**
   * @return bool
   */
  public function getDevMode()
  {
    return $this->devMode;
  }
  /**
   * The name of the function to execute in the given script. The name does not
   * include parentheses or parameters. It can reference a function in an
   * included library such as `Library.libFunction1`.
   *
   * @param string $function
   */
  public function setFunction($function)
  {
    $this->function = $function;
  }
  /**
   * @return string
   */
  public function getFunction()
  {
    return $this->function;
  }
  /**
   * The parameters to be passed to the function being executed. The object type
   * for each parameter should match the expected type in Apps Script.
   * Parameters cannot be Apps Script-specific object types (such as a
   * `Document` or a `Calendar`); they can only be primitive types such as
   * `string`, `number`, `array`, `object`, or `boolean`. Optional.
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
   * *Deprecated*. For use with Android add-ons only. An ID that represents the
   * user's current session in the Android app for Google Docs or Sheets,
   * included as extra data in the
   * [Intent](https://developer.android.com/guide/components/intents-
   * filters.html) that launches the add-on. When an Android add-on is run with
   * a session state, it gains the privileges of a
   * [bound](https://developers.google.com/apps-script/guides/bound) script—that
   * is, it can access information like the user's current cursor position (in
   * Docs) or selected cell (in Sheets). To retrieve the state, call `Intent.get
   * StringExtra("com.google.android.apps.docs.addons.SessionState")`. Optional.
   *
   * @param string $sessionState
   */
  public function setSessionState($sessionState)
  {
    $this->sessionState = $sessionState;
  }
  /**
   * @return string
   */
  public function getSessionState()
  {
    return $this->sessionState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExecutionRequest::class, 'Google_Service_Script_ExecutionRequest');
