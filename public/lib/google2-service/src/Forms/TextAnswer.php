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

class TextAnswer extends \Google\Model
{
  /**
   * Output only. The answer value. Formatting used for different kinds of
   * question: * ChoiceQuestion * `RADIO` or `DROP_DOWN`: A single string
   * corresponding to the option that was selected. * `CHECKBOX`: Multiple
   * strings corresponding to each option that was selected. * TextQuestion: The
   * text that the user entered. * ScaleQuestion: A string containing the number
   * that was selected. * DateQuestion * Without time or year: MM-DD e.g.
   * "05-19" * With year: YYYY-MM-DD e.g. "1986-05-19" * With time: MM-DD HH:MM
   * e.g. "05-19 14:51" * With year and time: YYYY-MM-DD HH:MM e.g. "1986-05-19
   * 14:51" * TimeQuestion: String with time or duration in HH:MM format e.g.
   * "14:51" * RowQuestion within QuestionGroupItem: The answer for each row of
   * a QuestionGroupItem is represented as a separate Answer. Each will contain
   * one string for `RADIO`-type choices or multiple strings for `CHECKBOX`
   * choices.
   *
   * @var string
   */
  public $value;

  /**
   * Output only. The answer value. Formatting used for different kinds of
   * question: * ChoiceQuestion * `RADIO` or `DROP_DOWN`: A single string
   * corresponding to the option that was selected. * `CHECKBOX`: Multiple
   * strings corresponding to each option that was selected. * TextQuestion: The
   * text that the user entered. * ScaleQuestion: A string containing the number
   * that was selected. * DateQuestion * Without time or year: MM-DD e.g.
   * "05-19" * With year: YYYY-MM-DD e.g. "1986-05-19" * With time: MM-DD HH:MM
   * e.g. "05-19 14:51" * With year and time: YYYY-MM-DD HH:MM e.g. "1986-05-19
   * 14:51" * TimeQuestion: String with time or duration in HH:MM format e.g.
   * "14:51" * RowQuestion within QuestionGroupItem: The answer for each row of
   * a QuestionGroupItem is represented as a separate Answer. Each will contain
   * one string for `RADIO`-type choices or multiple strings for `CHECKBOX`
   * choices.
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
class_alias(TextAnswer::class, 'Google_Service_Forms_TextAnswer');
