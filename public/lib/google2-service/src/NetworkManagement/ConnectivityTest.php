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

namespace Google\Service\NetworkManagement;

class ConnectivityTest extends \Google\Collection
{
  protected $collection_key = 'relatedProjects';
  /**
   * Whether the analysis should skip firewall checking. Default value is false.
   *
   * @var bool
   */
  public $bypassFirewallChecks;
  /**
   * Output only. The time the test was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The user-supplied description of the Connectivity Test. Maximum of 512
   * characters.
   *
   * @var string
   */
  public $description;
  protected $destinationType = Endpoint::class;
  protected $destinationDataType = '';
  /**
   * Output only. The display name of a Connectivity Test.
   *
   * @var string
   */
  public $displayName;
  /**
   * Resource labels to represent user-provided metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Unique name of the resource using the form:
   * `projects/{project_id}/locations/global/connectivityTests/{test_id}`
   *
   * @var string
   */
  public $name;
  protected $probingDetailsType = ProbingDetails::class;
  protected $probingDetailsDataType = '';
  /**
   * IP Protocol of the test. When not provided, "TCP" is assumed.
   *
   * @var string
   */
  public $protocol;
  protected $reachabilityDetailsType = ReachabilityDetails::class;
  protected $reachabilityDetailsDataType = '';
  /**
   * Other projects that may be relevant for reachability analysis. This is
   * applicable to scenarios where a test can cross project boundaries.
   *
   * @var string[]
   */
  public $relatedProjects;
  protected $returnReachabilityDetailsType = ReachabilityDetails::class;
  protected $returnReachabilityDetailsDataType = '';
  /**
   * Whether run analysis for the return path from destination to source.
   * Default value is false.
   *
   * @var bool
   */
  public $roundTrip;
  protected $sourceType = Endpoint::class;
  protected $sourceDataType = '';
  /**
   * Output only. The time the test's configuration was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Whether the analysis should skip firewall checking. Default value is false.
   *
   * @param bool $bypassFirewallChecks
   */
  public function setBypassFirewallChecks($bypassFirewallChecks)
  {
    $this->bypassFirewallChecks = $bypassFirewallChecks;
  }
  /**
   * @return bool
   */
  public function getBypassFirewallChecks()
  {
    return $this->bypassFirewallChecks;
  }
  /**
   * Output only. The time the test was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The user-supplied description of the Connectivity Test. Maximum of 512
   * characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. Destination specification of the Connectivity Test. You can use a
   * combination of destination IP address, URI of a supported endpoint, project
   * ID, or VPC network to identify the destination location. Reachability
   * analysis proceeds even if the destination location is ambiguous. However,
   * the test result might include endpoints or use a destination that you don't
   * intend to test.
   *
   * @param Endpoint $destination
   */
  public function setDestination(Endpoint $destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return Endpoint
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * Output only. The display name of a Connectivity Test.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Resource labels to represent user-provided metadata.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Identifier. Unique name of the resource using the form:
   * `projects/{project_id}/locations/global/connectivityTests/{test_id}`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The probing details of this test from the latest run, present
   * for applicable tests only. The details are updated when creating a new
   * test, updating an existing test, or triggering a one-time rerun of an
   * existing test.
   *
   * @param ProbingDetails $probingDetails
   */
  public function setProbingDetails(ProbingDetails $probingDetails)
  {
    $this->probingDetails = $probingDetails;
  }
  /**
   * @return ProbingDetails
   */
  public function getProbingDetails()
  {
    return $this->probingDetails;
  }
  /**
   * IP Protocol of the test. When not provided, "TCP" is assumed.
   *
   * @param string $protocol
   */
  public function setProtocol($protocol)
  {
    $this->protocol = $protocol;
  }
  /**
   * @return string
   */
  public function getProtocol()
  {
    return $this->protocol;
  }
  /**
   * Output only. The reachability details of this test from the latest run. The
   * details are updated when creating a new test, updating an existing test, or
   * triggering a one-time rerun of an existing test.
   *
   * @param ReachabilityDetails $reachabilityDetails
   */
  public function setReachabilityDetails(ReachabilityDetails $reachabilityDetails)
  {
    $this->reachabilityDetails = $reachabilityDetails;
  }
  /**
   * @return ReachabilityDetails
   */
  public function getReachabilityDetails()
  {
    return $this->reachabilityDetails;
  }
  /**
   * Other projects that may be relevant for reachability analysis. This is
   * applicable to scenarios where a test can cross project boundaries.
   *
   * @param string[] $relatedProjects
   */
  public function setRelatedProjects($relatedProjects)
  {
    $this->relatedProjects = $relatedProjects;
  }
  /**
   * @return string[]
   */
  public function getRelatedProjects()
  {
    return $this->relatedProjects;
  }
  /**
   * Output only. The reachability details of this test from the latest run for
   * the return path. The details are updated when creating a new test, updating
   * an existing test, or triggering a one-time rerun of an existing test.
   *
   * @param ReachabilityDetails $returnReachabilityDetails
   */
  public function setReturnReachabilityDetails(ReachabilityDetails $returnReachabilityDetails)
  {
    $this->returnReachabilityDetails = $returnReachabilityDetails;
  }
  /**
   * @return ReachabilityDetails
   */
  public function getReturnReachabilityDetails()
  {
    return $this->returnReachabilityDetails;
  }
  /**
   * Whether run analysis for the return path from destination to source.
   * Default value is false.
   *
   * @param bool $roundTrip
   */
  public function setRoundTrip($roundTrip)
  {
    $this->roundTrip = $roundTrip;
  }
  /**
   * @return bool
   */
  public function getRoundTrip()
  {
    return $this->roundTrip;
  }
  /**
   * Required. Source specification of the Connectivity Test. You can use a
   * combination of source IP address, URI of a supported endpoint, project ID,
   * or VPC network to identify the source location. Reachability analysis might
   * proceed even if the source location is ambiguous. However, the test result
   * might include endpoints or use a source that you don't intend to test.
   *
   * @param Endpoint $source
   */
  public function setSource(Endpoint $source)
  {
    $this->source = $source;
  }
  /**
   * @return Endpoint
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Output only. The time the test's configuration was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConnectivityTest::class, 'Google_Service_NetworkManagement_ConnectivityTest');
