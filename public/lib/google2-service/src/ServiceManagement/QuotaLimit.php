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

namespace Google\Service\ServiceManagement;

class QuotaLimit extends \Google\Model
{
  /**
   * Default number of tokens that can be consumed during the specified
   * duration. This is the number of tokens assigned when a client application
   * developer activates the service for his/her project. Specifying a value of
   * 0 will block all requests. This can be used if you are provisioning quota
   * to selected consumers and blocking others. Similarly, a value of -1 will
   * indicate an unlimited quota. No other negative values are allowed. Used by
   * group-based quotas only.
   *
   * @var string
   */
  public $defaultLimit;
  /**
   * Optional. User-visible, extended description for this quota limit. Should
   * be used only when more context is needed to understand this limit than
   * provided by the limit's display name (see: `display_name`).
   *
   * @var string
   */
  public $description;
  /**
   * User-visible display name for this limit. Optional. If not set, the UI will
   * provide a default display name based on the quota configuration. This field
   * can be used to override the default display name generated from the
   * configuration.
   *
   * @var string
   */
  public $displayName;
  /**
   * Duration of this limit in textual notation. Must be "100s" or "1d". Used by
   * group-based quotas only.
   *
   * @var string
   */
  public $duration;
  /**
   * Free tier value displayed in the Developers Console for this limit. The
   * free tier is the number of tokens that will be subtracted from the billed
   * amount when billing is enabled. This field can only be set on a limit with
   * duration "1d", in a billable group; it is invalid on any other limit. If
   * this field is not set, it defaults to 0, indicating that there is no free
   * tier for this service. Used by group-based quotas only.
   *
   * @var string
   */
  public $freeTier;
  /**
   * Maximum number of tokens that can be consumed during the specified
   * duration. Client application developers can override the default limit up
   * to this maximum. If specified, this value cannot be set to a value less
   * than the default limit. If not specified, it is set to the default limit.
   * To allow clients to apply overrides with no upper bound, set this to -1,
   * indicating unlimited maximum quota. Used by group-based quotas only.
   *
   * @var string
   */
  public $maxLimit;
  /**
   * The name of the metric this quota limit applies to. The quota limits with
   * the same metric will be checked together during runtime. The metric must be
   * defined within the service config.
   *
   * @var string
   */
  public $metric;
  /**
   * Name of the quota limit. The name must be provided, and it must be unique
   * within the service. The name can only include alphanumeric characters as
   * well as '-'. The maximum length of the limit name is 64 characters.
   *
   * @var string
   */
  public $name;
  /**
   * Specify the unit of the quota limit. It uses the same syntax as
   * MetricDescriptor.unit. The supported unit kinds are determined by the quota
   * backend system. Here are some examples: * "1/min/{project}" for quota per
   * minute per project. Note: the order of unit components is insignificant.
   * The "1" at the beginning is required to follow the metric unit syntax.
   *
   * @var string
   */
  public $unit;
  /**
   * Tiered limit values. You must specify this as a key:value pair, with an
   * integer value that is the maximum number of requests allowed for the
   * specified unit. Currently only STANDARD is supported.
   *
   * @var string[]
   */
  public $values;

  /**
   * Default number of tokens that can be consumed during the specified
   * duration. This is the number of tokens assigned when a client application
   * developer activates the service for his/her project. Specifying a value of
   * 0 will block all requests. This can be used if you are provisioning quota
   * to selected consumers and blocking others. Similarly, a value of -1 will
   * indicate an unlimited quota. No other negative values are allowed. Used by
   * group-based quotas only.
   *
   * @param string $defaultLimit
   */
  public function setDefaultLimit($defaultLimit)
  {
    $this->defaultLimit = $defaultLimit;
  }
  /**
   * @return string
   */
  public function getDefaultLimit()
  {
    return $this->defaultLimit;
  }
  /**
   * Optional. User-visible, extended description for this quota limit. Should
   * be used only when more context is needed to understand this limit than
   * provided by the limit's display name (see: `display_name`).
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
   * User-visible display name for this limit. Optional. If not set, the UI will
   * provide a default display name based on the quota configuration. This field
   * can be used to override the default display name generated from the
   * configuration.
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
   * Duration of this limit in textual notation. Must be "100s" or "1d". Used by
   * group-based quotas only.
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * Free tier value displayed in the Developers Console for this limit. The
   * free tier is the number of tokens that will be subtracted from the billed
   * amount when billing is enabled. This field can only be set on a limit with
   * duration "1d", in a billable group; it is invalid on any other limit. If
   * this field is not set, it defaults to 0, indicating that there is no free
   * tier for this service. Used by group-based quotas only.
   *
   * @param string $freeTier
   */
  public function setFreeTier($freeTier)
  {
    $this->freeTier = $freeTier;
  }
  /**
   * @return string
   */
  public function getFreeTier()
  {
    return $this->freeTier;
  }
  /**
   * Maximum number of tokens that can be consumed during the specified
   * duration. Client application developers can override the default limit up
   * to this maximum. If specified, this value cannot be set to a value less
   * than the default limit. If not specified, it is set to the default limit.
   * To allow clients to apply overrides with no upper bound, set this to -1,
   * indicating unlimited maximum quota. Used by group-based quotas only.
   *
   * @param string $maxLimit
   */
  public function setMaxLimit($maxLimit)
  {
    $this->maxLimit = $maxLimit;
  }
  /**
   * @return string
   */
  public function getMaxLimit()
  {
    return $this->maxLimit;
  }
  /**
   * The name of the metric this quota limit applies to. The quota limits with
   * the same metric will be checked together during runtime. The metric must be
   * defined within the service config.
   *
   * @param string $metric
   */
  public function setMetric($metric)
  {
    $this->metric = $metric;
  }
  /**
   * @return string
   */
  public function getMetric()
  {
    return $this->metric;
  }
  /**
   * Name of the quota limit. The name must be provided, and it must be unique
   * within the service. The name can only include alphanumeric characters as
   * well as '-'. The maximum length of the limit name is 64 characters.
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
   * Specify the unit of the quota limit. It uses the same syntax as
   * MetricDescriptor.unit. The supported unit kinds are determined by the quota
   * backend system. Here are some examples: * "1/min/{project}" for quota per
   * minute per project. Note: the order of unit components is insignificant.
   * The "1" at the beginning is required to follow the metric unit syntax.
   *
   * @param string $unit
   */
  public function setUnit($unit)
  {
    $this->unit = $unit;
  }
  /**
   * @return string
   */
  public function getUnit()
  {
    return $this->unit;
  }
  /**
   * Tiered limit values. You must specify this as a key:value pair, with an
   * integer value that is the maximum number of requests allowed for the
   * specified unit. Currently only STANDARD is supported.
   *
   * @param string[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return string[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QuotaLimit::class, 'Google_Service_ServiceManagement_QuotaLimit');
