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

namespace Google\Service\Classroom;

class StudentContext extends \Google\Model
{
  /**
   * Requesting user's submission id to be used for grade passback and to
   * identify the student when showing student work to the teacher. This is set
   * exactly when `supportsStudentWork` is `true`.
   *
   * @var string
   */
  public $submissionId;

  /**
   * Requesting user's submission id to be used for grade passback and to
   * identify the student when showing student work to the teacher. This is set
   * exactly when `supportsStudentWork` is `true`.
   *
   * @param string $submissionId
   */
  public function setSubmissionId($submissionId)
  {
    $this->submissionId = $submissionId;
  }
  /**
   * @return string
   */
  public function getSubmissionId()
  {
    return $this->submissionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StudentContext::class, 'Google_Service_Classroom_StudentContext');
