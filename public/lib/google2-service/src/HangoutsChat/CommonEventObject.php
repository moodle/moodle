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

namespace Google\Service\HangoutsChat;

class CommonEventObject extends \Google\Model
{
  /**
   * Google can't identify a host app.
   */
  public const HOST_APP_UNSPECIFIED_HOST_APP = 'UNSPECIFIED_HOST_APP';
  /**
   * The add-on launches from Gmail.
   */
  public const HOST_APP_GMAIL = 'GMAIL';
  /**
   * The add-on launches from Google Calendar.
   */
  public const HOST_APP_CALENDAR = 'CALENDAR';
  /**
   * The add-on launches from Google Drive.
   */
  public const HOST_APP_DRIVE = 'DRIVE';
  /**
   * Not used.
   */
  public const HOST_APP_DEMO = 'DEMO';
  /**
   * The add-on launches from Google Docs.
   */
  public const HOST_APP_DOCS = 'DOCS';
  /**
   * The add-on launches from Google Meet.
   */
  public const HOST_APP_MEET = 'MEET';
  /**
   * The add-on launches from Google Sheets.
   */
  public const HOST_APP_SHEETS = 'SHEETS';
  /**
   * The add-on launches from Google Slides.
   */
  public const HOST_APP_SLIDES = 'SLIDES';
  /**
   * The add-on launches from Google Drawings.
   */
  public const HOST_APP_DRAWINGS = 'DRAWINGS';
  /**
   * A Google Chat app.
   */
  public const HOST_APP_CHAT = 'CHAT';
  public const PLATFORM_UNKNOWN_PLATFORM = 'UNKNOWN_PLATFORM';
  public const PLATFORM_WEB = 'WEB';
  public const PLATFORM_IOS = 'IOS';
  public const PLATFORM_ANDROID = 'ANDROID';
  protected $formInputsType = Inputs::class;
  protected $formInputsDataType = 'map';
  /**
   * Indicates the host app the add-on is active in when the event object is
   * generated. Possible values include the following: * `GMAIL` * `CALENDAR` *
   * `DRIVE` * `DOCS` * `SHEETS` * `SLIDES` * `CHAT`
   *
   * @var string
   */
  public $hostApp;
  /**
   * Name of the function to invoke. This field doesn't populate for Google
   * Workspace Add-ons that extend Google Chat. Instead, to receive function
   * data like identifiers, add-ons that extend Chat should use the `parameters`
   * field. See [Build interactive interfaces for Chat
   * apps](https://developers.google.com/workspace/add-ons/chat/build).
   *
   * @var string
   */
  public $invokedFunction;
  /**
   * Any additional parameters you supply to an action using
   * [`actionParameters`](https://developers.google.com/workspace/add-ons/refere
   * nce/rpc/google.apps.card.v1#google.apps.card.v1.Action.ActionParameter) or
   * [`Action.setParameters()`](https://developers.google.com/apps-
   * script/reference/card-service/action#setparametersparameters). **Developer
   * Preview:** For [add-ons that extend Google
   * Chat](https://developers.google.com/workspace/add-ons/chat), to suggest
   * items based on what the users type in multiselect menus, use the value of
   * the `"autocomplete_widget_query"` key
   * (`event.commonEventObject.parameters["autocomplete_widget_query"]`). You
   * can use this value to query a database and suggest selectable items to
   * users as they type. For details, see [Collect and process information from
   * Google Chat users](https://developers.google.com/workspace/add-
   * ons/chat/collect-information).
   *
   * @var string[]
   */
  public $parameters;
  /**
   * The platform enum which indicates the platform where the event originates
   * (`WEB`, `IOS`, or `ANDROID`). Not supported by Chat apps.
   *
   * @var string
   */
  public $platform;
  protected $timeZoneType = TimeZone::class;
  protected $timeZoneDataType = '';
  /**
   * **Disabled by default.** The user's language and country/region identifier
   * in the format of [ISO
   * 639](https://wikipedia.org/wiki/ISO_639_macrolanguage) language code-[ISO
   * 3166](https://wikipedia.org/wiki/ISO_3166) country/region code. For
   * example, `en-US`. To turn on this field, you must set
   * `addOns.common.useLocaleFromApp` to `true` in your add-on's manifest. Your
   * add-on's scope list must also include
   * `https://www.googleapis.com/auth/script.locale`. See [Accessing user locale
   * and timezone](https://developers.google.com/workspace/add-ons/how-
   * tos/access-user-locale) for more details.
   *
   * @var string
   */
  public $userLocale;

  /**
   * A map containing the current values of the widgets in the displayed card.
   * The map keys are the string IDs assigned with each widget. The structure of
   * the map value object is dependent on the widget type: **Note**: The
   * following examples are formatted for Apps Script's V8 runtime. If you're
   * using Rhino runtime, you must add `[""]` after the value. For example,
   * instead of
   * `e.commonEventObject.formInputs.employeeName.stringInputs.value[0]`, format
   * the event object as
   * `e.commonEventObject.formInputs.employeeName[""].stringInputs.value[0]`. To
   * learn more about runtimes in Apps Script, see the [V8 Runtime
   * Overview](https://developers.google.com/apps-script/guides/v8-runtime). *
   * Single-valued widgets (for example, a text box): a list of strings (only
   * one element). **Example**: for a text input widget with `employeeName` as
   * its ID, access the text input value with:
   * `e.commonEventObject.formInputs.employeeName.stringInputs.value[0]`. *
   * Multi-valued widgets (for example, checkbox groups): a list of strings.
   * **Example**: for a multi-value widget with `participants` as its ID, access
   * the value array with:
   * `e.commonEventObject.formInputs.participants.stringInputs.value`. * **A
   * date-time picker**: a [`DateTimeInput
   * object`](https://developers.google.com/workspace/add-ons/concepts/event-
   * objects#date-time-input). **Example**: For a picker with an ID of
   * `myDTPicker`, access the
   * [`DateTimeInput`](https://developers.google.com/workspace/add-
   * ons/concepts/event-objects#date-time-input) object using
   * `e.commonEventObject.formInputs.myDTPicker.dateTimeInput`. * **A date-only
   * picker**: a [`DateInput
   * object`](https://developers.google.com/workspace/add-ons/concepts/event-
   * objects#date-input). **Example**: For a picker with an ID of
   * `myDatePicker`, access the
   * [`DateInput`](https://developers.google.com/workspace/add-
   * ons/concepts/event-objects#date-input) object using
   * `e.commonEventObject.formInputs.myDatePicker.dateInput`. * **A time-only
   * picker**: a [`TimeInput
   * object`](https://developers.google.com/workspace/add-ons/concepts/event-
   * objects#time-input). **Example**: For a picker with an ID of
   * `myTimePicker`, access the
   * [`TimeInput`](https://developers.google.com/workspace/add-
   * ons/concepts/event-objects#time-input) object using
   * `e.commonEventObject.formInputs.myTimePicker.timeInput`.
   *
   * @param Inputs[] $formInputs
   */
  public function setFormInputs($formInputs)
  {
    $this->formInputs = $formInputs;
  }
  /**
   * @return Inputs[]
   */
  public function getFormInputs()
  {
    return $this->formInputs;
  }
  /**
   * Indicates the host app the add-on is active in when the event object is
   * generated. Possible values include the following: * `GMAIL` * `CALENDAR` *
   * `DRIVE` * `DOCS` * `SHEETS` * `SLIDES` * `CHAT`
   *
   * Accepted values: UNSPECIFIED_HOST_APP, GMAIL, CALENDAR, DRIVE, DEMO, DOCS,
   * MEET, SHEETS, SLIDES, DRAWINGS, CHAT
   *
   * @param self::HOST_APP_* $hostApp
   */
  public function setHostApp($hostApp)
  {
    $this->hostApp = $hostApp;
  }
  /**
   * @return self::HOST_APP_*
   */
  public function getHostApp()
  {
    return $this->hostApp;
  }
  /**
   * Name of the function to invoke. This field doesn't populate for Google
   * Workspace Add-ons that extend Google Chat. Instead, to receive function
   * data like identifiers, add-ons that extend Chat should use the `parameters`
   * field. See [Build interactive interfaces for Chat
   * apps](https://developers.google.com/workspace/add-ons/chat/build).
   *
   * @param string $invokedFunction
   */
  public function setInvokedFunction($invokedFunction)
  {
    $this->invokedFunction = $invokedFunction;
  }
  /**
   * @return string
   */
  public function getInvokedFunction()
  {
    return $this->invokedFunction;
  }
  /**
   * Any additional parameters you supply to an action using
   * [`actionParameters`](https://developers.google.com/workspace/add-ons/refere
   * nce/rpc/google.apps.card.v1#google.apps.card.v1.Action.ActionParameter) or
   * [`Action.setParameters()`](https://developers.google.com/apps-
   * script/reference/card-service/action#setparametersparameters). **Developer
   * Preview:** For [add-ons that extend Google
   * Chat](https://developers.google.com/workspace/add-ons/chat), to suggest
   * items based on what the users type in multiselect menus, use the value of
   * the `"autocomplete_widget_query"` key
   * (`event.commonEventObject.parameters["autocomplete_widget_query"]`). You
   * can use this value to query a database and suggest selectable items to
   * users as they type. For details, see [Collect and process information from
   * Google Chat users](https://developers.google.com/workspace/add-
   * ons/chat/collect-information).
   *
   * @param string[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return string[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * The platform enum which indicates the platform where the event originates
   * (`WEB`, `IOS`, or `ANDROID`). Not supported by Chat apps.
   *
   * Accepted values: UNKNOWN_PLATFORM, WEB, IOS, ANDROID
   *
   * @param self::PLATFORM_* $platform
   */
  public function setPlatform($platform)
  {
    $this->platform = $platform;
  }
  /**
   * @return self::PLATFORM_*
   */
  public function getPlatform()
  {
    return $this->platform;
  }
  /**
   * **Disabled by default.** The timezone ID and offset from Coordinated
   * Universal Time (UTC). To turn on this field, you must set
   * `addOns.common.useLocaleFromApp` to `true` in your add-on's manifest. Your
   * add-on's scope list must also include
   * `https://www.googleapis.com/auth/script.locale`. See [Accessing user locale
   * and timezone](https://developers.google.com/workspace/add-ons/how-
   * tos/access-user-locale) for more details. Only supported for the event
   * types [`CARD_CLICKED`](https://developers.google.com/chat/api/reference/res
   * t/v1/EventType#ENUM_VALUES.CARD_CLICKED) and [`SUBMIT_DIALOG`](https://deve
   * lopers.google.com/chat/api/reference/rest/v1/DialogEventType#ENUM_VALUES.SU
   * BMIT_DIALOG).
   *
   * @param TimeZone $timeZone
   */
  public function setTimeZone(TimeZone $timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return TimeZone
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  /**
   * **Disabled by default.** The user's language and country/region identifier
   * in the format of [ISO
   * 639](https://wikipedia.org/wiki/ISO_639_macrolanguage) language code-[ISO
   * 3166](https://wikipedia.org/wiki/ISO_3166) country/region code. For
   * example, `en-US`. To turn on this field, you must set
   * `addOns.common.useLocaleFromApp` to `true` in your add-on's manifest. Your
   * add-on's scope list must also include
   * `https://www.googleapis.com/auth/script.locale`. See [Accessing user locale
   * and timezone](https://developers.google.com/workspace/add-ons/how-
   * tos/access-user-locale) for more details.
   *
   * @param string $userLocale
   */
  public function setUserLocale($userLocale)
  {
    $this->userLocale = $userLocale;
  }
  /**
   * @return string
   */
  public function getUserLocale()
  {
    return $this->userLocale;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CommonEventObject::class, 'Google_Service_HangoutsChat_CommonEventObject');
