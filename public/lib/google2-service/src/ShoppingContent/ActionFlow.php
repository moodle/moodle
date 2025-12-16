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

class ActionFlow extends \Google\Collection
{
  protected $collection_key = 'inputs';
  /**
   * Label for the button to trigger the action from the action dialog. For
   * example: "Request review"
   *
   * @var string
   */
  public $dialogButtonLabel;
  protected $dialogCalloutType = Callout::class;
  protected $dialogCalloutDataType = '';
  protected $dialogMessageType = TextWithTooltip::class;
  protected $dialogMessageDataType = '';
  /**
   * Title of the request dialog. For example: "Before you request a review"
   *
   * @var string
   */
  public $dialogTitle;
  /**
   * Not for display but need to be sent back for the selected action flow.
   *
   * @var string
   */
  public $id;
  protected $inputsType = InputField::class;
  protected $inputsDataType = 'array';
  /**
   * Text value describing the intent for the action flow. It can be used as an
   * input label if merchant needs to pick one of multiple flows. For example:
   * "I disagree with the issue"
   *
   * @var string
   */
  public $label;

  /**
   * Label for the button to trigger the action from the action dialog. For
   * example: "Request review"
   *
   * @param string $dialogButtonLabel
   */
  public function setDialogButtonLabel($dialogButtonLabel)
  {
    $this->dialogButtonLabel = $dialogButtonLabel;
  }
  /**
   * @return string
   */
  public function getDialogButtonLabel()
  {
    return $this->dialogButtonLabel;
  }
  /**
   * Important message to be highlighted in the request dialog. For example:
   * "You can only request a review for disagreeing with this issue once. If
   * it's not approved, you'll need to fix the issue and wait a few days before
   * you can request another review."
   *
   * @param Callout $dialogCallout
   */
  public function setDialogCallout(Callout $dialogCallout)
  {
    $this->dialogCallout = $dialogCallout;
  }
  /**
   * @return Callout
   */
  public function getDialogCallout()
  {
    return $this->dialogCallout;
  }
  /**
   * Message displayed in the request dialog. For example: "Make sure you've
   * fixed all your country-specific issues. If not, you may have to wait 7 days
   * to request another review". There may be an more information to be shown in
   * a tooltip.
   *
   * @param TextWithTooltip $dialogMessage
   */
  public function setDialogMessage(TextWithTooltip $dialogMessage)
  {
    $this->dialogMessage = $dialogMessage;
  }
  /**
   * @return TextWithTooltip
   */
  public function getDialogMessage()
  {
    return $this->dialogMessage;
  }
  /**
   * Title of the request dialog. For example: "Before you request a review"
   *
   * @param string $dialogTitle
   */
  public function setDialogTitle($dialogTitle)
  {
    $this->dialogTitle = $dialogTitle;
  }
  /**
   * @return string
   */
  public function getDialogTitle()
  {
    return $this->dialogTitle;
  }
  /**
   * Not for display but need to be sent back for the selected action flow.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * A list of input fields.
   *
   * @param InputField[] $inputs
   */
  public function setInputs($inputs)
  {
    $this->inputs = $inputs;
  }
  /**
   * @return InputField[]
   */
  public function getInputs()
  {
    return $this->inputs;
  }
  /**
   * Text value describing the intent for the action flow. It can be used as an
   * input label if merchant needs to pick one of multiple flows. For example:
   * "I disagree with the issue"
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActionFlow::class, 'Google_Service_ShoppingContent_ActionFlow');
