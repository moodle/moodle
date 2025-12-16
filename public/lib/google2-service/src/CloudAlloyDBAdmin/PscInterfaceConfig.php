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

namespace Google\Service\CloudAlloyDBAdmin;

class PscInterfaceConfig extends \Google\Model
{
  /**
   * The network attachment resource created in the consumer network to which
   * the PSC interface will be linked. This is of the format: "projects/${CONSUM
   * ER_PROJECT}/regions/${REGION}/networkAttachments/${NETWORK_ATTACHMENT_NAME}
   * ". The network attachment must be in the same region as the instance.
   *
   * @var string
   */
  public $networkAttachmentResource;

  /**
   * The network attachment resource created in the consumer network to which
   * the PSC interface will be linked. This is of the format: "projects/${CONSUM
   * ER_PROJECT}/regions/${REGION}/networkAttachments/${NETWORK_ATTACHMENT_NAME}
   * ". The network attachment must be in the same region as the instance.
   *
   * @param string $networkAttachmentResource
   */
  public function setNetworkAttachmentResource($networkAttachmentResource)
  {
    $this->networkAttachmentResource = $networkAttachmentResource;
  }
  /**
   * @return string
   */
  public function getNetworkAttachmentResource()
  {
    return $this->networkAttachmentResource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PscInterfaceConfig::class, 'Google_Service_CloudAlloyDBAdmin_PscInterfaceConfig');
