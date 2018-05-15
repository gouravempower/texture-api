<?php

class User_model extends CI_Model
{

    public function add_invited_user ($data)
    {
        $unique_id = $data['u'];
        $full_name = $data['full_name'];
        $display_name = $data['display_name'];
        $password = $data['password'];

        if ($unique_id == '') {
            return false;
        } else {
            $this->db->select ('invited_email,company_id');
            $this->db->from ('user_invitation');
            $this->db->where ('unique_id', $unique_id);
            $this->db->where ('accepted_on', '');
            $query = $this->db->get ();

            if ($query->num_rows () === 1) {

                $result = $query->row_array ();
                $email = $result['invited_email'];
                $company = $result['company_id'];

                $this->db->select ('company_name');
                $this->db->from ('company');
                $this->db->where ('id', $company);
                $comapny_query = $this->db->get ();
                $company_name = '';
                if ($comapny_query->num_rows () === 1) {
                    $comapny_result = $comapny_query->row_array ();
                    $company_name = $comapny_result['company_name'];
                }

                $this->db->trans_begin ();

                $user_data = array('full_name' => $full_name, 'display_name' => $display_name, 'email' => $email, 'password' => $password);
                $this->db->insert ('user', $user_data);
                $user_id = $this->db->insert_id ();

                $is_admin = 0;

                $user_workplace_data = array('user_id' => $user_id, 'company_id' => $company, 'is_admin' => $is_admin);
                $this->db->insert ('company_user_association', $user_workplace_data);

                $data = array('accepted_on' => date ('Y-m-d H:i:s'));
                $this->db->where ('unique_id', $unique_id);
                $this->db->update ('user_invitation', $data);

                $update_data = array('user_id' => $user_id, 'company_id' => $company, 'is_admin' => $is_admin);
                $this->db->insert ('company_user_association', $update_data);

                $this->db->trans_complete ();
//        company_user_association
                if ($this->db->trans_status () === FALSE) {
                    $this->db->trans_rollback ();
                    return $return_array = ['status' => FALSE, 'description' => 'error_in_query'];
                } else {
                    $this->db->trans_commit ();
                    return $return_array = ['status' => TRUE, 'description' => 'successfully_done', 'user_id' => $user_id, 'company_id' => $company, 'company_name' => $company_name];
                }
            } else {
                return $return_array = ['status' => FALSE, 'description' => 'already_accepted'];
            }
        }
    }

    public function add_user ($data)
    {
        $full_name = $data['full_name'];
        $display_name = $data['display_name'];
        $email = $data['email'];
        $password = $data['password'];
        $company_name = $data['company_name'];
//        $company_usage = $data['company_usage'];
        $company_length = $data['company_length'];
        $company_kind = $data['company_kind'];
        $role_id = $data['role_id'];
        $is_admin = $data['is_admin'];
        $is_manager = $data['is_manager'];
        $invited_email = $data['invited_email'];
        $invited_landing = $data['invited_landing'];

        $this->db->trans_begin ();

        $user_data = array('full_name' => $full_name, 'display_name' => $display_name, 'email' => $email, 'password' => $password);

        $this->db->insert ('user', $user_data);

        $user_id = $this->db->insert_id ();
//        $company_data = array('company_name' => $company_name, 'company_usage' => $company_usage, 'company_length' => $company_length, 'company_kind' => $company_kind, 'created_by' => $user_id);
        $company_data = array('company_name' => $company_name, 'company_length' => $company_length, 'company_kind' => $company_kind, 'created_by' => $user_id);

        $this->db->insert ('company', $company_data);

        $company_id = $this->db->insert_id ();

        $is_admin = 1;
        $user_workplace_data = array('user_id' => $user_id, 'company_id' => $company_id, 'role_id' => $role_id, 'is_admin' => $is_admin, 'is_manager' => $is_manager);

        $this->db->insert ('company_user_association', $user_workplace_data);
        $this->db->trans_complete ();
//        company_user_association
        if ($this->db->trans_status () === FALSE) {
            $this->db->trans_rollback ();
            return $return_array = ['status' => FALSE, 'description' => 'error_in_query'];
        } else {
            $this->db->trans_commit ();

            $this->db->trans_begin ();
            foreach ($invited_email as $e):
                $rand_number = rand ();
                $l = md5 ($user_id . "," . $email . "," . $company_id . "," . $company_name . "," . $rand_number);
                $link = $invited_landing . '?company=' . $company_name . '&u=' . $l;
                //$new_password = rand(34546, 89757);
                $this->email->from ('tkmdjvo@gmail.com', "Texture");
                $this->email->to ($e);
//$this->email->cc($this->input->post('student_email'));
                $this->email->subject ('Invited To Join');
//$this->flexi_auth->forgotten_password($identity);
                $this->email->message ("You are invited to join the group. Your link is: $link ");
                if ($this->email->send ()) {
                    $user_data = array('invited_email' => $e, 'company_id' => $company_id, 'invited_by' => $user_id, 'unique_id' => $l);
                    $this->db->insert ('user_invitation', $user_data);
                    $this->db->insert_id ();
                }
            endforeach;
            $this->db->trans_complete ();
            if ($this->db->trans_status () === FALSE) {
                $this->db->trans_rollback ();
                return $return_array = ['status' => FALSE, 'description' => 'error_in_query'];
            } else {
                $this->db->trans_commit ();
                return $return_array = ['status' => TRUE, 'description' => 'successfully_done', 'user_id' => $user_id, 'company_id' => $company_id, 'company_name' => $company_name, 'sender_email' => $email];
            }
        }
    }

    public function check_company_name ($data)
    {
        $company_name = $data['company_name'];

        if ($company_name == '') {
            return $return_array = ['status' => FALSE, 'description' => 'blank_value'];
        } else {
            $this->db->select ('company_name');
            $this->db->from ('company');
            $this->db->where ('company_name', $company_name);
            $query = $this->db->get ();
            if ($query->num_rows () > 0) {
                return $return_array = ['status' => FALSE, 'description' => 'name_exist'];
            } else {
                return $return_array = ['status' => FALSE, 'description' => 'name_not_exist'];
            }
        }
    }

    public function do_login ($data)
    {
        $email = $data['email'];
        $password = $data['password'];
        $company_name = $data['company_name'];
        $this->db->select ('user.id as user_id,'
                . 'user.full_name,user.display_name,'
                . 'user.email,company_user_association.is_manager,'
                . 'user.email,company_user_association.is_admin,'
                . 'company_user_association.company_id,'
                . 'company.company_name');
        $this->db->from ('user');
        $this->db->join ('company_user_association', 'company_user_association.user_id =user.id', 'INNER');
        $this->db->join ('company', 'company.id =company_user_association.company_id', 'INNER');
        $this->db->where ('email', $email);
        $this->db->where ('password', $password);
        $this->db->where ('company.company_name', $company_name);
        $query = $this->db->get ();
        /*
          SELECT
         *
          FROM
          texture.user
          INNER JOIN
          company_user_association ON company_user_association.user_id = user.id
          INNER JOIN
          company ON company.id = company_user_association.company_id
          WHERE
          user.email = 'empower@test.com'
          AND password = 'e14f7e1b8b46831e6c49a477db50743c'
          AND company.company_name = 'empower';
         *          */
// Let's check if there are any results
        if ($query->num_rows () === 1) {
            $result_set = $query->row_array ();
            return $return_array = ['status' => TRUE, 'description' => 'successfully_done', 'data' => $result_set];
        } else {
            return $return_array = ['status' => FALSE, 'description' => 'user_not_exist', 'data' => $result_set];
        }
    }

}
