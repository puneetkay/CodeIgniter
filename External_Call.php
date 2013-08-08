<?php

/*
 * External Call - 08/08/2013
 * https://github.com/puneetkay/CodeIgniter
 *
 * @author: Puneet Kalra
 * http://www.puneetk.com
 */

/****************************
* INSTRUCTIONS
*****************************
* This tweak is divided into 2 parts. 
* 1 - Making calls from outside CI
* 2 - Recognizing those calls.
****************************/

/****************************
* TESTING
*****************************
* This code has been tested on CI v2.1.4 only. 
****************************/


/****************************
* PART 1 - Recognizing external calls.
* To do that, We can use constant. We can define constant on external script
* And on CI, We will check it to recognize external calls. eg: EXT_CALL
*
* Now open up CI_INSTALLATION/system/core/CodeIgniter.php file and look
* for 'Call the requested method' part. In this part, look for code
* call_user_func_array(array(&$CI, $method), array_slice($URI->rsegments, 2));
* It should be around line number 357-360 and replace it with :
****************************/

if(!defined('EXT_CALL')){ // Checking if EXT_CALL constant is defined
    call_user_func_array(array(&$CI, $method), array_slice($URI->rsegments, 2));
} // Thats it for PART 1


/****************************
* PART 2 - Making calls from outside CI
* Follow the code and read comments.
****************************/

define('EXT_CALL', true); // Added EXT_CALL constant to mark external calls
$_GET['controller/method'] = ''; // add pair to $_GET with call route as key

$current = getcwd(); // Save current directory path
chdir('PATH/TO/CI_INSTALLATION'); // change directory to CI_INSTALLATION

require_once 'index.php'; // Add index.php (CI) to script
    
$CI =& get_instance(); // Get instance of CI

// NOTE: IF DYNAMIC CALLING!!
call_user_func_array(array(&$CI, 'method'), array('controller','method')); // replace controller and method with call route
// OR
// FOR STATIC CALLING!!
$CI->method('controller','method'); // replace controller and method with call route
// eg: $CI->list('welcome','list'); If calling welcome/list route.

$OUT->_display(); // to display output. (quick one)

// Or if you need output in variable,
$output = $CI->load->view('VIEW_NAME',array(),TRUE); // To call any specific view file (bit slow)
// You can pass variables in array. View file will pick those as it works in CI

chdir($current); // Change back to current directory

/****************************
* ENDS HERE
****************************/

?>