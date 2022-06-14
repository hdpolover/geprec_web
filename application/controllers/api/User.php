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

    public function index_get()
    {
        // code...
        $id = $this->get('referral_code');
        if ($id === NULL) {
            // code...
            $influencer = $this->influencer->get_influencer();
        } else {
            // code...
            $influencer = $this->influencer->get_influencer($id);
        }

        if ($influencer) {
            // code...
            $this->response([
                'status' => true,
                'data' => $influencer
            ],  RestController::HTTP_OK);
        } else {
            // code...
            $this->response([
                'status' => false,
                'message' => 'id not found'
            ],  RestController::HTTP_NOT_FOUND);
        }
    }
}