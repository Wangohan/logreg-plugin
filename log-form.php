<?php



function login_function() {
    if (isset($_POST['submit'])) {
        login_validation(
        $_POST['username'],
        $_POST['password'],
		);
		
		// sanitize user form input
        global $username, $password;
        $username	= 	sanitize_user($_POST['username']);
        $password 	= 	esc_attr($_POST['password']);

		// call @function complete_registration to create the user
		// only when no WP_error is found
        complete_login(
        $username,
        $password,
		);
    }

    login_form(
    	$username,
        $password,
		);
}

function login_form( $username, $password) {
    echo '
    <style>
	@font-face {
    font-family: PTSans-Regular; 
    src: url(../fonts/PTSans-Regular.ttf);
	}
	div {
		margin-bottom:2px;
	}
	label{
		width: 200px;
	}
	.input1 {
    width: 400px;
    height: 40px;
    font-size: 18px;
	padding-left: 10px;
    text-align: left;
    margin: 15px;
    border: solid 2px rgb(240, 240, 240);
    background-color: rgb(255, 255, 255);
    box-shadow: inset 0px 3px 9px 0px rgba(187, 187, 187, 0.7);
	}
	textarea{
    width: 400px;
    height: 51px;
    font-size: 18px;
    text-align: center;
    margin: 15px;
    border: solid 2px rgb(240, 240, 240);
    background-color: rgb(255, 255, 255);
    box-shadow: inset 0px 3px 9px 0px rgba(187, 187, 187, 0.7);
	}
	.button_register {
    border-radius: 21px;
    background-image: -moz-linear-gradient( 90deg, rgb(198,43,43) 0%, rgb(247,90,90) 100%);
    background-image: -webkit-linear-gradient( 90deg, rgb(198,43,43) 0%, rgb(247,90,90) 100%);
    background-image: -ms-linear-gradient( 90deg, rgb(198,43,43) 0%, rgb(247,90,90) 100%);
	border: solid 2px rgb(170,43,43);
    box-shadow: 0 2px 8px rgba(166, 17, 17, 0.4);
    border-bottom: solid 2px #a61111;
    cursor: pointer;
    text-align: center;
    padding: 12px 0px 10px 12px;
    margin-top: 15px;
    width: 266px;
    line-height: 1.2;
    height: 42px;
    color: #fff;
    font-size: 16px;
	font-family: PTSans-Regular;
	}
	.button_register:hover {
    background-image: -moz-linear-gradient( 90deg, rgb(193, 36, 36) 0%, rgb(255, 0, 0) 100%);
    background-image: -webkit-linear-gradient( 90deg, rgb(193, 36, 36) 0%, rgb(255, 0, 0) 100%);
    background-image: -ms-linear-gradient( 90deg, rgb(193, 36, 36) 0%, rgb(255, 0, 0) 100%);
	}
	</style>
	';

    echo '
    <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
	<div>
	<label for="username">Логин <strong>*</strong></label><br>
	<input class="input1" type="text" name="username" value="' . (isset($_POST['username']) ? $username : null) . '">
	</div>
	
	<div>
	<label for="password">Пароль <strong>*</strong></label><br>
	<input class="input1" type="password" name="password" value="' . (isset($_POST['password']) ? $password : null) . '">
	</div>
	<button class="button_register  bth_puls:hover" onclick="submit" type="submit" name="submit" value="Логин">ЛОГИН</button>
	</form>
	';
}

function login_validation( $username, $password)  {
    global $reg_errors;
    $reg_errors = new WP_Error;

    if ( empty( $username ) || empty( $password ) || empty( $email ) ) {
        $reg_errors->add('field', ' Обязательные поля не заполнены');
    }


    if ( is_wp_error( $reg_errors ) ) {

        foreach ( $reg_errors->get_error_messages() as $error ) {
            echo '<div>';
            echo '<strong>Ошибка</strong>:';
            echo $error . '<br/>';

            echo '</div>';
        }
    }
}

function complete_login() {
    global $reg_errors, $username, $password;
    if ( count($reg_errors->get_error_messages()) < 1 ) {
        $userdata = array(
        'user_login'	=> 	$username
        'user_pass' 	=> 	$password,
        );
		
		//авторизация после успешной регистрации
		$creds = array();
		$creds['user_login'] = $username;
		$creds['user_password'] = $password;
		$creds['remember'] = true;

		$user = wp_signon( $creds, false );

		
		if ( is_wp_error($user) ) {
		echo $user->get_error_message();
		}
		
		wp_redirect(get_site_url());
	}
}

/* Попытка логина
	function wp_signon( $credentials = array(), $secure_cookie = '' ) {
	if ( empty($credentials) ) {
		$credentials = array(); // Back-compat for plugins passing an empty string.

		if ( ! empty($_POST['username']) )
			$credentials['user_login'] = $_POST['username'];
		if ( ! empty($_POST['password']) )
			$credentials['user_password'] = $_POST['password'];
		if ( ! empty($_POST['rememberme']) )
			$credentials['remember'] = $_POST['rememberme'];
	}

	if ( !empty($credentials['remember']) )
		$credentials['remember'] = true;
	else
		$credentials['remember'] = false;
	do_action( 'wp_login', $user->user_login, $user );
	return $user;
}
*/
//  Регистрация шорткода: [log_form]
add_shortcode('log_form', 'log_form_plugin_shortcode');

// The callback function that will replace [book]
function log_form_plugin_shortcode() {
    ob_start();
    login_function();
    return ob_get_clean();
}
