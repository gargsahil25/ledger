<?php

include_once "util.php";

function getLoggedInUser($redirect = false) {
    if (isset($_SESSION['userId'])) {
    	$user = array();
    	$user['userId'] = $_SESSION['userId'];
    	$user['userName'] = $_SESSION['userName'];
        $user['isAdmin'] = (isset($_GET['mode']) && $_GET['mode'] == 'admin') ? true : false;
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
	return redirect();
}

?>