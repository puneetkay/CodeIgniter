<?php

/*
 * show_404() function - 04/08/2013
 * https://github.com/puneetkay/CodeIgniter
 *
 * @author: Puneet Kalra
 * http://www.puneetk.com
 */


/****************************
* Code starts from here
****************************/

// Override show_404 method
function show_404(){
    // Load Router and Core classes.
    $RTR =& load_class('Router', 'core');

    // Get class and method for 404 from routes.php
    $x = explode('/', $RTR->routes['404_override']);
    $class = $x[0];
    $method = (isset($x[1]) ? $x[1] : 'index');

    // Get current class and method for callback
    $callback_class = $RTR->fetch_class();
    $callback_method = $RTR->fetch_method();
    // Can also log here, using callback class and method.

    // Create object for callback
    $CI = new $callback_class;
    call_user_func_array(array(&$CI, $method), array_slice(array($class,$method), 2));
} // End!

/****************************
* Ends here
****************************/

?>