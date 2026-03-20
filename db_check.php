<?php
$c = mysqli_connect('localhost', 'root', '', 'playroom');
$r = mysqli_query($c, "SELECT role FROM users WHERE email='admin@playroom.test'");
$f = mysqli_fetch_assoc($r);
echo "DATABASE_ROLE:" . $f['role'] . "\n";
