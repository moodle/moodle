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

class InterconnectAttachmentConfigurationConstraints extends \Google\Collection
{
  /**
   * MD5_OPTIONAL: BGP MD5 authentication is supported and can optionally be
   * configured.
   */
  public const BGP_MD5_MD5_OPTIONAL = 'MD5_OPTIONAL';
  /**
   * MD5_REQUIRED: BGP MD5 authentication must be configured.
   */
  public const BGP_MD5_MD5_REQUIRED = 'MD5_REQUIRED';
  /**
   * MD5_UNSUPPORTED: BGP MD5 authentication must not be configured
   */
  public const BGP_MD5_MD5_UNSUPPORTED = 'MD5_UNSUPPORTED';
  protected $collection_key = 'bgpPeerAsnRanges';
  /**
   * Output only. [Output Only] Whether the attachment's BGP session
   * requires/allows/disallows BGP MD5 authentication. This can take one of the
   * following values: MD5_OPTIONAL, MD5_REQUIRED, MD5_UNSUPPORTED.
   *
   * For example, a Cross-Cloud Interconnect connection to a remote cloud
   * provider that requires BGP MD5 authentication has the
   * interconnectRemoteLocation attachment_configuration_constraints.bgp_md5
   * field set to MD5_REQUIRED, and that property is propagated to the
   * attachment. Similarly, if BGP MD5 is MD5_UNSUPPORTED, an error is returned
   * if MD5 is requested.
   *
   * @var string
   */
  public $bgpMd5;
  protected $bgpPeerAsnRangesType = InterconnectAttachmentConfigurationConstraintsBgpPeerASNRange::class;
  protected $bgpPeerAsnRangesDataType = 'array';

  /**
   * Output only. [Output Only] Whether the attachment's BGP session
   * requires/allows/disallows BGP MD5 authentication. This can take one of the
   * following values: MD5_OPTIONAL, MD5_REQUIRED, MD5_UNSUPPORTED.
   *
   * For example, a Cross-Cloud Interconnect connection to a remote cloud
   * provider that requires BGP MD5 authentication has the
   * interconnectRemoteLocation attachment_configuration_constraints.bgp_md5
   * field set to MD5_REQUIRED, and that property is propagated to the
   * attachment. Similarly, if BGP MD5 is MD5_UNSUPPORTED, an error is returned
   * if MD5 is requested.
   *
   * Accepted values: MD5_OPTIONAL, MD5_REQUIRED, MD5_UNSUPPORTED
   *
   * @param self::BGP_MD5_* $bgpMd5
   */
  public function setBgpMd5($bgpMd5)
  {
    $this->bgpMd5 = $bgpMd5;
  }
  /**
   * @return self::BGP_MD5_*
   */
  public function getBgpMd5()
  {
    return $this->bgpMd5;
  }
  /**
   * Output only. [Output Only] List of ASN ranges that the remote location is
   * known to support. Formatted as an array of inclusive ranges {min: min-
   * value, max: max-value}. For example, [{min: 123, max: 123}, {min: 64512,
   * max: 65534}] allows the peer ASN to be 123 or anything in the range
   * 64512-65534.
   *
   * This field is only advisory. Although the API accepts other ranges, these
   * are the ranges that we recommend.
   *
   * @param InterconnectAttachmentConfigurationConstraintsBgpPeerASNRange[] $bgpPeerAsnRanges
   */
  public function setBgpPeerAsnRanges($bgpPeerAsnRanges)
  {
    $this->bgpPeerAsnRanges = $bgpPeerAsnRanges;
  }
  /**
   * @return InterconnectAttachmentConfigurationConstraintsBgpPeerASNRange[]
   */
  public function getBgpPeerAsnRanges()
  {
    return $this->bgpPeerAsnRanges;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectAttachmentConfigurationConstraints::class, 'Google_Service_Compute_InterconnectAttachmentConfigurationConstraints');
