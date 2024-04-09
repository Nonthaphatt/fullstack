<?php
// Start session
    if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 

    if (isset($_SESSION['status']) && isset($_SESSION['member'])) {
        $status = $_SESSION['status'];
        unset($_SESSION['cart']);
        unset($_SESSION['guest']);
    } else if(isset($_SESSION['admin'])){
        unset($_SESSION['cart']);
        unset($_SESSION['guest']);
    }
    else {
        $_SESSION['guest'] = 'guest';
    }
    
    // var_dump($_SESSION);
?>