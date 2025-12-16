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

class GoogleAppsCardV1TextInput extends \Google\Model
{
  /**
   * The text input field has a fixed height of one line.
   */
  public const TYPE_SINGLE_LINE = 'SINGLE_LINE';
  /**
   * The text input field has a fixed height of multiple lines.
   */
  public const TYPE_MULTIPLE_LINE = 'MULTIPLE_LINE';
  protected $autoCompleteActionType = GoogleAppsCardV1Action::class;
  protected $autoCompleteActionDataType = '';
  /**
   * Text that appears below the text input field meant to assist users by
   * prompting them to enter a certain value. This text is always visible.
   * Required if `label` is unspecified. Otherwise, optional.
   *
   * @var string
   */
  public $hintText;
  protected $hostAppDataSourceType = HostAppDataSourceMarkup::class;
  protected $hostAppDataSourceDataType = '';
  protected $initialSuggestionsType = GoogleAppsCardV1Suggestions::class;
  protected $initialSuggestionsDataType = '';
  /**
   * The text that appears above the text input field in the user interface.
   * Specify text that helps the user enter the information your app needs. For
   * example, if you are asking someone's name, but specifically need their
   * surname, write `surname` instead of `name`. Required if `hintText` is
   * unspecified. Otherwise, optional.
   *
   * @var string
   */
  public $label;
  /**
   * The name by which the text input is identified in a form input event. For
   * details about working with form inputs, see [Receive form
   * data](https://developers.google.com/workspace/chat/read-form-data).
   *
   * @var string
   */
  public $name;
  protected $onChangeActionType = GoogleAppsCardV1Action::class;
  protected $onChangeActionDataType = '';
  /**
   * Text that appears in the text input field when the field is empty. Use this
   * text to prompt users to enter a value. For example, `Enter a number from 0
   * to 100`. [Google Chat apps](https://developers.google.com/workspace/chat):
   *
   * @var string
   */
  public $placeholderText;
  /**
   * How a text input field appears in the user interface. For example, whether
   * the field is single or multi-line.
   *
   * @var string
   */
  public $type;
  protected $validationType = GoogleAppsCardV1Validation::class;
  protected $validationDataType = '';
  /**
   * The value entered by a user, returned as part of a form input event. For
   * details about working with form inputs, see [Receive form
   * data](https://developers.google.com/workspace/chat/read-form-data).
   *
   * @var string
   */
  public $value;

  /**
   * Optional. Specify what action to take when the text input field provides
   * suggestions to users who interact with it. If unspecified, the suggestions
   * are set by `initialSuggestions` and are processed by the client. If
   * specified, the app takes the action specified here, such as running a
   * custom function. [Google Workspace add-
   * ons](https://developers.google.com/workspace/add-ons):
   *
   * @param GoogleAppsCardV1Action $autoCompleteAction
   */
  public function setAutoCompleteAction(GoogleAppsCardV1Action $autoCompleteAction)
  {
    $this->autoCompleteAction = $autoCompleteAction;
  }
  /**
   * @return GoogleAppsCardV1Action
   */
  public function getAutoCompleteAction()
  {
    return $this->autoCompleteAction;
  }
  /**
   * Text that appears below the text input field meant to assist users by
   * prompting them to enter a certain value. This text is always visible.
   * Required if `label` is unspecified. Otherwise, optional.
   *
   * @param string $hintText
   */
  public function setHintText($hintText)
  {
    $this->hintText = $hintText;
  }
  /**
   * @return string
   */
  public function getHintText()
  {
    return $this->hintText;
  }
  /**
   * A data source that's unique to a Google Workspace host application, such as
   * Gmail emails, Google Calendar events, or Google Chat messages. Available
   * for Google Workspace add-ons that extend Google Workspace Studio.
   * Unavailable for Google Chat apps.
   *
   * @param HostAppDataSourceMarkup $hostAppDataSource
   */
  public function setHostAppDataSource(HostAppDataSourceMarkup $hostAppDataSource)
  {
    $this->hostAppDataSource = $hostAppDataSource;
  }
  /**
   * @return HostAppDataSourceMarkup
   */
  public function getHostAppDataSource()
  {
    return $this->hostAppDataSource;
  }
  /**
   * Suggested values that users can enter. These values appear when users click
   * inside the text input field. As users type, the suggested values
   * dynamically filter to match what the users have typed. For example, a text
   * input field for programming language might suggest Java, JavaScript,
   * Python, and C++. When users start typing `Jav`, the list of suggestions
   * filters to show just `Java` and `JavaScript`. Suggested values help guide
   * users to enter values that your app can make sense of. When referring to
   * JavaScript, some users might enter `javascript` and others `java script`.
   * Suggesting `JavaScript` can standardize how users interact with your app.
   * When specified, `TextInput.type` is always `SINGLE_LINE`, even if it's set
   * to `MULTIPLE_LINE`. [Google Workspace add-ons and Chat
   * apps](https://developers.google.com/workspace/extend):
   *
   * @param GoogleAppsCardV1Suggestions $initialSuggestions
   */
  public function setInitialSuggestions(GoogleAppsCardV1Suggestions $initialSuggestions)
  {
    $this->initialSuggestions = $initialSuggestions;
  }
  /**
   * @return GoogleAppsCardV1Suggestions
   */
  public function getInitialSuggestions()
  {
    return $this->initialSuggestions;
  }
  /**
   * The text that appears above the text input field in the user interface.
   * Specify text that helps the user enter the information your app needs. For
   * example, if you are asking someone's name, but specifically need their
   * surname, write `surname` instead of `name`. Required if `hintText` is
   * unspecified. Otherwise, optional.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * The name by which the text input is identified in a form input event. For
   * details about working with form inputs, see [Receive form
   * data](https://developers.google.com/workspace/chat/read-form-data).
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
   * What to do when a change occurs in the text input field. For example, a
   * user adding to the field or deleting text. Examples of actions to take
   * include running a custom function or opening a
   * [dialog](https://developers.google.com/workspace/chat/dialogs) in Google
   * Chat.
   *
   * @param GoogleAppsCardV1Action $onChangeAction
   */
  public function setOnChangeAction(GoogleAppsCardV1Action $onChangeAction)
  {
    $this->onChangeAction = $onChangeAction;
  }
  /**
   * @return GoogleAppsCardV1Action
   */
  public function getOnChangeAction()
  {
    return $this->onChangeAction;
  }
  /**
   * Text that appears in the text input field when the field is empty. Use this
   * text to prompt users to enter a value. For example, `Enter a number from 0
   * to 100`. [Google Chat apps](https://developers.google.com/workspace/chat):
   *
   * @param string $placeholderText
   */
  public function setPlaceholderText($placeholderText)
  {
    $this->placeholderText = $placeholderText;
  }
  /**
   * @return string
   */
  public function getPlaceholderText()
  {
    return $this->placeholderText;
  }
  /**
   * How a text input field appears in the user interface. For example, whether
   * the field is single or multi-line.
   *
   * Accepted values: SINGLE_LINE, MULTIPLE_LINE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Specify the input format validation necessary for this text field. [Google
   * Workspace add-ons and Chat
   * apps](https://developers.google.com/workspace/extend):
   *
   * @param GoogleAppsCardV1Validation $validation
   */
  public function setValidation(GoogleAppsCardV1Validation $validation)
  {
    $this->validation = $validation;
  }
  /**
   * @return GoogleAppsCardV1Validation
   */
  public function getValidation()
  {
    return $this->validation;
  }
  /**
   * The value entered by a user, returned as part of a form input event. For
   * details about working with form inputs, see [Receive form
   * data](https://developers.google.com/workspace/chat/read-form-data).
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1TextInput::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1TextInput');
