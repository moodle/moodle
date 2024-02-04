<?php
if (isset($_POST['evidence_name_type'])) {
    $filename = $_POST['filename'];
    $evidence_name_type = $_POST['evidence_name_type']; // Ensure it's an integer

    switch ($evidence_name_type) {
        case 'no_face':
            $evidence_name_type = 7;
            break;
        case 'multiple_face':
            $evidence_name_type = 8;
            break;
        case 'suspicious_movement':
            $evidence_name_type = 9;
            break;
        // default:
    }
    
    echo "evdtype: " . $evidence_name_type;
    echo "</br>";
    echo "filename: " . $filename;
}
?>