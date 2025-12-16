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

class PublishSettings extends \Google\Model
{
  protected $publishStateType = PublishState::class;
  protected $publishStateDataType = '';

  /**
   * Optional. The publishing state of a form. When updating `publish_state`,
   * both `is_published` and `is_accepting_responses` must be set. However,
   * setting `is_accepting_responses` to `true` and `is_published` to `false`
   * isn't supported and returns an error.
   *
   * @param PublishState $publishState
   */
  public function setPublishState(PublishState $publishState)
  {
    $this->publishState = $publishState;
  }
  /**
   * @return PublishState
   */
  public function getPublishState()
  {
    return $this->publishState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PublishSettings::class, 'Google_Service_Forms_PublishSettings');
