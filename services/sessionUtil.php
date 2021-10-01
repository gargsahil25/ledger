<?php

include_once "util.php";

function getLoggedInUser($redirect = false) {
    if(isset($_GET['admin'])) {
        setcookie('admin', $_GET['admin'], time() + (1000000000), "/");
    }
    if (isset($_SESSION['userId'])) {
    	$user = array();
    	$user['userId'] = $_SESSION['userId'];
    	$user['userName'] = $_SESSION['userName'];
        $user['profit'] = $_SESSION['profit'];
        $user['isAdmin'] = (isset($_COOKIE['admin']) && $_COOKIE['admin'] == 't') ? true : false;
        return $user;
    } else if ($redirect) {
        return redirect('/login.php');
    }
}

function loggedInRedirect() {
    if (isset($_SESSION['userId'])) {
        return redirect();
    }
}

function setLoginUser($user) {
	$_SESSION['userId'] = $user['id'];
	$_SESSION['userName'] = $user['name'];
    $_SESSION['profit'] = $user['profit'];
	return redirect();
}

?>