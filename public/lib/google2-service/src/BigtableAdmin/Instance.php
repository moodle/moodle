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

namespace Google\Service\BigtableAdmin;

class Instance extends \Google\Model
{
  /**
   * The state of the instance could not be determined.
   */
  public const STATE_STATE_NOT_KNOWN = 'STATE_NOT_KNOWN';
  /**
   * The instance has been successfully created and can serve requests to its
   * tables.
   */
  public const STATE_READY = 'READY';
  /**
   * The instance is currently being created, and may be destroyed if the
   * creation process encounters an error.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The type of the instance is unspecified. If set when creating an instance,
   * a `PRODUCTION` instance will be created. If set when updating an instance,
   * the type will be left unchanged.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * An instance meant for production use. `serve_nodes` must be set on the
   * cluster.
   */
  public const TYPE_PRODUCTION = 'PRODUCTION';
  /**
   * DEPRECATED: Prefer PRODUCTION for all use cases, as it no longer enforces a
   * higher minimum node count than DEVELOPMENT.
   */
  public const TYPE_DEVELOPMENT = 'DEVELOPMENT';
  /**
   * Output only. A commit timestamp representing when this Instance was
   * created. For instances created before this field was added (August 2021),
   * this value is `seconds: 0, nanos: 1`.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The descriptive name for this instance as it appears in UIs. Can
   * be changed at any time, but should be kept globally unique to avoid
   * confusion.
   *
   * @var string
   */
  public $displayName;
  /**
   * Labels are a flexible and lightweight mechanism for organizing cloud
   * resources into groups that reflect a customer's organizational needs and
   * deployment strategies. They can be used to filter resources and aggregate
   * metrics. * Label keys must be between 1 and 63 characters long and must
   * conform to the regular expression: `\p{Ll}\p{Lo}{0,62}`. * Label values
   * must be between 0 and 63 characters long and must conform to the regular
   * expression: `[\p{Ll}\p{Lo}\p{N}_-]{0,63}`. * No more than 64 labels can be
   * associated with a given resource. * Keys and values must both be under 128
   * bytes.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The unique name of the instance. Values are of the form
   * `projects/{project}/instances/a-z+[a-z0-9]`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. The current state of the instance.
   *
   * @var string
   */
  public $state;
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * resource. For example: - "123/environment": "production", -
   * "123/costCenter": "marketing" Tags and Labels (above) are both used to bind
   * metadata to resources, with different use-cases. See
   * https://cloud.google.com/resource-manager/docs/tags/tags-overview for an
   * in-depth overview on the difference between tags and labels.
   *
   * @var string[]
   */
  public $tags;
  /**
   * The type of the instance. Defaults to `PRODUCTION`.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. A commit timestamp representing when this Instance was
   * created. For instances created before this field was added (August 2021),
   * this value is `seconds: 0, nanos: 1`.
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
   * Required. The descriptive name for this instance as it appears in UIs. Can
   * be changed at any time, but should be kept globally unique to avoid
   * confusion.
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
   * Labels are a flexible and lightweight mechanism for organizing cloud
   * resources into groups that reflect a customer's organizational needs and
   * deployment strategies. They can be used to filter resources and aggregate
   * metrics. * Label keys must be between 1 and 63 characters long and must
   * conform to the regular expression: `\p{Ll}\p{Lo}{0,62}`. * Label values
   * must be between 0 and 63 characters long and must conform to the regular
   * expression: `[\p{Ll}\p{Lo}\p{N}_-]{0,63}`. * No more than 64 labels can be
   * associated with a given resource. * Keys and values must both be under 128
   * bytes.
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
   * The unique name of the instance. Values are of the form
   * `projects/{project}/instances/a-z+[a-z0-9]`.
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
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. The current state of the instance.
   *
   * Accepted values: STATE_NOT_KNOWN, READY, CREATING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * resource. For example: - "123/environment": "production", -
   * "123/costCenter": "marketing" Tags and Labels (above) are both used to bind
   * metadata to resources, with different use-cases. See
   * https://cloud.google.com/resource-manager/docs/tags/tags-overview for an
   * in-depth overview on the difference between tags and labels.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * The type of the instance. Defaults to `PRODUCTION`.
   *
   * Accepted values: TYPE_UNSPECIFIED, PRODUCTION, DEVELOPMENT
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Instance::class, 'Google_Service_BigtableAdmin_Instance');
