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

class BatchUpdateFormResponse extends \Google\Collection
{
  protected $collection_key = 'replies';
  protected $formType = Form::class;
  protected $formDataType = '';
  protected $repliesType = Response::class;
  protected $repliesDataType = 'array';
  protected $writeControlType = WriteControl::class;
  protected $writeControlDataType = '';

  /**
   * Based on the bool request field `include_form_in_response`, a form with all
   * applied mutations/updates is returned or not. This may be later than the
   * revision ID created by these changes.
   *
   * @param Form $form
   */
  public function setForm(Form $form)
  {
    $this->form = $form;
  }
  /**
   * @return Form
   */
  public function getForm()
  {
    return $this->form;
  }
  /**
   * The reply of the updates. This maps 1:1 with the update requests, although
   * replies to some requests may be empty.
   *
   * @param Response[] $replies
   */
  public function setReplies($replies)
  {
    $this->replies = $replies;
  }
  /**
   * @return Response[]
   */
  public function getReplies()
  {
    return $this->replies;
  }
  /**
   * The updated write control after applying the request.
   *
   * @param WriteControl $writeControl
   */
  public function setWriteControl(WriteControl $writeControl)
  {
    $this->writeControl = $writeControl;
  }
  /**
   * @return WriteControl
   */
  public function getWriteControl()
  {
    return $this->writeControl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchUpdateFormResponse::class, 'Google_Service_Forms_BatchUpdateFormResponse');
