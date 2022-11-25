<?php

use LDAP\Result;

defined('BASEPATH') or exit('No direct script access allowed');

class M_admin extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //---ADMINNNNN COCO---//

    //get admin
    public function getData()
    {
        $this->load->database();
        $data = $this->db->get('admin');
        return $data->result_array();
    }

    //post admin
    function insertData($data)
    {
        $this->load->database();
        $insert = $this->db->insert('admin', $data);

        // $this->db->insert('admin', $data);
    }

    //put admin
    public function updateAdmin($data, $id)
    {
        $this->load->database();
        $this->db->where('id', $id);
        $this->db->update('admin', $data);

        $result = $this->db->get_where('admin', array('id' => $id));

        return $result->row_array();
    }

    //delete admin
    public function deleteAdmin($id)
    {
        $this->load->database();
        $result = $this->db->get_where('admin', array('id' => $id));

        $this->db->where('id', $id);
        $this->db->delete('admin');

        return $result->row_array();
    }

    //get admin login
    public function cekLoginAdmin($data)
    {
        $this->db->where($data);
        $result = $this->db->get('admin');

        return $result->row_array();
    }

    //get admin by id
    public function cekAdminExist($id)
    {
        $data = array(
            "id" => $id
        );

        $this->db->where($data);
        $result = $this->db->get('admin');

        if (empty($result->row_array())) {
            return false;
        }

        return true;
    }
}