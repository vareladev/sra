<?php

function nuevo_registro(){
	include 'sql-open.php';
	
	include "sql-close.php";
}

/* Initialize mysqli database link */
$link = mysqli_connect('localhost', 'test', 'test', 'test');

/* Check connection */
if (!$link) {
die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
}

/* Switch off auto commit to allow transactions*/
mysqli_autocommit($link, FALSE);
$query_success = TRUE;

/* Select user_id */
$username = 'demo';
$sql = 'SELECT user_id FROM users WHERE name = ?';
$stmt = mysqli_prepare($link, $sql);     
mysqli_stmt_bind_param($stmt, 's', $username);
if (!mysqli_stmt_execute($stmt)) {
$query_success = FALSE;
}
mysqli_stmt_bind_result($stmt, $user_id);
if (!mysqli_stmt_fetch($stmt)) {
$query_success = FALSE;
}
mysqli_stmt_close($stmt);

/* Insert into sessions */
$sql = 'INSERT INTO sessions (user_id, created) VALUES (?, NOW())';
$stmt = mysqli_prepare($link, $sql);     
mysqli_stmt_bind_param($stmt, 'i', $user_id);
if (!mysqli_stmt_execute($stmt)) {
$query_success = FALSE;
}
mysqli_stmt_close($stmt);

/* Insert into sessions_roles */
$role_id = uniqid();
$sql = 'INSERT INTO sessions_roles (role_id, session_id) VALUES (?, LAST_INSERT_ID())';
$stmt = mysqli_prepare($link, $sql);     
mysqli_stmt_bind_param($stmt, 's', $role_id);
if (!mysqli_stmt_execute($stmt)) {
$query_success = FALSE;
}
mysqli_stmt_close($stmt);

/* Commit or rollback transaction */
if ($query_success) {
echo 'Success';
mysqli_commit($link);
} else {
echo 'Error';
mysqli_rollback($link);
}   

?>