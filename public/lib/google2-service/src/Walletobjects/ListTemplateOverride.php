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

namespace Google\Service\Walletobjects;

class ListTemplateOverride extends \Google\Model
{
  protected $firstRowOptionType = FirstRowOption::class;
  protected $firstRowOptionDataType = '';
  protected $secondRowOptionType = FieldSelector::class;
  protected $secondRowOptionDataType = '';
  protected $thirdRowOptionType = FieldSelector::class;
  protected $thirdRowOptionDataType = '';

  /**
   * Specifies from a predefined set of options or from a reference to the field
   * what will be displayed in the first row. To set this override, set the
   * FirstRowOption.fieldOption to the FieldSelector of your choice.
   *
   * @param FirstRowOption $firstRowOption
   */
  public function setFirstRowOption(FirstRowOption $firstRowOption)
  {
    $this->firstRowOption = $firstRowOption;
  }
  /**
   * @return FirstRowOption
   */
  public function getFirstRowOption()
  {
    return $this->firstRowOption;
  }
  /**
   * A reference to the field to be displayed in the second row. This option is
   * only displayed if there are not multiple user objects in a group. If there
   * is a group, the second row will always display a field shared by all
   * objects. To set this override, please set secondRowOption to the
   * FieldSelector of you choice.
   *
   * @param FieldSelector $secondRowOption
   */
  public function setSecondRowOption(FieldSelector $secondRowOption)
  {
    $this->secondRowOption = $secondRowOption;
  }
  /**
   * @return FieldSelector
   */
  public function getSecondRowOption()
  {
    return $this->secondRowOption;
  }
  /**
   * An unused/deprecated field. Setting it will have no effect on what the user
   * sees.
   *
   * @deprecated
   * @param FieldSelector $thirdRowOption
   */
  public function setThirdRowOption(FieldSelector $thirdRowOption)
  {
    $this->thirdRowOption = $thirdRowOption;
  }
  /**
   * @deprecated
   * @return FieldSelector
   */
  public function getThirdRowOption()
  {
    return $this->thirdRowOption;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListTemplateOverride::class, 'Google_Service_Walletobjects_ListTemplateOverride');
