<?php
session_start();
session_destroy();
$vars = array_keys(get_defined_vars());
for ($i = 0; $i < sizeOf($vars); $i++) {
    unset($$vars[$i]);
}
unset($vars,$i);
header('Location: index.php');
exit;
?>