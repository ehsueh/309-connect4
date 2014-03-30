<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config = array(
		'account/createNew' => array(
				array(
						'field' => 'username',
						'label' => 'Username',
						'rules' => 'required|min_length[5]|max_length[12]|is_unique[user.login]'
				),
				array(
						'field' => 'password',
						'label' => 'Password',
						'rules' => 'required|min_length[4]'
				),
				array(
						'field' => 'passconf',
						'label' => 'Password Confirmation',
						'rules' => 'required|min_length[4]|matches[password]'
				),
				array(
						'field' => 'first',
						'label' => 'First',
						'rules' => 'required|max_length[24]'
				),
				array(
						'field' => 'last',
						'label' => 'Last',
						'rules' => 'required|max_length[24]'
				),
				array(
						'field' => 'email',
						'label' => 'Email',
						'rules' => 'required|valid_email|max_length[45]|is_unique[user.email]'
				)
		)
);


