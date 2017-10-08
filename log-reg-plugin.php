<?php

/*
  Plugin Name: Login Registration Plugin
  Description: Login, Registration forms, updates user rating based on number of posts.
  Version: 1.0
  Author: Erdniev Kirsan	
 */

function registration_function() {
    if (isset($_POST['submit'])) {
        registration_validation(
        $_POST['username'],
        $_POST['password'],
        $_POST['email'],
        $_POST['website'],
        $_POST['fname'],
        $_POST['lname'],
        $_POST['nickname'],
        $_POST['bio']
		);
		
		// sanitize user form input
        global $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio;
        $username	= 	sanitize_user($_POST['username']);
        $password 	= 	esc_attr($_POST['password']);
        $email 		= 	sanitize_email($_POST['email']);
        $website 	= 	esc_url($_POST['website']);
        $first_name = 	sanitize_text_field($_POST['fname']);
        $last_name 	= 	sanitize_text_field($_POST['lname']);
        $nickname 	= 	sanitize_text_field($_POST['nickname']);
        $bio 		= 	esc_textarea($_POST['bio']);

		// call @function complete_registration to create the user
		// only when no WP_error is found
        complete_registration(
        $username,
        $password,
        $email,
        $website,
        $first_name,
        $last_name,
        $nickname,
        $bio
		);
    }

    registration_form(
    	$username,
        $password,
        $email,
        $website,
        $first_name,
        $last_name,
        $nickname,
        $bio
		);
}

function registration_form( $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio ) {
    echo '
    <style>
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
    text-align: center;
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
    box-shadow: 0 2px 8px rgba(166, 17, 17, 0.4);
    border-bottom: solid 2px #a61111;
    cursor: pointer;
    text-align: center;
    padding: 12px 0px 0px 20px;
    margin-top: 15px;
    width: 266px;
    line-height: 1.2;
    height: 42px;
    color: #fff;
    font-size: 16px;
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
	
	<div>
	<label for="email">Email <strong>*</strong></label><br>
	<input class="input1" type="text" name="email" value="' . (isset($_POST['email']) ? $email : null) . '">
	</div>
	
	<div>
	<label for="website">Сайт</label><br>
	<input class="input1" type="text" name="website" value="' . (isset($_POST['website']) ? $website : null) . '">
	</div>
	
	<div>
	<label for="firstname">Имя</label><br>
	<input class="input1" type="text" name="fname" value="' . (isset($_POST['fname']) ? $first_name : null) . '">
	</div>
	
	<div>
	<label for="website">Фамилия</label><br>
	<input class="input1" type="text" name="lname" value="' . (isset($_POST['lname']) ? $last_name : null) . '">
	</div>
	
	<div>
	<label for="nickname">Никнейм</label><br>
	<input class="input1" type="text" name="nickname" value="' . (isset($_POST['nickname']) ? $nickname : null) . '">
	</div>
	
	<div>
	<label for="bio">О себе</label><br>
	<textarea name="bio">' . (isset($_POST['bio']) ? $bio : null) . '</textarea>
	</div>
	<div class="button_register  bth_puls:hover"><input type="submit" name="submit" value="Регистрация"/></div>
	</form>
	';
}

function registration_validation( $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio )  {
    global $reg_errors;
    $reg_errors = new WP_Error;

    if ( empty( $username ) || empty( $password ) || empty( $email ) ) {
        $reg_errors->add('field', ' Обязательные поля не заполнены');
    }

    if ( strlen( $username ) < 4 ) {
        $reg_errors->add('username_length', ' Логин слишком короткий. Требуются как минимум 4 знака');
    }

    if ( username_exists( $username ) )
        $reg_errors->add('user_name', ' Извините, пользователь с таким логином уже существует');

    if ( !validate_username( $username ) ) {
        $reg_errors->add('username_invalid', ' Извините, введенный вами логин неккоректен');
    }

    if ( strlen( $password ) < 5 ) {
        $reg_errors->add('password', ' Пароль должен быть длиннее 5 знаков');
    }

    if ( !is_email( $email ) ) {
        $reg_errors->add('email_invalid', ' Email неккоректен');
    }

    if ( email_exists( $email ) ) {
        $reg_errors->add('email', ' Email уже используется');
    }
    
    if ( !empty( $website ) ) {
        if ( !filter_var($website, FILTER_VALIDATE_URL) ) {
            $reg_errors->add('website', 'Ссылка на сайт недействительна');
        }
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

function complete_registration() {
    global $reg_errors, $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio;
    if ( count($reg_errors->get_error_messages()) < 1 ) {
        $userdata = array(
        'user_login'	=> 	$username,
        'user_email' 	=> 	$email,
        'user_pass' 	=> 	$password,
        'user_url' 		=> 	$website,
        'first_name' 	=> 	$first_name,
        'last_name' 	=> 	$last_name,
        'nickname' 		=> 	$nickname,
        'description' 	=> 	$bio,
		);
        $user = wp_insert_user( $userdata );
		// Отправка письма
		$mail_subject = "Сообщение об успешной регистрации на сайте WordPress";
		$mail_message = "Уважаемый " . $username . ", добро пожаловать на сайт WordPress.\n\nПожалуйста сохраните это сообщение. Параметры вашей учётной записи таковы:\n\nВаш логин: " . $username . "\nВаш пароль: " . $password . "\n\nС уважением, команда сайта WordPress.";
		
		wp_mail( $email, $mail_subject, $mail_message );
		
		//авторизация после успешной регистрации
		$creds = array();
		$creds['user_login'] = $username;
		$creds['user_password'] = $password;
		$creds['remember'] = true;

		$user = wp_signon( $creds, false );

		if ( is_wp_error($user) ) {
		echo $user->get_error_message();
		}
		
		if ( is_wp_error($user) ) {
		echo $user->get_error_message();
		}
		// Сообщение о завершении регистрации с ссылкой на логин страницу
        //echo 'Регистрация прошла успешно. Можете войти на странице <a href="' . get_site_url() . '/wp-login.php">авторизации</a>.';
		echo 'Регистрация прошла успешно. Вернитесь на главную страницу для <a href="' . get_site_url() . 'авторизации</a>.';	
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
//  Регистрация шорткода: [log_reg_plugin]
add_shortcode('log_reg_plugin', 'log_reg_plugin_shortcode');

// The callback function that will replace [book]
function log_reg_plugin_shortcode() {
    ob_start();
    registration_function();
    return ob_get_clean();
}
