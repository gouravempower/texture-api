<?php

class Api_model extends CI_Model
{

    public function get_role_list ()
    {
        $this->db->select ('*');
        $this->db->from ('role');
        $query = $this->db->get ();
        if ($query->num_rows () > 0) {
            return $result = $query->result ();
        } else {
            return FALSE;
        }
    }

}
