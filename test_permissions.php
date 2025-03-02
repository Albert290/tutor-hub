<?php
$file = 'test.txt';
if (file_put_contents($file, 'test')) {
    echo "Write permissions OK!";
    unlink($file); // Cleanup
} else {
    echo "Permission denied for: " . getcwd();
}
?>