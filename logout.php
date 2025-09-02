<?php
// logout.php (project root)
session_start();
session_unset();
session_destroy();
header("Location: /blood-donation-system/index.php");
exit;
