<?PHP  

/************************************
 * Automatic group generataion
 ************************************/
 
 // @@@ TO DO - Some of this code could be simplified a lot I realised 
 // after I wrote it and got it working! 
 
/**
 * Seeds the random number generator used by groups_create_automatic_grouping. 
 * This must be called before using groups_create_automatic_grouping and should 
 * only be called once in each script even if you are calling 
 * groups_create_automatic_grouping more than once.
 */ 
function groups_seed_random_number_generator() { 
    $seed = (double)microtime()*1234567 ;
    srand($seed);
}
 

/**
 * Distributes students into groups randomly and creates a grouping with those 
 * groups.
 * 
 * You need to call groups_seed_random_number_generator() at some point in your 
 * script before calling this function. 
 * 
 * Note that this function does not distribute teachers into groups - this still 
 * needs to be done manually. 
 * 
 * @param int $courseid The id of the course that the grouping should belong to
 * @param int $nostudentspergroup The number of students to put in each group - 
 * this can be set to false if you prefer to specify the number of groups 
 * instead
 * @param int $nogroups The number of groups - this can be set to false if you 
 * prefer to specify the number of student in each group. If both are specified 
 * then $nostudentspergroup takes precedence. If neither is
 * specified then the function does nothing and returns false. 
 * @param boolean $distribevenly If $noofstudentspergroup is specified, then 
 * if this is set to true, any leftover students are distributed evenly among 
 * the groups, whereas if it is set to false then they are put in a separate 
 * group. 
 * @param object $groupsettings The default settings to give each group. 
 * This should contain prefix and defaultgroupdescription fields. The groups 
 * are named with the prefix followed by 1, 2, etc. and given the
 * default group description set. 
 * @param int $groupid If this is not set to false, then only students in the 
 * specified group are distributed into groups, not all the students enrolled on 
 * the course. 
 * @param boolean $alphabetical If this is set to true, then the students are 
 * not distributed randomly but in alphabetical order of last name. 
 * @return int The id of the grouping
 */
function groups_create_automatic_grouping($courseid, $nostudentspergroup, 
                                          $nogroups, $distribevenly, 
                                          $groupingsettings, 
                                          $groupid = false, 
                                          $alphabetical = false) {

    if (!$nostudentspergroup and !$noteacherspergroup and !$nogroups) {
        $groupingid = false;
    } else {
        // Set $userids to the list of students that we want to put into groups 
        // in the grouping
        if (!$groupid) {
            $users = get_course_students($courseid);
            $userids = groups_users_to_userids($users); 
        } else {
            $userids = groups_get_members($groupid);
        }

        // Distribute the users into sets according to the parameters specified    
        $userarrays = groups_distribute_in_random_sets($userids, 
            $nostudentspergroup, $nogroups, $distribevenly, !$alphabetical);  

        if (!$userarrays) {
            $groupingid = false;
        } else { 
            // Create the grouping that the groups we create will go into   
            $groupingid = groups_create_grouping($courseid, $groupingsettings);

            // Get the prefix for the names of each group and default group 
            // description to give each group
            if (!$groupingsettings->prefix) {
                $prefix = get_string('defaultgroupprefix', 'groups');
            } else {
                $prefix = $groupingsettings->prefix;
            }

            if (!$groupingsettings->defaultgroupdescription) {
                $defaultgroupdescription = '';
            } else {
                $defaultgroupdescription = $groupingsettings->defaultgroupdescription;
            }

            // Now create a group for each set of students, add the group to the 
            // grouping and then add the students
            $i = 1;
            foreach ($userarrays as $userids) {
                $groupsettings->name = $prefix.' '.$i;
                $groupsettings->description = $defaultgroupdescription;
                $i++;
                $groupid = groups_create_group($courseid, $groupsettings);
                $groupadded = groups_add_group_to_grouping($groupid, 
                    $groupingid);
                if (!$groupid or !$groupadded) {
                    $groupingid = false;
                } else {
                    if ($userids) {
                        foreach($userids as $userid) {
                            $usersadded = groups_add_member($groupid, $userid);
                            // If unsuccessful just carry on I guess
                        }
                    }
                }
            }
        }
    }
    return $groupingid;
                                          }


/**
 * Takes an array and a set size, puts the elements of the array into an array 
 * of arrays of size $setsize randomly. 
 * @param array $array The array to distribute into random sets
 * @param int $setsize The size of each set - this can be set to false, if you 
 * would prefer to specify the number of sets.  
 * @param int $nosets The number of sets - this can be set to false if you would 
 * prefer to specify the setsize. 
 * If both $setsize and $nosets are set then $setsize takes precedence. If both 
 * are set to false then the function does nothing and returns false.  
 * @param $distribevenly boolean Determines how extra elements are distributed 
 * if $setsize doesn't divide exactly into the number of elements if $setsize is 
 * specified. If it is true then extra elements will be distributed evenly 
 * amongst the sets, whereas if it is set to false then the remaining elements 
 * will be put into a separate set. 
 * @param boolean $randomise If this is true then the elements of array will be 
 * put into the arrays in a random order, otherwise they will be put into the 
 * array in the same order as the original array. 
 * @return array The array of arrays of elements generated. 
 */
function groups_distribute_in_random_sets($array, $setsize, $nosets, 
                                          $distribevenly = true, 
                                          $randomise = true) {
    $noelements = count($array);    

    // Create a list of the numbers 1,..., $noelements, in either random order 
    // or in numerical order depending on whether $randomise has been set.    
    if ($randomise) {
        $orderarray = groups_random_list($noelements);
    } else {
        // Just create the array (1,2,3,....)
        $orderarray = array();
        for($i = 0; $i < $noelements; $i++) {
            array_push($orderarray, $i);
        }
    }

    // Now use the ordering in $orderarray to generate the new arrays
    $arrayofrandomsets = array(); // 

    for ($i = 0; $i < $noelements; $i++) {      
        $arrayofrandomsets[$arrayno][$i] = $array[$orderarray[$i]];
        if (groups_last_element_in_set($noelements, $setsize, $nosets, 
            $distribevenly, $i) 
            and $i != $noelements - 1) {
                $arrayno++;
                $arrayofrandomsets[$arrayno] = array(); 
            }

    }

    return  $arrayofrandomsets;
                                          }

/**
 * Returns an array of the numbers 0,..,$size - 1 in random order 
 * @param int $size the number of numbers to return in a random order 
 * @return array The array of numbers in a random order
 */
function groups_random_list($size) {
    $orderarray = array();
    $noelementsset = 0;
    while($noelementsset != $size) {
        $newelement = rand() % $size;
        // If the array doesn't already contain the element, add it.
        if (array_search($newelement, $orderarray) === false) {
            array_push($orderarray, $newelement);
            $noelementsset++;
        }
    }

    return $orderarray;
}

/**
 * A helper function for groups_distribute_in_random_sets(). 
 * When distributing elements into sets, determines if a given element is the 
 * last element in the set. 
 * @param int $totalnoelements The total number of elements being distributed. 
 * @param int $setsize See groups_distribute_in_random_sets()
 * @param int $nosets See groups_distribute_in_random_sets()
 * @param boolean $distribevenly  See groups_distribute_in_random_sets()
 * @param int $elementno The element number that we are considering i.e. if this 
 * is the 15th element then this would be 15. 
 * @return boolean True if the element under consideration would be the last 
 * element in the set, fals otherwise. 
 */
function groups_last_element_in_set($totalnoelements, $setsize, $nosets, 
                                    $distribevenly, $elementno) {
    $lastelement = false;
    $elementno = $elementno + 1; // Counting from zero is just too confusing! 

    // If $nosets has been specified, make sure $setsize is set to the right 
    // value, so that we can treat the two cases identically. Then work out how 
    // many extra elements will be left over. 
    if (!$setsize) {
        $setsize = floor($totalnoelements / $nosets);
        $noextra = $totalnoelements % $nosets;
    } else {
        $noextra = $totalnoelements % $setsize;
    }

    if (!$distribevenly) {
        // If we're putting our extra elements in a set at the end, everything 
        // is easy!
        if ($elementno % $setsize == 0) {
            $lastelement = true;
        }
    } else {
        // Work out the number of elements that will be in the bigger sets that 
        // have the leftover elements in 
        // them.
        $noinbiggersets = $noextra * ($setsize + 1);
        // Work out if this element is a last element in a set or not - we need 
        // to separate the case where the element is one of the ones that goes 
        // into the bigger sets at the beginning 
        // and the case where it's one of the elements in the normal sized sets. 
        if (($elementno <= $noinbiggersets and $elementno % ($setsize + 1) == 0) 
            or ($elementno > $noinbiggersets  and 
                ($elementno - $noinbiggersets ) % $setsize == 0) ) {
                   $lastelement = true;
        }
    }

    return $lastelement;
                                    }

?>
