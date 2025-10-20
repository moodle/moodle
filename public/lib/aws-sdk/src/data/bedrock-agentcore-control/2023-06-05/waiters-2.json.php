<?php
// This file was auto-generated from sdk-root/src/data/bedrock-agentcore-control/2023-06-05/waiters-2.json
return [ 'version' => 2, 'waiters' => [ 'MemoryCreated' => [ 'delay' => 2, 'maxAttempts' => 60, 'operation' => 'GetMemory', 'acceptors' => [ [ 'matcher' => 'path', 'argument' => 'memory.status', 'state' => 'retry', 'expected' => 'CREATING', ], [ 'matcher' => 'path', 'argument' => 'memory.status', 'state' => 'success', 'expected' => 'ACTIVE', ], [ 'matcher' => 'path', 'argument' => 'memory.status', 'state' => 'failure', 'expected' => 'FAILED', ], ], ], ],];
