<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class User extends RestController
{

    public function __construct()
    {
        // code...
        parent::__construct();
    }

    public function login_get($username, $password)
    {
        $users = $this->MCore->get_data('pengguna', 'username = "' . $username . '" AND password ="' . $password . '"')->result();

        // Check if the users data store contains users
        if ($users) {
            // Set the response and exit
            // Set the response and exit
            $this->response([
                'status' => true,
                'message' => 'Berhasil',
                'data'  => $users
            ], 200);
            
        } else {
            // Set the response and exit
            $this->response([
                'status' => false,
                'message' => 'No users were found'
            ], 404);
        }
    }
}
