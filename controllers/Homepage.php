<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Homepage extends Veripay_Controller
{
    function __construct()
    {
        parent:: __construct();

        $this->load->model($this->router->fetch_class() . '_model', 'model');

    }

    public function index(){
        $this->load->view('index');
    }
    public function register(){
        $data=new stdClass();
        $this->form_validation->set_rules('firstname','firstname','required|xss_clean');
        $this->form_validation->set_rules('password','password','required|xss_clean');
        $this->form_validation->set_rules('quest','quest','required|xss_clean');
        if ($this->form_validation->run() !=FALSE){
            $post=new stdClass();
            $post->firstname=$this->input->post('firstname',true);
            $password=$this->input->post('password',true);
            $post->password = password_hash($password,PASSWORD_DEFAULT);
            $post->quest=$this->input->post('quest',true);
            $this->session->set_userdata('user',$post);
            $this->model->register($post);
            $data->succes="BAŞARIYLA KAYDOLDUN";

        }
        $data->csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );

        $this->load->view('register',$data);
    }
    public function login(){
        if (!empty($this->session->userdata('users'))){
            redirect(site_url());
        }
        $data=new stdClass();
        $this->form_validation->set_rules('firstname','firstname','required|xss_clean');
        $this->form_validation->set_rules('password','password','required|xss_clean');
        if ($this->form_validation->run() != FALSE){
            $post=new stdClass();
            $post->firstname=$this->input->post('firstname',true);
            $post->password=$this->input->post('password',true);

            if (!empty($person=$this->model->login($post))) {

                if (password_verify($post->password,$person->password)){
                    $this->session->set_userdata('users',$person);
                    redirect(site_url('Homepage/loginsucces'));
                }
                else{
                    $data->error="KULLANCII ADI VEYA ŞİFRE HATALI";
                }
            }
            else{
                $data->error="HATALI İSİM SİFRE";
            }
        }
        $data->csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        $this->load->view('login',$data);
    }
    public function loginsucces(){
        $data=new stdClass();
        $this->form_validation->set_rules('content','content','required|xss_clean');
        $this->form_validation->set_rules('id','id','required|xss_clean');
        $post=new stdClass();
        if ($this->form_validation->run() != FALSE){

            $post->content=$this->input->post('content',true);
            $post->userid=$this->input->post('id',true);
            $this->model->tweet($post);
        }
        $data->user=$this->session->userdata('users');
        $data->twitler=$this->model->tweetlist();
        $data->yorumlar=$this->model->yorumlist();

        foreach ($data->twitler as $key => $val) {
            foreach ($data->yorumlar as $_key => $_val) {
                if($_val->yorumyapilanid == $val->id) {
                    $val->comments[$_key]=$_val->yorum;
                }
            }
        }
      //  prex($data->twitler);



        $data->csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );



        $this->load->view('loginsucces',$data);
    }
    public function add_image()
    {
        $uploaded_images = [];
        $config['upload_path'] = 'assets/uploads/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|';
        $config['encrypt_name'] = TRUE;
        $this->load->library('Upload', $config);
        if ($this->upload->do_upload('file')) {
            $image_session = $this->session->userdata('images');
            if ($image_session == false) {
                $uploaded_images = [];
            } else {
                $uploaded_images = $image_session;
            }
            $uploaded_images[] = 'assets/uploads/' . $this->upload->data('file_name');
            $this->session->set_userdata('images', $uploaded_images);
            pre($this->session->userdata('images'));
        } else {
            $this->output->set_status_header('404');
            print strip_tags($this->upload->display_errors());
            exit;
        }
    }
    public function ppupload(){
        $data=new stdClass();
        $this->form_validation->set_rules('id','id','required|xss_clean');
        if ($this->form_validation->run() !=FALSE) {
            $post = new stdClass();
            $post->id=$this->input->post('id',true);
            if ($this->session->userdata('images')) {
                $post->img_pet = $this->session->userdata('images')[0];
                $this->session->unset_userdata('images');
            }
            $this->model->ppup($post);
        }
        $data->csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        $data->user=$this->session->userdata('users');
        $this->load->view('ppupload',$data);
    }
    public function profil(){
        $data=new stdClass();
        $data->user=$this->session->userdata('users');
        $this->form_validation->set_rules('content','content','required|xss_clean');
        $this->form_validation->set_rules('id','id','required|xss_clean');
        if ($this->form_validation->run() != FALSE){
            $post=new stdClass();
            $post->content=$this->input->post('content',true);
            $post->userid=$this->input->post('id',true);
            $this->model->tweet($post);
        }
        $data->profil=$this->model->profillist($data->user->id);

        $data->csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        $this->load->view('profil',$data);
    }
    public function kapat(){
        $this->session->unset_userdata('users');
        redirect(site_url('index'));
    }
    public function takiplesme($id=''){
        $data=new stdClass();
        $data->user=$this->session->userdata('users');
        $post=new stdClass();
        $post->takipedilenid=$id;
        $post->takipid=$data->user->id;
        $this->model->takipinsert($post);
        redirect(site_url('twitter'));
    }
    public function profildetail($id=''){
        $data=new stdClass();
        $data->user=$this->session->userdata('users');
        $data->prof=$this->model->profildetlist($id);

        $this->load->view('profildetails',$data);

    }
     public function me($id=''){
      $data=new stdClass();
      $data->user=$this->session->userdata('users');
      $data->liste=$this->model->melist();
      $array=[];
      foreach ($data->liste as $row){
          array_push($array,$row->takipid);
      }
      $data->list=$this->model->cnlist($id);
//prex($data->list);
       $this->load->view('me',$data);
     }
     public function like($id=''){
        $data=new stdClass();
        $data->user=$this->session->userdata('users');
        $post=new stdClass();
        $post->begenilenid=$id;
        $ic=$data->user->id;
        $post->begenenid=$ic;
        $data->begen=$this->model->like($post);

        redirect($this->agent->referrer());
     }
     public function yorumekle(){
        $data=new stdClass();
        $data->user=$this->session->userdata('users');
        $this->form_validation->set_rules('yorum','yorum','required|xss_clean');
        $this->form_validation->set_rules('yorumyapanid','yorumyapanid','required|xss_clean');
        $this->form_validation->set_rules('yorumyapilanid','yorumyapilanid','required|xss_clean');
        if ($this->form_validation->run() !=FALSE){
            $post=new stdClass();
            $post->yorum=$this->input->post('yorum',true);
            $post->yorumyapanid=$this->input->post('yorumyapanid',true);
            $post->yorumyapilanid=$this->input->post('yorumyapilanid',true);
            $this->model->yorumekle($post);
        }
        $data->csrf = array(
             'name' => $this->security->get_csrf_token_name(),
             'hash' => $this->security->get_csrf_hash()
         );
         redirect($this->agent->referrer());
     }

public function crl()
{
   $crl=curl_init();
   curl_setopt($crl,CURLOPT_URL,"http://www.google.com");
   $result=curl_exec($crl);
   curl_close($crl);

}

















}