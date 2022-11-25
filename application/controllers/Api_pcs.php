<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/Firebase/JWT/JWT.php';

use \Firebase\JWT\JWT;
use LDAP\Result;

class Api_pcs extends REST_Controller
{

    private $secret_key = "anakquda";

    function __construct()
    {
        parent::__construct();
        $this->load->model('M_admin');
        $this->load->model('M_produk');
        $this->load->model('M_transaksi');
        $this->load->model('M_item_transaksi');
    }



    //--- LOGIN ---//
    public function login_post()
    {
        $data = array(
            "email" => $this->post("email"),
            "password" => md5($this->post("password"))
        );

        $result = $this->M_admin->cekLoginAdmin($data);

        if (empty($result)) {
            $data_json = array(
                "success" => false,
                "message" => "Email dan Password tidak valid",
                "error_code" => 1308,
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        } else {
            $date = new Datetime();

            $payload["id"] = $result["id"];
            $payload["email"] = $result["email"];
            $payload["iat"] = $date->getTimestamp(); //waktu token dikeluarkan kapan
            $payload["exp"] = $date->getTimestamp() + 3600; //expired 1 jam(3600 s)

            $data_json = array(
                "success" => true,
                "message" => "Otentikasi Berhasil",
                "data" => array(
                    "admin" => $result,
                    "token" => JWT::encode($payload, $this->secret_key)
                )
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
        }
    }

    public function cekToken()
    {
        try {
            $token = $this->input->get_request_header('Authorization');

            if (!empty($token)) {
                $token = explode(' ', $token)[1];
            }

            $token_decode = JWT::decode($token, $this->secret_key, array('HS256'));
        } catch (Exception $e) {
            $data_json = array(
                "success" => false,
                "message" => "Token tidak valid",
                "error_code" => 1204,
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }
    }



    //--- ADMIN ---//

    public function admin_get()
    {
        $this->cekToken();
        $data = $this->M_admin->getData();

        $result = array(
            "success" => true,
            "message" => "Data ditemukan",
            "data" => $data
        );

        echo json_encode($result);
    }

    public function admin_post()
    {
        // $this->cekToken();
        $validation_message = [];

        if ($this->post("email") == "") {
            array_push($validation_message, "email tidak boleh kosong");
        }
        if ($this->post("password") == "") {
            array_push($validation_message, "password tidak boleh kosong");
        }
        // if ($this->post("nama") == "") {
        //     array_push($validation_message, "nama tidak boleh kosong");
        // }

        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data tidak valid",
                "data" => $validation_message
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        //if accross validation
        $data = array(
            'email' => $this->post('email'),
            'password' => md5($this->post('password')),
            'nama' => $this->post('nama')
        );

        $insert = $this->M_admin->insertData($data);

        if ($insert) {
            $this->response($data, 200);
        } else {
            $this->response($data, 502);
        }
    }

    public function admin_put()
    {
        $this->cekToken();

        // validasi
        $validation_message = [];

        if ($this->put("id") == "") {
            array_push($validation_message, "id tidak boleh kosong");
        }
        if ($this->put("email") == "") {
            array_push($validation_message, "Email tidak boleh kosong");
        }

        if ($this->put("email") != "" && !filter_var($this->put("email"), FILTER_VALIDATE_EMAIL)) {
            array_push($validation_message, "Format Email tidak valid");
        }

        if ($this->put("password") == "") {
            array_push($validation_message, "Password tidak boleh kosong");
        }

        if ($this->put("nama") == "") {
            array_push($validation_message, "Nama tidak boleh kosong");
        }

        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data tidak valid",
                "data" => $validation_message
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        //jika  lolos validasi
        $data = array(
            "email" => $this->put("email"),
            "password" => md5($this->put("password")),
            "nama" => $this->put("nama")
        );

        $id = $this->put("id");
        $result = $this->M_admin->updateAdmin($data, $id);

        $data_json = array(
            "success" => true,
            "message" => "Update berhasil",
            "data" => array(
                "admin" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function admin_delete()
    {
        $this->cekToken();
        $id = $this->delete("id");
        $result = $this->M_admin->deleteAdmin($id);

        if (empty($result)) {
            $data_json = array(
                "success" => false,
                "message" => "ID tidak valid",
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        $data_json = array(
            "success" => true,
            "message" => "Delete berhasil",
            "data" => array(
                "admin" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }



    //--- PRODUK ---//

    public function produk_get()
    {
        $this->cekToken();

        $data = $this->M_produk->get_produk();
        $result = array(
            "success" => true,
            "message" => "Data Correct",
            "data" => $data
        );
        echo json_encode($result);
    }

    public function produk_post()
    {
        $this->cekToken();
        $validation_message = [];

        if ($this->post("admin_id") == "") {
            array_push($validation_message, "id admin tidak boleh kosong");
        }
        if ($this->post("admin_id") != "" && !$this->M_admin->cekAdminExist($this->post("admin_id"))) {
            array_push($validation_message, "id admin not found");
        }
        if ($this->post("nama") == "") {
            array_push($validation_message, "nama tidak boleh kosong");
        }
        if ($this->post("harga") == "") {
            array_push($validation_message, "harga tidak boleh kosong");
        }
        if ($this->post("harga") != "" && !is_numeric($this->post("harga"))) {
            array_push($validation_message, "harga harus diisi angka");
        }
        if ($this->post("stok") == "") {
            array_push($validation_message, "stok tidak boleh kosong");
        }
        if ($this->post("stok") != "" && !is_numeric($this->post("stok"))) {
            array_push($validation_message, "stok harus diisi angka");
        }

        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data Not Valid",
                "data" => $validation_message
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        //if accross validation
        $data = array(
            'admin_id' => $this->post('admin_id'),
            'nama' => $this->post('nama'),
            'harga' => $this->post('harga'),
            'stok' => $this->post('stok')
        );

        $insert = $this->M_produk->insert_produk($data);

        if ($insert) {
            $this->response($data, 200);
        } else {
            $this->response($data, 502);
        }
    }

    public function produk_put()
    {
        $this->cekToken();
        $validation_message = [];

        if ($this->put("id") == "") {
            array_push($validation_message, "id tidak boleh kosong");
        }
        if ($this->put("admin_id") == "") {
            array_push($validation_message, "id admin tidak boleh kosong");
        }
        if ($this->put("admin_id") != "" && !$this->M_admin->cekAdminExist($this->put("admin_id"))) {
            array_push($validation_message, "id admin not found");
        }
        if ($this->put("nama") == "") {
            array_push($validation_message, "nama tidak boleh kosong");
        }
        if ($this->put("harga") == "") {
            array_push($validation_message, "harga tidak boleh kosong");
        }
        if ($this->put("stok") == "") {
            array_push($validation_message, "stok tidak boleh kosong");
        }

        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data Not Valid",
                "data" => $validation_message
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        //if across validation
        $data = array(
            "admin_id" => $this->put("admin_id"),
            "nama" => $this->put("nama"),
            "harga" => $this->put("harga"),
            "stok" => $this->put("stok")
        );

        $id = $this->put("id");

        $result = $this->M_produk->update_produk($data, $id);

        $data_json = array(
            "success" => true,
            "message" => "Update berhasil",
            "data" => array(
                "produk" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function produk_delete()
    {
        $this->cekToken();
        $id = $this->delete("id");
        $result = $this->M_produk->delete_produk($id);

        if (empty($result)) {
            $data_json = array(
                "success" => false,
                "message" => "ID tidak valid",
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        $data_json = array(
            "success" => true,
            "message" => "Delete berhasil",
            "data" => array(
                "produk" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }



    //--- TRANSAKSI ---//

    public function transaksi_get()
    {
        $this->cekToken();

        $data = $this->M_transaksi->get_transaksi();
        $result = array(
            "success" => true,
            "message" => "Data Correct",
            "data" => $data
        );
        echo json_encode($result);
    }

    public function transaksi_bulan_ini_get()
    {
        $this->cekToken();

        $data = $this->M_transaksi->get_transaksiBulanIni();
        $result = array(
            "success" => true,
            "message" => "Data Correct",
            "data" => $data
        );
        echo json_encode($result);
    }

    public function transaksi_post()
    {
        $this->cekToken();
        $validation_message = [];

        if ($this->post("admin_id") == "") {
            array_push($validation_message, "admin_id tidak boleh kosong");
        }
        if ($this->post("admin_id") != "" && !$this->M_admin->cekAdminExist($this->post("admin_id"))) {
            array_push($validation_message, "id admin not found");
        }
        if ($this->post("total") == "") {
            array_push($validation_message, "total tidak boleh kosong");
        }
        if ($this->post("total") != "" && !is_numeric($this->post("total"))) {
            array_push($validation_message, "total harus diisi angka");
        }

        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data Not Valid",
                "data" => $validation_message
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        //if accross validation
        $data = array(
            'admin_id' => $this->post('admin_id'),
            'tanggal' => date("Y-m-d H:i:s"),
            'total' => $this->post('total')
        );

        $insert = $this->M_transaksi->insert_transaksi($data);

        if ($insert) {
            $this->response($data, 200);
        } else {
            $this->response($data, 502);
        }
    }

    public function transaksi_put()
    {
        $this->cekToken();
        $validation_message = [];

        if ($this->put("id") == "") {
            array_push($validation_message, "id tidak boleh kosong");
        }
        if ($this->put("admin_id") == "") {
            array_push($validation_message, "id admin tidak boleh kosong");
        }
        if ($this->put("admin_id") != "" && !$this->M_admin->cekAdminExist($this->put("admin_id"))) {
            array_push($validation_message, "id admin not found");
        }
        if ($this->put("total") == "") {
            array_push($validation_message, "total tidak boleh kosong");
        }

        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data Not Valid",
                "data" => $validation_message
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        //if across validation
        $data = array(
            "admin_id" => $this->put("admin_id"),
            "tanggal" => date("Y-m-d H:i:s"),
            "total" => $this->put("total")
        );

        $id = $this->put("id");
        $result = $this->M_transaksi->update_transaksi($data, $id);

        $data_json = array(
            "success" => true,
            "message" => "Update berhasil",
            "data" => array(
                "transaksi" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function transaksi_delete()
    {
        $this->cekToken();
        $id = $this->delete("id");
        $result = $this->M_transaksi->delete_transaksi($id);

        if (empty($result)) {
            $data_json = array(
                "success" => false,
                "message" => "ID tidak valid",
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        $data_json = array(
            "success" => true,
            "message" => "Delete berhasil",
            "data" => array(
                "transaksi" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }





    //--- ITEM TRANSAKSI ---//

    public function item_transaksi_get()
    {
        $this->cekToken();

        $data = $this->M_item_transaksi->get_item_transaksi();
        $result = array(
            "success" => true,
            "message" => "Data Correct",
            "data" => $data
        );
        echo json_encode($result);
    }

    public function item_transaksi_by_transaksi_id_get()
    {
        $this->cekToken();

        $data = $this->M_item_transaksi->get_item_transaksi_by_transaksi_id($this->get('transaksi_id'));
        $result = array(
            "success" => true,
            "message" => "Data Correct",
            "data" => $data
        );
        echo json_encode($result);
    }

    public function item_transaksi_post()
    {
        $this->cekToken();
        $validation_message = [];

        if ($this->post("transaksi_id") == "") {
            array_push($validation_message, "transaksi_id tidak boleh kosong");
        }
        if ($this->post("transaksi_id") != "" && !$this->M_transaksi->cekTransaksiExist($this->post("transaksi_id"))) {
            array_push($validation_message, "id transaksi not found");
        }
        if ($this->post("produk_id") == "") {
            array_push($validation_message, "produk_id tidak boleh kosong");
        }
        if ($this->post("produk_id") != "" && !$this->M_produk->cekProdukExist($this->post("produk_id"))) {
            array_push($validation_message, "id produk not found");
        }
        if ($this->post("qty") == "") {
            array_push($validation_message, "qty tidak boleh kosong");
        }
        if ($this->post("qty") != "" && !is_numeric($this->post("qty"))) {
            array_push($validation_message, "qty harus diisi angka");
        }
        if ($this->post("harga_saat_transaksi") == "") {
            array_push($validation_message, "harga_saat_transaksi tidak boleh kosong");
        }
        if ($this->post("harga_saat_transaksi") != "" && !is_numeric($this->post("harga_saat_transaksi"))) {
            array_push($validation_message, "harga_saat_transaksi harus diisi angka");
        }

        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data Not Valid",
                "data" => $validation_message
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        //if accross validation
        $data = array(
            'transaksi_id' => $this->post('transaksi_id'),
            'produk_id' => $this->post('produk_id'),
            'qty' => $this->post('qty'),
            'harga_saat_transaksi' => $this->post('harga_saat_transaksi'),
            'sub_total' => $this->post('harga_saat_transaksi') * $this->post('qty')
        );

        $insert = $this->M_item_transaksi->insert_item_transaksi($data);

        if ($insert) {
            $this->response($data, 200);
        } else {
            $this->response($data, 502);
        }
    }

    public function item_transaksi_put()
    {
        $this->cekToken();
        $validation_message = [];

        if ($this->put("id") == "") {
            array_push($validation_message, "id tidak boleh kosong");
        }
        if ($this->put("transaksi_id") == "") {
            array_push($validation_message, "id transaksi tidak boleh kosong");
        }
        if ($this->put("transaksi_id") != "" && !$this->M_transaksi->cekTransaksiExist($this->put("transaksi_id"))) {
            array_push($validation_message, "id transaksi not found");
        }
        if ($this->put("produk_id") == "") {
            array_push($validation_message, "id produk tidak boleh kosong");
        }
        if ($this->put("produk_id") != "" && !$this->M_produk->cekProdukExist($this->put("produk_id"))) {
            array_push($validation_message, "id produk not found");
        }
        if ($this->put("qty") == "") {
            array_push($validation_message, "Qty tidak boleh kosong");
        }
        if ($this->put("qty") != "" && !is_numeric($this->put("qty"))) {
            array_push($validation_message, "qty harus diisi angka");
        }
        if ($this->put("harga_saat_transaksi") == "") {
            array_push($validation_message, "harga tidak boleh kosong");
        }
        if ($this->put("harga_saat_transaksi") != "" && !is_numeric($this->put("harga_saat_transaksi"))) {
            array_push($validation_message, "harga_saat_transaksi harus diisi angka");
        }

        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data Not Valid",
                "data" => $validation_message
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        //if across validation
        $data = array(
            "transaksi_id" => $this->put("transaksi_id"),
            "produk_id" => $this->put("produk_id"),
            "qty" => $this->put("qty"),
            "harga_saat_transaksi" => $this->put("harga_saat_transaksi"),
            "sub_total" => $this->put("harga_saat_transaksi") * $this->put("qty")
        );

        $id = $this->put("id");
        $result = $this->M_item_transaksi->update_item_transaksi($data, $id);

        $data_json = array(
            "success" => true,
            "message" => "Update berhasil",
            "data" => array(
                "item_transaksi" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function item_transaksi_delete()
    {
        $this->cekToken();
        $id = $this->delete("id");
        $result = $this->M_item_transaksi->delete_item_transaksi($id);

        if (empty($result)) {
            $data_json = array(
                "success" => false,
                "message" => "ID tidak valid",
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        $data_json = array(
            "success" => true,
            "message" => "Delete berhasil",
            "data" => array(
                "item_transaksi" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function item_transaksi_by_transaksi_id_delete()
    {
        $this->cekToken();
        $transaksi_id = $this->delete("transaksi_id");
        $result = $this->M_item_transaksi->delete_item_transaksi_by_transaksi_id($transaksi_id);

        if (empty($result)) {
            $data_json = array(
                "success" => false,
                "message" => "Transaksi ID tidak valid",
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        $data_json = array(
            "success" => true,
            "message" => "Delete berhasil",
            "data" => array(
                "item_transaksi" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }
}
