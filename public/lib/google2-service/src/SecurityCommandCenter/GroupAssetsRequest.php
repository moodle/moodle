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

namespace Google\Service\SecurityCommandCenter;

class GroupAssetsRequest extends \Google\Model
{
  /**
   * When compare_duration is set, the GroupResult's "state_change" property is
   * updated to indicate whether the asset was added, removed, or remained
   * present during the compare_duration period of time that precedes the
   * read_time. This is the time between (read_time - compare_duration) and
   * read_time. The state change value is derived based on the presence of the
   * asset at the two points in time. Intermediate state changes between the two
   * times don't affect the result. For example, the results aren't affected if
   * the asset is removed and re-created again. Possible "state_change" values
   * when compare_duration is specified: * "ADDED": indicates that the asset was
   * not present at the start of compare_duration, but present at
   * reference_time. * "REMOVED": indicates that the asset was present at the
   * start of compare_duration, but not present at reference_time. * "ACTIVE":
   * indicates that the asset was present at both the start and the end of the
   * time period defined by compare_duration and reference_time. If
   * compare_duration is not specified, then the only possible state_change is
   * "UNUSED", which will be the state_change set for all assets present at
   * read_time. If this field is set then `state_change` must be a specified
   * field in `group_by`.
   *
   * @var string
   */
  public $compareDuration;
  /**
   * Expression that defines the filter to apply across assets. The expression
   * is a list of zero or more restrictions combined via logical operators `AND`
   * and `OR`. Parentheses are supported, and `OR` has higher precedence than
   * `AND`. Restrictions have the form ` ` and may have a `-` character in front
   * of them to indicate negation. The fields map to those defined in the Asset
   * resource. Examples include: * name *
   * security_center_properties.resource_name * resource_properties.a_property *
   * security_marks.marks.marka The supported operators are: * `=` for all value
   * types. * `>`, `<`, `>=`, `<=` for integer values. * `:`, meaning substring
   * matching, for strings. The supported value types are: * string literals in
   * quotes. * integer literals without quotes. * boolean literals `true` and
   * `false` without quotes. The following field and operator combinations are
   * supported: * name: `=` * update_time: `=`, `>`, `<`, `>=`, `<=` Usage: This
   * should be milliseconds since epoch or an RFC3339 string. Examples:
   * `update_time = "2019-06-10T16:07:18-07:00"` `update_time = 1560208038000` *
   * create_time: `=`, `>`, `<`, `>=`, `<=` Usage: This should be milliseconds
   * since epoch or an RFC3339 string. Examples: `create_time =
   * "2019-06-10T16:07:18-07:00"` `create_time = 1560208038000` *
   * iam_policy.policy_blob: `=`, `:` * resource_properties: `=`, `:`, `>`, `<`,
   * `>=`, `<=` * security_marks.marks: `=`, `:` *
   * security_center_properties.resource_name: `=`, `:` *
   * security_center_properties.resource_display_name: `=`, `:` *
   * security_center_properties.resource_type: `=`, `:` *
   * security_center_properties.resource_parent: `=`, `:` *
   * security_center_properties.resource_parent_display_name: `=`, `:` *
   * security_center_properties.resource_project: `=`, `:` *
   * security_center_properties.resource_project_display_name: `=`, `:` *
   * security_center_properties.resource_owners: `=`, `:` For example,
   * `resource_properties.size = 100` is a valid filter string. Use a partial
   * match on the empty string to filter based on a property existing:
   * `resource_properties.my_property : ""` Use a negated partial match on the
   * empty string to filter based on a property not existing:
   * `-resource_properties.my_property : ""`
   *
   * @var string
   */
  public $filter;
  /**
   * Required. Expression that defines what assets fields to use for grouping.
   * The string value should follow SQL syntax: comma separated list of fields.
   * For example: "security_center_properties.resource_project,security_center_p
   * roperties.project". The following fields are supported when
   * compare_duration is not set: * security_center_properties.resource_project
   * * security_center_properties.resource_project_display_name *
   * security_center_properties.resource_type *
   * security_center_properties.resource_parent *
   * security_center_properties.resource_parent_display_name The following
   * fields are supported when compare_duration is set: *
   * security_center_properties.resource_type *
   * security_center_properties.resource_project_display_name *
   * security_center_properties.resource_parent_display_name
   *
   * @var string
   */
  public $groupBy;
  /**
   * The maximum number of results to return in a single response. Default is
   * 10, minimum is 1, maximum is 1000.
   *
   * @var int
   */
  public $pageSize;
  /**
   * The value returned by the last `GroupAssetsResponse`; indicates that this
   * is a continuation of a prior `GroupAssets` call, and that the system should
   * return the next page of data.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Time used as a reference point when filtering assets. The filter is limited
   * to assets existing at the supplied time and their values are those at that
   * specific time. Absence of this field will default to the API's version of
   * NOW.
   *
   * @var string
   */
  public $readTime;

  /**
   * When compare_duration is set, the GroupResult's "state_change" property is
   * updated to indicate whether the asset was added, removed, or remained
   * present during the compare_duration period of time that precedes the
   * read_time. This is the time between (read_time - compare_duration) and
   * read_time. The state change value is derived based on the presence of the
   * asset at the two points in time. Intermediate state changes between the two
   * times don't affect the result. For example, the results aren't affected if
   * the asset is removed and re-created again. Possible "state_change" values
   * when compare_duration is specified: * "ADDED": indicates that the asset was
   * not present at the start of compare_duration, but present at
   * reference_time. * "REMOVED": indicates that the asset was present at the
   * start of compare_duration, but not present at reference_time. * "ACTIVE":
   * indicates that the asset was present at both the start and the end of the
   * time period defined by compare_duration and reference_time. If
   * compare_duration is not specified, then the only possible state_change is
   * "UNUSED", which will be the state_change set for all assets present at
   * read_time. If this field is set then `state_change` must be a specified
   * field in `group_by`.
   *
   * @param string $compareDuration
   */
  public function setCompareDuration($compareDuration)
  {
    $this->compareDuration = $compareDuration;
  }
  /**
   * @return string
   */
  public function getCompareDuration()
  {
    return $this->compareDuration;
  }
  /**
   * Expression that defines the filter to apply across assets. The expression
   * is a list of zero or more restrictions combined via logical operators `AND`
   * and `OR`. Parentheses are supported, and `OR` has higher precedence than
   * `AND`. Restrictions have the form ` ` and may have a `-` character in front
   * of them to indicate negation. The fields map to those defined in the Asset
   * resource. Examples include: * name *
   * security_center_properties.resource_name * resource_properties.a_property *
   * security_marks.marks.marka The supported operators are: * `=` for all value
   * types. * `>`, `<`, `>=`, `<=` for integer values. * `:`, meaning substring
   * matching, for strings. The supported value types are: * string literals in
   * quotes. * integer literals without quotes. * boolean literals `true` and
   * `false` without quotes. The following field and operator combinations are
   * supported: * name: `=` * update_time: `=`, `>`, `<`, `>=`, `<=` Usage: This
   * should be milliseconds since epoch or an RFC3339 string. Examples:
   * `update_time = "2019-06-10T16:07:18-07:00"` `update_time = 1560208038000` *
   * create_time: `=`, `>`, `<`, `>=`, `<=` Usage: This should be milliseconds
   * since epoch or an RFC3339 string. Examples: `create_time =
   * "2019-06-10T16:07:18-07:00"` `create_time = 1560208038000` *
   * iam_policy.policy_blob: `=`, `:` * resource_properties: `=`, `:`, `>`, `<`,
   * `>=`, `<=` * security_marks.marks: `=`, `:` *
   * security_center_properties.resource_name: `=`, `:` *
   * security_center_properties.resource_display_name: `=`, `:` *
   * security_center_properties.resource_type: `=`, `:` *
   * security_center_properties.resource_parent: `=`, `:` *
   * security_center_properties.resource_parent_display_name: `=`, `:` *
   * security_center_properties.resource_project: `=`, `:` *
   * security_center_properties.resource_project_display_name: `=`, `:` *
   * security_center_properties.resource_owners: `=`, `:` For example,
   * `resource_properties.size = 100` is a valid filter string. Use a partial
   * match on the empty string to filter based on a property existing:
   * `resource_properties.my_property : ""` Use a negated partial match on the
   * empty string to filter based on a property not existing:
   * `-resource_properties.my_property : ""`
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Required. Expression that defines what assets fields to use for grouping.
   * The string value should follow SQL syntax: comma separated list of fields.
   * For example: "security_center_properties.resource_project,security_center_p
   * roperties.project". The following fields are supported when
   * compare_duration is not set: * security_center_properties.resource_project
   * * security_center_properties.resource_project_display_name *
   * security_center_properties.resource_type *
   * security_center_properties.resource_parent *
   * security_center_properties.resource_parent_display_name The following
   * fields are supported when compare_duration is set: *
   * security_center_properties.resource_type *
   * security_center_properties.resource_project_display_name *
   * security_center_properties.resource_parent_display_name
   *
   * @param string $groupBy
   */
  public function setGroupBy($groupBy)
  {
    $this->groupBy = $groupBy;
  }
  /**
   * @return string
   */
  public function getGroupBy()
  {
    return $this->groupBy;
  }
  /**
   * The maximum number of results to return in a single response. Default is
   * 10, minimum is 1, maximum is 1000.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * The value returned by the last `GroupAssetsResponse`; indicates that this
   * is a continuation of a prior `GroupAssets` call, and that the system should
   * return the next page of data.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * Time used as a reference point when filtering assets. The filter is limited
   * to assets existing at the supplied time and their values are those at that
   * specific time. Absence of this field will default to the API's version of
   * NOW.
   *
   * @param string $readTime
   */
  public function setReadTime($readTime)
  {
    $this->readTime = $readTime;
  }
  /**
   * @return string
   */
  public function getReadTime()
  {
    return $this->readTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GroupAssetsRequest::class, 'Google_Service_SecurityCommandCenter_GroupAssetsRequest');
