<?php

use LDAP\Result;

defined('BASEPATH') or exit('No direct script access allowed');

class M_item_transaksi extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    //--- item_transaksi ---//

    //get item_transaksi
    public function get_item_transaksi()
    {
        // $data = $this->db->get('item_transaksi');
        // return $data->result_array();

        $this->db->select('item_transaksi.id, item_transaksi.transaksi_id, item_transaksi.produk_id, 
        produk.nama, item_transaksi.qty, item_transaksi.harga_saat_transaksi, item_transaksi.sub_total');
        $this->db->from('item_transaksi');
        $this->db->join('produk', 'produk.id = item_transaksi.produk_id');
        $query = $this->db->get();
        return $query->result_array();
    }

    //get item_transaksi by transaksi_id
    public function get_item_transaksi_by_transaksi_id($transaksi_id)
    {
        $this->db->select('item_transaksi.id, item_transaksi.transaksi_id, item_transaksi.produk_id, 
        produk.nama, item_transaksi.qty, item_transaksi.harga_saat_transaksi, item_transaksi.sub_total');
        $this->db->from('item_transaksi');
        $this->db->join('produk', 'produk.id = item_transaksi.produk_id');
        $this->db->where('item_transaksi.transaksi_id', $transaksi_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    //insert item_transaksi
    public function insert_item_transaksi($data)
    {
        // $insert = $this->db->insert('item_transaksi', $data);

        $this->db->insert('item_transaksi', $data);
        $insert_id = $this->db->insert_id();
        $result = $this->db->get_where('item_transaksi', array('id' => $insert_id));

        //code untuk mengubah stok produk
        $result_produk = $this->db->get_where('produk', array('id' => $data["produk_id"]));
        $result_produk = $result_produk->row_array();
        $stok_lama = $result_produk["stok"];
        $stok_baru = $stok_lama - $data["qty"];

        $data_produk_update = array(
            "stok" => $stok_baru
        );

        $this->db->where('id', $data["produk_id"]);
        $this->db->update('produk', $data_produk_update);



        return $result->row_array();
    }

    //update item_transaksi
    public function update_item_transaksi($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('item_transaksi', $data);

        $result = $this->db->get_where('item_transaksi', array('id' => $id));
        return $result->row_array();
    }

    //delete item_transaksi
    public function delete_item_transaksi($id)
    {
        $result = $this->db->get_where('item_transaksi', array('id' => $id));

        $this->db->where('id', $id);
        $this->db->delete('item_transaksi');

        return $result->row_array();
    }

    //delete item_transaksi by transaksi_id
    public function delete_item_transaksi_by_transaksi_id($transaksi_id)
    {
        $result = $this->db->get_where('item_transaksi', array('transaksi_id' => $transaksi_id));

        $this->db->where('transaksi_id', $transaksi_id);
        $this->db->delete('item_transaksi');

        return $result->result_array();
    }
}
