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

class Option extends \Google\Model
{
  /**
   * Default value. Unused.
   */
  public const GO_TO_ACTION_GO_TO_ACTION_UNSPECIFIED = 'GO_TO_ACTION_UNSPECIFIED';
  /**
   * Go to the next section.
   */
  public const GO_TO_ACTION_NEXT_SECTION = 'NEXT_SECTION';
  /**
   * Go back to the beginning of the form.
   */
  public const GO_TO_ACTION_RESTART_FORM = 'RESTART_FORM';
  /**
   * Submit form immediately.
   */
  public const GO_TO_ACTION_SUBMIT_FORM = 'SUBMIT_FORM';
  /**
   * Section navigation type.
   *
   * @var string
   */
  public $goToAction;
  /**
   * Item ID of section header to go to.
   *
   * @var string
   */
  public $goToSectionId;
  protected $imageType = Image::class;
  protected $imageDataType = '';
  /**
   * Whether the option is "other". Currently only applies to `RADIO` and
   * `CHECKBOX` choice types, but is not allowed in a QuestionGroupItem.
   *
   * @var bool
   */
  public $isOther;
  /**
   * Required. The choice as presented to the user.
   *
   * @var string
   */
  public $value;

  /**
   * Section navigation type.
   *
   * Accepted values: GO_TO_ACTION_UNSPECIFIED, NEXT_SECTION, RESTART_FORM,
   * SUBMIT_FORM
   *
   * @param self::GO_TO_ACTION_* $goToAction
   */
  public function setGoToAction($goToAction)
  {
    $this->goToAction = $goToAction;
  }
  /**
   * @return self::GO_TO_ACTION_*
   */
  public function getGoToAction()
  {
    return $this->goToAction;
  }
  /**
   * Item ID of section header to go to.
   *
   * @param string $goToSectionId
   */
  public function setGoToSectionId($goToSectionId)
  {
    $this->goToSectionId = $goToSectionId;
  }
  /**
   * @return string
   */
  public function getGoToSectionId()
  {
    return $this->goToSectionId;
  }
  /**
   * Display image as an option.
   *
   * @param Image $image
   */
  public function setImage(Image $image)
  {
    $this->image = $image;
  }
  /**
   * @return Image
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Whether the option is "other". Currently only applies to `RADIO` and
   * `CHECKBOX` choice types, but is not allowed in a QuestionGroupItem.
   *
   * @param bool $isOther
   */
  public function setIsOther($isOther)
  {
    $this->isOther = $isOther;
  }
  /**
   * @return bool
   */
  public function getIsOther()
  {
    return $this->isOther;
  }
  /**
   * Required. The choice as presented to the user.
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
class_alias(Option::class, 'Google_Service_Forms_Option');
