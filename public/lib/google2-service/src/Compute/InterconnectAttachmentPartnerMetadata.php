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

namespace Google\Service\Compute;

class InterconnectAttachmentPartnerMetadata extends \Google\Model
{
  /**
   * Plain text name of the Interconnect this attachment is connected to, as
   * displayed in the Partner's portal. For instance "Chicago 1". This value may
   * be validated to match approved Partner values.
   *
   * @var string
   */
  public $interconnectName;
  /**
   * Plain text name of the Partner providing this attachment. This value may be
   * validated to match approved Partner values.
   *
   * @var string
   */
  public $partnerName;
  /**
   * URL of the Partner's portal for this Attachment. Partners may customise
   * this to be a deep link to the specific resource on the Partner portal. This
   * value may be validated to match approved Partner values.
   *
   * @var string
   */
  public $portalUrl;

  /**
   * Plain text name of the Interconnect this attachment is connected to, as
   * displayed in the Partner's portal. For instance "Chicago 1". This value may
   * be validated to match approved Partner values.
   *
   * @param string $interconnectName
   */
  public function setInterconnectName($interconnectName)
  {
    $this->interconnectName = $interconnectName;
  }
  /**
   * @return string
   */
  public function getInterconnectName()
  {
    return $this->interconnectName;
  }
  /**
   * Plain text name of the Partner providing this attachment. This value may be
   * validated to match approved Partner values.
   *
   * @param string $partnerName
   */
  public function setPartnerName($partnerName)
  {
    $this->partnerName = $partnerName;
  }
  /**
   * @return string
   */
  public function getPartnerName()
  {
    return $this->partnerName;
  }
  /**
   * URL of the Partner's portal for this Attachment. Partners may customise
   * this to be a deep link to the specific resource on the Partner portal. This
   * value may be validated to match approved Partner values.
   *
   * @param string $portalUrl
   */
  public function setPortalUrl($portalUrl)
  {
    $this->portalUrl = $portalUrl;
  }
  /**
   * @return string
   */
  public function getPortalUrl()
  {
    return $this->portalUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectAttachmentPartnerMetadata::class, 'Google_Service_Compute_InterconnectAttachmentPartnerMetadata');
