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

namespace Google\Service\PaymentsResellerSubscription;

class CycleOptions extends \Google\Model
{
  protected $initialCycleDurationType = Duration::class;
  protected $initialCycleDurationDataType = '';

  /**
   * Optional. The duration of the initial cycle. Only `DAY` is supported. If
   * set, Google will start the subscription with this initial cycle duration
   * starting at the request time (see available methods below). A prorated
   * charge will be applied. This option is available to the following methods:
   * - partners.subscriptions.provision - partners.subscriptions.resume -
   * partners.userSessions.generate
   *
   * @param Duration $initialCycleDuration
   */
  public function setInitialCycleDuration(Duration $initialCycleDuration)
  {
    $this->initialCycleDuration = $initialCycleDuration;
  }
  /**
   * @return Duration
   */
  public function getInitialCycleDuration()
  {
    return $this->initialCycleDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CycleOptions::class, 'Google_Service_PaymentsResellerSubscription_CycleOptions');
