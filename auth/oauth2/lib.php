<?php

function auth_oauth2_extend_navigation_user_settings(navigation_node $useraccount,
                                                     stdClass $user,
                                                     context_user $context,
                                                     stdClass $course,
                                                     context_course $coursecontext) {

    if (!\core\session\manager::is_loggedinas()) {
        if (has_capability('auth/oauth2:managelinkedlogins', $context)) {

            if (get_config('auth_oauth2', 'allowlinkedlogins')) {
                $parent = $useraccount->parent->find('useraccount', navigation_node::TYPE_CONTAINER);
                $thingnode = $parent->add(get_string('linkedlogins', 'auth_oauth2'), new moodle_url('/auth/oauth2/linkedlogins.php'));
            }
        }
    }
}

