<?php

require(APPPATH . '/libraries/REST_Controller.php');

class User extends REST_Controller
{

    function __construct ()
    {
        parent::__construct ();
        $this->load->helper ('url');
        $this->load->model ('User_model');
        $this->load->library ('email');

        $this->email->initialize (array(
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.sendgrid.net',
            'smtp_user' => 'app90312644@heroku.com',
            'smtp_pass' => 'mxyaheup5467',
            'smtp_port' => 587,
            'crlf' => "\r\n",
            'newline' => "\r\n"
        ));
    }

    /**
     * @method check_client
     * @brief This method is used to check clinic is exist.
     */
    function check_company_post ()
    {

        if ($this->input->get ()) {
            $data = $this->input->get ();
        } else {
            $data = $this->input->post ();
        }
        $result = $this->User_model->check_company_name ($data);
        if ($result['status']) {
            $this->set_response (['status' => FALSE, 'data' => $result], REST_Controller::HTTP_NO_CONTENT);
        } else {
            $this->set_response (['status' => TRUE, 'data' => $result], REST_Controller::HTTP_OK);
        }
    }

    /**
     * @method login_user
     * @brief This method is used for login functionality
     */
    function login_user_post ()
    {

        if ($this->input->get ()) {
            $data = $this->input->get ();
        } else {
            $data = $this->input->post ();
        }
        $data = $this->User_model->do_login ($data);
        if ($data['status'] === TRUE) {
            $this->set_response ($data, REST_Controller::HTTP_OK);
        } elseif ($data['status'] === FALSE) {
            $this->set_response (['status' => FALSE, 'data' => $data], REST_Controller::HTTP_NO_CONTENT);
        }
    }

    /**
     * @method add_invite_post
     * @brief This method is used to add invited user.
     * @param $id
     */
    function add_invite_post ($id = 0)
    {
        if ($this->input->get ()) {
            $data = $this->input->get ();
        } else {
            $data = $this->input->post ();
        }
        $result_data = $this->User_model->add_invited_user ($data);

        if ($result_data['status']) {
            $this->set_response (['status' => TRUE, 'data' => $result_data], REST_Controller::HTTP_OK);
        } else {
            $this->set_response (['status' => FALSE, 'data' => $result_data], REST_Controller::HTTP_OK);
        }
    }

    /**
     * @method add_post
     * @brief This method is used to add user.
     * @param $id
     */
    function add_post ($id = 0)
    {
        if ($this->input->get ()) {
            $data = $this->input->get ();
        } else {
            $data = $this->input->post ();
        }
        $result_data = $this->User_model->add_user ($data);

        if ($result_data['status']) {
            $this->set_response (['status' => TRUE, 'data' => $result_data], REST_Controller::HTTP_OK);
        } else {
            $this->set_response (['status' => FALSE, 'data' => $result_data], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @method invite_post
     * @brief This method is used to send invitation to member.
     * @param $id
     */
    function invite_post ($id = 0)
    {
        if ($this->input->get ()) {
            $data = $this->input->get ();
        } else {
            $data = $this->input->post ();
        }
        $message = $this->User_model->add_invitations ($data);
        echo "<pre>";
        var_dump ($message);
        die;
        if ($message) {
            $message = 'success';
            $this->set_response ($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = 'failed';
            $this->set_response ($message, REST_Controller::HTTP_FAILED_DEPENDENCY);
        }
    }

}
