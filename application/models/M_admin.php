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


    //--- PRODUK ---//

    // //get produk
    // public function get_produk()
    // {
    //     $this->load->database();
    //     $data = $this->db->get('produk');
    //     return $data->result_array();
    // }

    // //insert produk
    // public function insert_produk($data)
    // {
    //     $this->load->database();
    //     $insert = $this->db->insert('produk', $data);
    // }

    // //update produk
    // public function update_produk($data, $id)
    // {
    //     $this->load->database();
    //     $this->where('id', $id);
    //     $this->update('produk', $data);

    //     $result = $this->db->get_where('produk', array('id' => $id));
    //     return $result->row_array();
    // }

    // //delete produk
    // public function delete_produk($id)
    // {
    //     $this->load->database();
    //     $result = $this->db->get_where('produk', array('id' => $id));

    //     $this->db->where('id', $id);
    //     $this->db->delete('produk');

    //     return $result->row_array();
    // }



    //--- transaksi ---//

    // //get transaksi
    // public function get_transaksi()
    // {
    //     $this->load->database();
    //     $data = $this->db->get('transaksi');
    //     return $data->result_array();
    // }

    // //insert transaksi
    // public function insert_transaksi($data)
    // {
    //     $this->load->database();
    //     $insert = $this->db->insert('transaksi', $data);
    // }

    // //update transaksi
    // public function update_transaksi($data, $id)
    // {
    //     $this->load->database();
    //     $this->where('id', $id);
    //     $this->update('transaksi', $data);

    //     $result = $this->db->get_where('transaksi', array('id' => $id));
    //     return $result->row_array();
    // }

    // //delete transaksi
    // public function delete_transaksi($id)
    // {
    //     $this->load->database();
    //     $result = $this->db->get_where('transaksi', array('id' => $id));

    //     $this->db->where('id', $id);
    //     $this->db->delete('transaksi');

    //     return $result->row_array();
    // }



    //--- ITEM TRANSAKSI ---//

    // //get item_transaksi
    // public function get_item_transaksi()
    // {
    //     $this->load->database();
    //     $data = $this->db->get('item_transaksi');
    //     return $data->result_array();
    // }

    // //insert item_transaksi
    // public function insert_item_transaksi($data)
    // {
    //     $this->load->database();
    //     $insert = $this->db->insert('item_transaksi', $data);
    // }

    // //update item_transaksi
    // public function update_item_transaksi($data, $id)
    // {
    //     $this->load->database();
    //     $this->where('id', $id);
    //     $this->update('item_transaksi', $data);

    //     $result = $this->db->get_where('item_transaksi', array('id' => $id));
    //     return $result->row_array();
    // }

    // //delete item_transaksi
    // public function delete_item_transaksi($id)
    // {
    //     $this->load->database();
    //     $result = $this->db->get_where('item_transaksi', array('id' => $id));

    //     $this->db->where('id', $id);
    //     $this->db->delete('item_transaksi');

    //     return $result->row_array();
    // }
}
