<?php

require(APPPATH . '/libraries/REST_Controller.php');

class Api extends REST_Controller
{

    function __construct ()
    {
        parent::__construct ();
        $this->load->model ('Api_model');
    }

    /**
     * @method list_role
     * @brief This method is used for get list of all roles.
     * @param $id_param
     */
    public function list_role_get ()
    {
        $input = $this->input->get ();
        $data = $this->Api_model->get_role_list ($input);
        if ($data) {
            $this->set_response ($data, REST_Controller::HTTP_OK);
        } else {
            $this->set_response ([
                'status' => FALSE,
                'error' => 'Record could not be found'
                    ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    /**
     * @method list_usage
     * @brief This method is used for get list of all roles.
     * @param $id_param
     */
    public function list_usage_get ()
    {
        $input = $this->input->get ();
        $data = $this->Api_model->get_role_list ($input);
        if ($data) {
            $this->set_response ($data, REST_Controller::HTTP_OK);
        } else {
            $this->set_response ([
                'status' => FALSE,
                'error' => 'Record could not be found'
                    ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

//    /**
//     * @method list_get
//     * @brief This method is used for get list of all calls.
//     * @param $id_param
//     */
//    public function role_list_get ($id_param = NULL)
//    {
//        $input = $this->input->get ();
//        $data = $this->Api_model->get_role_list ($input);
//        if ($data) {
//            $this->set_response ($data, REST_Controller::HTTP_OK);
//        } else {
//            $this->set_response ([
//                'status' => FALSE,
//                'error' => 'Record could not be found'
//                    ], REST_Controller::HTTP_NOT_FOUND);
//        }
//    }
}

?>