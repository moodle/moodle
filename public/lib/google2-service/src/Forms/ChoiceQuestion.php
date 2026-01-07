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

namespace Google\Service\Forms;

class ChoiceQuestion extends \Google\Collection
{
  /**
   * Default value. Unused.
   */
  public const TYPE_CHOICE_TYPE_UNSPECIFIED = 'CHOICE_TYPE_UNSPECIFIED';
  /**
   * Radio buttons: All choices are shown to the user, who can only pick one of
   * them.
   */
  public const TYPE_RADIO = 'RADIO';
  /**
   * Checkboxes: All choices are shown to the user, who can pick any number of
   * them.
   */
  public const TYPE_CHECKBOX = 'CHECKBOX';
  /**
   * Drop-down menu: The choices are only shown to the user on demand, otherwise
   * only the current choice is shown. Only one option can be chosen.
   */
  public const TYPE_DROP_DOWN = 'DROP_DOWN';
  protected $collection_key = 'options';
  protected $optionsType = Option::class;
  protected $optionsDataType = 'array';
  /**
   * Whether the options should be displayed in random order for different
   * instances of the quiz. This is often used to prevent cheating by
   * respondents who might be looking at another respondent's screen, or to
   * address bias in a survey that might be introduced by always putting the
   * same options first or last.
   *
   * @var bool
   */
  public $shuffle;
  /**
   * Required. The type of choice question.
   *
   * @var string
   */
  public $type;

  /**
   * Required. List of options that a respondent must choose from.
   *
   * @param Option[] $options
   */
  public function setOptions($options)
  {
    $this->options = $options;
  }
  /**
   * @return Option[]
   */
  public function getOptions()
  {
    return $this->options;
  }
  /**
   * Whether the options should be displayed in random order for different
   * instances of the quiz. This is often used to prevent cheating by
   * respondents who might be looking at another respondent's screen, or to
   * address bias in a survey that might be introduced by always putting the
   * same options first or last.
   *
   * @param bool $shuffle
   */
  public function setShuffle($shuffle)
  {
    $this->shuffle = $shuffle;
  }
  /**
   * @return bool
   */
  public function getShuffle()
  {
    return $this->shuffle;
  }
  /**
   * Required. The type of choice question.
   *
   * Accepted values: CHOICE_TYPE_UNSPECIFIED, RADIO, CHECKBOX, DROP_DOWN
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChoiceQuestion::class, 'Google_Service_Forms_ChoiceQuestion');
