<?php
session_start();
echo "sign success <br>";
echo $_SESSION['REG_DATE'];
echo '<br>';
echo $_SESSION['USER_NAME'];
echo '<br>';

echo $_SESSION['FIRST_NAME'];
echo '<br>';

echo $_SESSION['LAST_NAME'];
echo '<br>';

echo $_SESSION['ADDRESS'];
echo '<br>';

echo $_SESSION['EMAIL'];
echo '<br>';

echo $_SESSION['PHONE'];


?>