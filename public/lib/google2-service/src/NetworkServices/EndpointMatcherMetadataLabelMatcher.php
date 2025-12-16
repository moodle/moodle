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

namespace Google\Service\NetworkServices;

class EndpointMatcherMetadataLabelMatcher extends \Google\Collection
{
  /**
   * Default value. Should not be used.
   */
  public const METADATA_LABEL_MATCH_CRITERIA_METADATA_LABEL_MATCH_CRITERIA_UNSPECIFIED = 'METADATA_LABEL_MATCH_CRITERIA_UNSPECIFIED';
  /**
   * At least one of the Labels specified in the matcher should match the
   * metadata presented by xDS client.
   */
  public const METADATA_LABEL_MATCH_CRITERIA_MATCH_ANY = 'MATCH_ANY';
  /**
   * The metadata presented by the xDS client should contain all of the labels
   * specified here.
   */
  public const METADATA_LABEL_MATCH_CRITERIA_MATCH_ALL = 'MATCH_ALL';
  protected $collection_key = 'metadataLabels';
  /**
   * Specifies how matching should be done. Supported values are: MATCH_ANY: At
   * least one of the Labels specified in the matcher should match the metadata
   * presented by xDS client. MATCH_ALL: The metadata presented by the xDS
   * client should contain all of the labels specified here. The selection is
   * determined based on the best match. For example, suppose there are three
   * EndpointPolicy resources P1, P2 and P3 and if P1 has a the matcher as
   * MATCH_ANY , P2 has MATCH_ALL , and P3 has MATCH_ALL . If a client with
   * label connects, the config from P1 will be selected. If a client with label
   * connects, the config from P2 will be selected. If a client with label
   * connects, the config from P3 will be selected. If there is more than one
   * best match, (for example, if a config P4 with selector exists and if a
   * client with label connects), pick up the one with older creation time.
   *
   * @var string
   */
  public $metadataLabelMatchCriteria;
  protected $metadataLabelsType = EndpointMatcherMetadataLabelMatcherMetadataLabels::class;
  protected $metadataLabelsDataType = 'array';

  /**
   * Specifies how matching should be done. Supported values are: MATCH_ANY: At
   * least one of the Labels specified in the matcher should match the metadata
   * presented by xDS client. MATCH_ALL: The metadata presented by the xDS
   * client should contain all of the labels specified here. The selection is
   * determined based on the best match. For example, suppose there are three
   * EndpointPolicy resources P1, P2 and P3 and if P1 has a the matcher as
   * MATCH_ANY , P2 has MATCH_ALL , and P3 has MATCH_ALL . If a client with
   * label connects, the config from P1 will be selected. If a client with label
   * connects, the config from P2 will be selected. If a client with label
   * connects, the config from P3 will be selected. If there is more than one
   * best match, (for example, if a config P4 with selector exists and if a
   * client with label connects), pick up the one with older creation time.
   *
   * Accepted values: METADATA_LABEL_MATCH_CRITERIA_UNSPECIFIED, MATCH_ANY,
   * MATCH_ALL
   *
   * @param self::METADATA_LABEL_MATCH_CRITERIA_* $metadataLabelMatchCriteria
   */
  public function setMetadataLabelMatchCriteria($metadataLabelMatchCriteria)
  {
    $this->metadataLabelMatchCriteria = $metadataLabelMatchCriteria;
  }
  /**
   * @return self::METADATA_LABEL_MATCH_CRITERIA_*
   */
  public function getMetadataLabelMatchCriteria()
  {
    return $this->metadataLabelMatchCriteria;
  }
  /**
   * The list of label value pairs that must match labels in the provided
   * metadata based on filterMatchCriteria This list can have at most 64
   * entries. The list can be empty if the match criteria is MATCH_ANY, to
   * specify a wildcard match (i.e this matches any client).
   *
   * @param EndpointMatcherMetadataLabelMatcherMetadataLabels[] $metadataLabels
   */
  public function setMetadataLabels($metadataLabels)
  {
    $this->metadataLabels = $metadataLabels;
  }
  /**
   * @return EndpointMatcherMetadataLabelMatcherMetadataLabels[]
   */
  public function getMetadataLabels()
  {
    return $this->metadataLabels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EndpointMatcherMetadataLabelMatcher::class, 'Google_Service_NetworkServices_EndpointMatcherMetadataLabelMatcher');
