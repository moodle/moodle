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

class KernelRootkit extends \Google\Model
{
  /**
   * Rootkit name, when available.
   *
   * @var string
   */
  public $name;
  /**
   * True if unexpected modifications of kernel code memory are present.
   *
   * @var bool
   */
  public $unexpectedCodeModification;
  /**
   * True if `ftrace` points are present with callbacks pointing to regions that
   * are not in the expected kernel or module code range.
   *
   * @var bool
   */
  public $unexpectedFtraceHandler;
  /**
   * True if interrupt handlers that are are not in the expected kernel or
   * module code regions are present.
   *
   * @var bool
   */
  public $unexpectedInterruptHandler;
  /**
   * True if kernel code pages that are not in the expected kernel or module
   * code regions are present.
   *
   * @var bool
   */
  public $unexpectedKernelCodePages;
  /**
   * True if `kprobe` points are present with callbacks pointing to regions that
   * are not in the expected kernel or module code range.
   *
   * @var bool
   */
  public $unexpectedKprobeHandler;
  /**
   * True if unexpected processes in the scheduler run queue are present. Such
   * processes are in the run queue, but not in the process task list.
   *
   * @var bool
   */
  public $unexpectedProcessesInRunqueue;
  /**
   * True if unexpected modifications of kernel read-only data memory are
   * present.
   *
   * @var bool
   */
  public $unexpectedReadOnlyDataModification;
  /**
   * True if system call handlers that are are not in the expected kernel or
   * module code regions are present.
   *
   * @var bool
   */
  public $unexpectedSystemCallHandler;

  /**
   * Rootkit name, when available.
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
   * True if unexpected modifications of kernel code memory are present.
   *
   * @param bool $unexpectedCodeModification
   */
  public function setUnexpectedCodeModification($unexpectedCodeModification)
  {
    $this->unexpectedCodeModification = $unexpectedCodeModification;
  }
  /**
   * @return bool
   */
  public function getUnexpectedCodeModification()
  {
    return $this->unexpectedCodeModification;
  }
  /**
   * True if `ftrace` points are present with callbacks pointing to regions that
   * are not in the expected kernel or module code range.
   *
   * @param bool $unexpectedFtraceHandler
   */
  public function setUnexpectedFtraceHandler($unexpectedFtraceHandler)
  {
    $this->unexpectedFtraceHandler = $unexpectedFtraceHandler;
  }
  /**
   * @return bool
   */
  public function getUnexpectedFtraceHandler()
  {
    return $this->unexpectedFtraceHandler;
  }
  /**
   * True if interrupt handlers that are are not in the expected kernel or
   * module code regions are present.
   *
   * @param bool $unexpectedInterruptHandler
   */
  public function setUnexpectedInterruptHandler($unexpectedInterruptHandler)
  {
    $this->unexpectedInterruptHandler = $unexpectedInterruptHandler;
  }
  /**
   * @return bool
   */
  public function getUnexpectedInterruptHandler()
  {
    return $this->unexpectedInterruptHandler;
  }
  /**
   * True if kernel code pages that are not in the expected kernel or module
   * code regions are present.
   *
   * @param bool $unexpectedKernelCodePages
   */
  public function setUnexpectedKernelCodePages($unexpectedKernelCodePages)
  {
    $this->unexpectedKernelCodePages = $unexpectedKernelCodePages;
  }
  /**
   * @return bool
   */
  public function getUnexpectedKernelCodePages()
  {
    return $this->unexpectedKernelCodePages;
  }
  /**
   * True if `kprobe` points are present with callbacks pointing to regions that
   * are not in the expected kernel or module code range.
   *
   * @param bool $unexpectedKprobeHandler
   */
  public function setUnexpectedKprobeHandler($unexpectedKprobeHandler)
  {
    $this->unexpectedKprobeHandler = $unexpectedKprobeHandler;
  }
  /**
   * @return bool
   */
  public function getUnexpectedKprobeHandler()
  {
    return $this->unexpectedKprobeHandler;
  }
  /**
   * True if unexpected processes in the scheduler run queue are present. Such
   * processes are in the run queue, but not in the process task list.
   *
   * @param bool $unexpectedProcessesInRunqueue
   */
  public function setUnexpectedProcessesInRunqueue($unexpectedProcessesInRunqueue)
  {
    $this->unexpectedProcessesInRunqueue = $unexpectedProcessesInRunqueue;
  }
  /**
   * @return bool
   */
  public function getUnexpectedProcessesInRunqueue()
  {
    return $this->unexpectedProcessesInRunqueue;
  }
  /**
   * True if unexpected modifications of kernel read-only data memory are
   * present.
   *
   * @param bool $unexpectedReadOnlyDataModification
   */
  public function setUnexpectedReadOnlyDataModification($unexpectedReadOnlyDataModification)
  {
    $this->unexpectedReadOnlyDataModification = $unexpectedReadOnlyDataModification;
  }
  /**
   * @return bool
   */
  public function getUnexpectedReadOnlyDataModification()
  {
    return $this->unexpectedReadOnlyDataModification;
  }
  /**
   * True if system call handlers that are are not in the expected kernel or
   * module code regions are present.
   *
   * @param bool $unexpectedSystemCallHandler
   */
  public function setUnexpectedSystemCallHandler($unexpectedSystemCallHandler)
  {
    $this->unexpectedSystemCallHandler = $unexpectedSystemCallHandler;
  }
  /**
   * @return bool
   */
  public function getUnexpectedSystemCallHandler()
  {
    return $this->unexpectedSystemCallHandler;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KernelRootkit::class, 'Google_Service_SecurityCommandCenter_KernelRootkit');
