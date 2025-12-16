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

namespace Google\Service\ShoppingContent;

class InputFieldChoiceInput extends \Google\Collection
{
  protected $collection_key = 'options';
  protected $optionsType = InputFieldChoiceInputChoiceInputOption::class;
  protected $optionsDataType = 'array';

  /**
   * A list of choices. Only one option can be selected.
   *
   * @param InputFieldChoiceInputChoiceInputOption[] $options
   */
  public function setOptions($options)
  {
    $this->options = $options;
  }
  /**
   * @return InputFieldChoiceInputChoiceInputOption[]
   */
  public function getOptions()
  {
    return $this->options;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InputFieldChoiceInput::class, 'Google_Service_ShoppingContent_InputFieldChoiceInput');
