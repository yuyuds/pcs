<?php

use LDAP\Result;

defined('BASEPATH') or exit('No direct script access allowed');

class M_produk extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    //--- PRODUK ---//

    //get produk
    public function get_produk()
    {
        // $data = $this->db->get('produk');
        $this->db->select('produk.id, admin.nama as nama admin, produk.nama as nama menu, produk.harga, produk.stok');
        $this->db->from('produk');
        $this->db->join('admin', 'admin.id = produk.admin_id');
        $query = $this->db->get();
        return $query->result_array();
    }

    //insert produk
    public function insert_produk($data)
    {
        $insert = $this->db->insert('produk', $data);
    }

    //update produk
    public function update_produk($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('produk', $data);

        $result = $this->db->get_where('produk', array('id' => $id));
        return $result->row_array();
    }

    //delete produk
    public function delete_produk($id)
    {
        $result = $this->db->get_where('produk', array('id' => $id));

        $this->db->where('id', $id);
        $this->db->delete('produk');

        return $result->row_array();
    }

    //get produk by id
    public function cekProdukExist($id)
    {
        $data = array(
            "id" => $id
        );

        $this->db->where($data);
        $result = $this->db->get('produk');

        if (empty($result->row_array())) {
            return false;
        }

        return true;
    }
}
