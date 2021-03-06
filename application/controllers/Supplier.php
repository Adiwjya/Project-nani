<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Supplier
 *
 * @author Adiw.io
 */
class Supplier extends CI_Controller{
    
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
            $this->load->view('supplier/index');
            $this->load->view('footer');
        }else{
           $this->modul->halaman('login');
        }
    }
    
    public function ajax_list() {
        if($this->session->userdata('logged_in')){
            $data = array();
            $list = $this->Mglobals->getAll("supplier1");
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
                        . '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="ganti('."'".$row->kode_supplier."'".')"><i class="ft-edit"></i> Edit</a>&nbsp;'
                        . '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="hapus('."'".$row->kode_supplier."'".','."'".$row->nama."'".')"><i class="ft-delete"></i> Delete</a>'
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
            $cek = $this->Mglobals->getAllQR("select count(*) as jml from supplier1 where nama = '".$this->input->post('nama_supplier')."';")->jml;
            if($cek > 0){
                $status = "Data sudah ada";
            }else{
                $data = array(
                    'kode_supplier' => $this->modul->autokode1('S','kode_supplier','supplier1','2','7'),
                    'nama' => $this->input->post('nama_supplier'),
                    'alamat' => $this->input->post('alamat'),
                    'kode_kota' => $this->input->post('kode_kota'),
                    'kode_wilayah' => $this->input->post('kode_wilayah'),
                    'no_tlp' => $this->input->post('no_tlp'),
                    'no_fax' => $this->input->post('no_fax')
                );
                $simpan = $this->Mglobals->add("supplier1",$data);
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
            $kondisi['kode_supplier'] = $this->uri->segment(3);
            $data = $this->Mglobals->get_by_id("supplier1", $kondisi);
            echo json_encode($data);
        }else{
            $this->modul->halaman('login');
        }
    }
    
    public function ajax_edit() {
        if($this->session->userdata('logged_in')){
            $data = array(
                'nama' => $this->input->post('nama_supplier'),
                    'alamat' => $this->input->post('alamat'),
                    'kode_kota' => $this->input->post('kode_kota'),
                    'kode_wilayah' => $this->input->post('kode_wilayah'),
                    'no_tlp' => $this->input->post('no_tlp'),
                    'no_fax' => $this->input->post('no_fax')
            );
            $condition['kode_supplier'] = $this->input->post('id');
            $update = $this->Mglobals->update("supplier1",$data, $condition);
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
            $kondisi['kode_supplier'] = $this->uri->segment(3);
            $hapus = $this->Mglobals->delete("supplier1",$kondisi);
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
}
