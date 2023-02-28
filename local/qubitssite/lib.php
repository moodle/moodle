<?php

function local_qubitssite_pluginfile($course, $cm, $context, $filearea, $args,
                               $forcedownload, array $options=array()) {
    if ($context->contextlevel != CONTEXT_SYSTEM) {
        send_file_not_found();
    }
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'local_qubitssite', $filearea, $args[0], '/', $args[1]);
    send_stored_file($file);
}