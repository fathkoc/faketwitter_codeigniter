<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Homepage_model extends CI_Model {

    public function register($post){
        $this->db->set($post)->insert('register');
        if($this->db->affected_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    public function login($post){
        $this->db->from('register');
        $this->db->WHERE(['firstname'=>$post->firstname]);
        $return_query = $this->db->get();
        if($return_query->num_rows() > 0) {
            return $return_query->row();
        } else {
            return false;
        }
    }
    public function tweet($post){
        $this->db->set($post)->insert('twitler');
        if($this->db->affected_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    /*
$this->db->where((array)['begenilenid'=>$array]);
return $this->db->count_all_results('begen');
*/
    public function tweetlist(){
        //$this->db->select('t.content,t.id,r.img_pet,r.firstname,b.begenilenid,Count(b.id) as like_count');
        $this->db->select('t.content,t.id,r.img_pet,r.firstname,(Select Count(id) From begen Where t.id = begenilenid) as like_count');
        $this->db->from('twitler t');
        $this->db->join('register r','t.userid = r.id ');
        //$this->db->join('begen b','t.id=b.begenilenid','left');

        $this->db->group_by('t.id');
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){

            return $return_query->result();


        }
        else{
            return false;
        }

    }
    public function ppup($post){
        $this->db->set($post)->WHERE(['id'=>$post->id])->update('register');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    public function profillist($id){
        $this->db->select('t.content,t.id as t_id,r.*');
        $this->db->from('twitler t');
        $this->db->join('register r','t.userid = r.id ');
        $this->db->where(['r.id'=>$id]);
        $this->db->group_by('t.id');
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->result();
        }
        else{
            return false;
        }
    }
    public function takipinsert($post){
        $this->db->set($post)->insert('takip');
        if($this->db->affected_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    public function profildetlist($id){
        $this->db->select('t.content,t.id as t_id,r.*');
        $this->db->from('twitler t');
        $this->db->join('register r','t.userid = r.id ');
        $this->db->where(['r.id'=>$id]);
        $this->db->group_by('t.id');
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->result();
        }
        else{
            return false;
        }
    }

    public function melist(){
        $this->db->select('t.userid,ta.takipid');
        $this->db->from('twitler t');
        $this->db->join('takip ta','t.userid = ta.takipid');
        $this->db->group_by('ta.id');
        $return_query = $this->db->get();
        if ($return_query->num_rows() > 0) {
            return $return_query->result();
        } else {
            return false;
        }
    }
    public function cnlist($id){
        $this->db->from('takip ta');
        $this->db->join('register r','r.id=ta.takipedilenid');
        $this->db->join('twitler t','ta.takipedilenid=t.userid');
        $this->db->where(['takipid'=>$id]);
        $return_query = $this->db->get();
        if ($return_query->num_rows() > 0) {
            return $return_query->result();
        } else {
            return false;
        }
    }
    public function like($post)
    {
        $this->db->from('begen');
        $this->db->where((array)$post);
        $return_query = $this->db->get();
        if ($return_query->num_rows() > 0) {
            return false;
        } else {
            $this->db->set($post)->insert('begen');
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }
    public function yorumekle($post){
        $this->db->set($post)->insert('yorum');
        if($this->db->affected_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    public function yorumlist(){
        $this->db->from('yorum ');
        $return_query = $this->db->get();
        if ($return_query->num_rows() > 0) {
            return $return_query->result();
        } else {
            return false;
        }
    }

}