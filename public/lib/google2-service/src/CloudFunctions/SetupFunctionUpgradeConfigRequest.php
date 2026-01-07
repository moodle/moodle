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

namespace Google\Service\CloudFunctions;

class SetupFunctionUpgradeConfigRequest extends \Google\Model
{
  /**
   * Optional. The trigger's service account. The service account must have
   * permission to invoke Cloud Run services, the permission is
   * `run.routes.invoke`. If empty, defaults to the Compute Engine default
   * service account: `{project_number}-compute@developer.gserviceaccount.com`.
   *
   * @var string
   */
  public $triggerServiceAccount;

  /**
   * Optional. The trigger's service account. The service account must have
   * permission to invoke Cloud Run services, the permission is
   * `run.routes.invoke`. If empty, defaults to the Compute Engine default
   * service account: `{project_number}-compute@developer.gserviceaccount.com`.
   *
   * @param string $triggerServiceAccount
   */
  public function setTriggerServiceAccount($triggerServiceAccount)
  {
    $this->triggerServiceAccount = $triggerServiceAccount;
  }
  /**
   * @return string
   */
  public function getTriggerServiceAccount()
  {
    return $this->triggerServiceAccount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SetupFunctionUpgradeConfigRequest::class, 'Google_Service_CloudFunctions_SetupFunctionUpgradeConfigRequest');
