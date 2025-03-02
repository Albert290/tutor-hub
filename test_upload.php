<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$result = handle_file_upload('test_file', 'test_uploads/');
echo "<pre>";
print_r($result);
echo "</pre>";