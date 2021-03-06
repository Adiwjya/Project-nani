<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Customer
 *
 * @author Adiw.io
 */
class Customer extends CI_Controller{
    
    public function __construct() {
        parent::__construct();
        $this->load->library('Modul');
        $this->load->model('Mglobals');
    }
    
    public function index() {
        if($this->session->userdata('logged_in')){
            $session_data = $this->session->userdata('logged_in');
            $data['email'] = $session_data['email'];
            $data['akses'] = $session_data['akses'];
            $data['nama'] = $session_data['nama'];
            $data['kota'] = $this->Mglobals->getAll("kota");
            $data['wilayah'] = $this->Mglobals->getAll("wilayah");
            
            
            $this->load->view('head', $data);
            $this->load->view('menu');
            $this->load->view('customer/index');
            $this->load->view('footer');
        }else{
           $this->modul->halaman('login');
        }
    }
    
    public function ajax_list() {
        if($this->session->userdata('logged_in')){
            $data = array();
            $list = $this->Mglobals->getAll("customer");
            foreach ($list->result() as $row) {
                $val = array();
                $val[] = $row->nama;
                $val[] = $row->alamat;
                $val[] = $this->Mglobals->getAllQR("SELECT nama FROM kota where kode_kota = '".$row->kode_kota."';")->nama;
                $val[] = $this->Mglobals->getAllQR("SELECT nama FROM wilayah where kode_wilayah = '".$row->kode_wilayah."';")->nama;
                $val[] = $row->no_tlp;
                $val[] = $row->no_fax;
                // $val[] = $this->Mglobals->getAllQR("SELECT nama_kategori FROM kategori where idkategori = '".$row->idkategori."';")->nama_kategori;
                $val[] = '<div style="text-align: center;">'
                .'<a class="btn btn-sm  btn-success" href="javascript:void(0)" title="Hapus" onclick="history('."'".$row->kode_customer."'".','."'".$row->nama."'".')"><i class="ft-delete"></i> History</a>&nbsp;'
                        . '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="ganti('."'".$row->kode_customer."'".')"><i class="ft-edit"></i> Edit</a>&nbsp;'
                        . '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="hapus('."'".$row->kode_customer."'".','."'".$row->nama."'".')"><i class="ft-delete"></i> Delete</a>'
                        . '</div>';
                
                $data[] = $val;
            }
            $output = array("data" => $data);
            echo json_encode($output);
        }else{
            $this->modul->halaman('login');
        }
    }

    public function ajax_add() {
        if($this->session->userdata('logged_in')){
            $cek = $this->Mglobals->getAllQR("select count(*) as jml from customer where nama = '".$this->input->post('nama_customer')."';")->jml;
            if($cek > 0){
                $status = "Data sudah ada";
            }else{
                $data = array(
                    'kode_customer' => $this->modul->autokode1('C','kode_customer','customer','2','7'),
                    'nama' => $this->input->post('nama_customer'),
                    'alamat' => $this->input->post('alamat'),
                    'kode_kota' => $this->input->post('kota'),
                    'kode_wilayah' => $this->input->post('kode_wilayah'),
                    'no_tlp' => $this->input->post('no_tlp'),
                    'no_fax' => $this->input->post('no_fax')
                );
                $simpan = $this->Mglobals->add("customer",$data);
                if($simpan == 1){
                    $status = "Data tersimpan";
                }else{
                    $status = "Data gagal tersimpan";
                }
            }
            echo json_encode(array("status" => $status));
        }else{
            $this->modul->halaman('login');
        }
    }
    
    public function ganti(){
        if($this->session->userdata('logged_in')){
            $kondisi['kode_customer'] = $this->uri->segment(3);
            $data = $this->Mglobals->get_by_id("customer", $kondisi);
            echo json_encode($data);
        }else{
            $this->modul->halaman('login');
        }
    }
    
    public function ajax_edit() {
        if($this->session->userdata('logged_in')){
            $data = array(
                'nama' => $this->input->post('nama_customer'),
                    'alamat' => $this->input->post('alamat'),
                    'kode_kota' => $this->input->post('kode_kota'),
                    'kode_wilayah' => $this->input->post('kode_wilayah'),
                    'no_tlp' => $this->input->post('no_tlp'),
                    'no_fax' => $this->input->post('no_fax')
            );
            $condition['kode_customer'] = $this->input->post('id');
            $update = $this->Mglobals->update("customer",$data, $condition);
            if($update == 1){
                $status = "Data terupdate";
            }else{
                $status = "Data gagal terupdate";
            }
            echo json_encode(array("status" => $status));
        }else{
            $this->modul->halaman('login');
        }
    }
    
    public function hapus() {
        if($this->session->userdata('logged_in')){
            $kondisi['kode_customer'] = $this->uri->segment(3);
            $hapus = $this->Mglobals->delete("customer",$kondisi);
            if($hapus == 1){
                $status = "Data terhapus";
            }else{
                $status = "Data gagal terhapus";
            }
            echo json_encode(array("status" => $status));
        }else{
            $this->modul->halaman('login');
        }
    }
    public function hapus_history() {
        if($this->session->userdata('logged_in')){
            $kondisi['idl'] = $this->uri->segment(3);
            $hapus = $this->Mglobals->delete("lunas",$kondisi);
            if($hapus == 1){
                $status = "Data terhapus";
            }else{
                $status = "Data gagal terhapus";
            }
            echo json_encode(array("status" => $status));
        }else{
            $this->modul->halaman('login');
        }
    }
    
    public function history() {
        if($this->session->userdata('logged_in')){
            $idnya = $this->uri->segment(3);
            $session_data = $this->session->userdata('logged_in');
            $data['email'] = $session_data['email'];
            $data['akses'] = $session_data['akses'];
            $data['nama'] = $session_data['nama'];
            $data['kota'] = $this->Mglobals->getAll("kota");
            $data['wilayah'] = $this->Mglobals->getAll("wilayah");
            $data['history'] = $this->Mglobals->getAllQ("select * from lunas where customer = '".$idnya."';");
            
            
            $this->load->view('head', $data);
            $this->load->view('menu');
            $this->load->view('customer/history');
            $this->load->view('footer');

        }else{
            $this->modul->halaman('login');
        }
    }


    public function history_detail() {
        if($this->session->userdata('logged_in')){
            $session_data = $this->session->userdata('logged_in');
            $data['email'] = $session_data['email'];
            $data['akses'] = $session_data['akses'];
            $data['nama'] = $session_data['nama'];   
            $data['kotaq'] = $this->Mglobals->getAll("kota");
            $data['wilayahq'] = $this->Mglobals->getAll("wilayah");
            $data['customerq'] = $this->Mglobals->getAll("customer");
            $data['salesq'] = $this->Mglobals->getAll("sales");

            $data['satts'] = 1;

                        

            $kode_dekrip = $this->uri->segment(3);
                
                    $data['kode'] = $kode_dekrip;
                    $data['kode_lunas'] = $this->Mglobals->getAllQR("SELECT idl FROM lunas where idl = '".$kode_dekrip."';")->idl;
                    $data['tanggal'] = $this->Mglobals->getAllQR("SELECT tanggal FROM lunas where idl = '".$kode_dekrip."';")->tanggal;
                    $data['sales'] = $this->Mglobals->getAllQR("SELECT a.nama_sales FROM sales a join lunas b where a.kode_sales=b.sales and b.idl = '".$kode_dekrip."';")->nama_sales;
                    $data['kota'] = $this->Mglobals->getAllQR("SELECT a.nama FROM kota a join lunas b where a.kode_kota=b.kota and b.idl = '".$kode_dekrip."';")->nama;
                    $data['wilayah'] = $this->Mglobals->getAllQR("SELECT a.nama FROM wilayah a join lunas b where a.kode_wilayah=b.wilayah and b.idl = '".$kode_dekrip."';")->nama;
                    $data['alamat'] = $this->Mglobals->getAllQR("SELECT alamat FROM lunas where idl = '".$kode_dekrip."';")->alamat;
                    $data['customer'] = $this->Mglobals->getAllQR("SELECT a.nama FROM customer a join lunas b where a.kode_customer=b.customer and b.idl = '".$kode_dekrip."';")->nama;
                    $data['subtotal'] = $this->Mglobals->getAllQR("SELECT subtotal FROM lunas where idl = '".$kode_dekrip."';")->subtotal;
                    $data['diskon'] = $this->Mglobals->getAllQR("SELECT diskon FROM lunas where idl = '".$kode_dekrip."';")->diskon;
                    $data['ppn'] = $this->Mglobals->getAllQR("SELECT ppn FROM lunas where idl = '".$kode_dekrip."';")->ppn;
                    $data['total_akhir'] = $this->Mglobals->getAllQR("SELECT total_akhir FROM lunas where idl = '".$kode_dekrip."';")->total_akhir;
                    $data['kembalian'] = $this->Mglobals->getAllQR("SELECT kembalian FROM lunas where idl = '".$kode_dekrip."';")->kembalian;
                    $data['jumlah_bayar'] = $this->Mglobals->getAllQR("SELECT jumlah_bayar FROM lunas where idl = '".$kode_dekrip."';")->jumlah_bayar;
                    $data['stat'] = $this->Mglobals->getAllQR("SELECT status as stat FROM lunas where idl = '".$kode_dekrip."';")->stat;

            
            $this->load->view('head', $data);
            $this->load->view('menu');
            $this->load->view('customer/history_detail');
            $this->load->view('footer');
        }else{
            $this->modul->halaman('login');
        }
    }

    public function ajax_list_detail_pembayaran() {
        if($this->session->userdata('logged_in')){
            $kode = $this->uri->segment(3);
            $data = array();
            $list = $this->Mglobals->getAllQ("SELECT * FROM lunas_detail where idl = '".$kode."';");
            foreach ($list->result() as $row) {
                $val = array();
                // data barang
                $barang = $this->Mglobals->getAllQR("select nama, merk, satuan from barang where idbarang = '".$row->kode_barang."';");
                $val[] = $barang->nama;
                $val[] = $barang->merk;
                $val[] = $barang->satuan;
                $val[] = $row->harga;
                $val[] = $row->jumlah;
                $val[] = '<div style="text-align: center;">'
                        . '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="ganti('."'".$row->idl_detail."'".')"><i class="ft-edit"></i> Edit</a>&nbsp;'
                        . '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="hapus('."'".$row->idl_detail."'".', '."'".$barang->nama."'".')"><i class="ft-delete"></i> Delete</a>'
                        . '</div>';
                
                $data[] = $val;
            }
            $output = array("data" => $data);
            echo json_encode($output);
        }else{
            $this->modul->halaman('login');
        }
    }
}
