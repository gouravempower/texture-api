<?php

require(APPPATH . '/libraries/REST_Controller.php');

class Role extends REST_Controller
{

    function __construct ()
    {
        parent::__construct ();
        $this->load->model ('Role_model');
    }

    /**
     * @method list
     * @brief This method is used for get list of all roles.
     * @param $id_param
     */
    public function list_get ()
    {
        $input = $this->input->get ();
        $data = $this->Role_model->get_list ($input);
        if ($data) {
            $this->set_response ($data, REST_Controller::HTTP_OK);
        } else {
            $this->set_response ([
                'status' => FALSE,
                'error' => 'Record could not be found'
                    ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

}
