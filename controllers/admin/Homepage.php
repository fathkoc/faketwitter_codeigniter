<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Homepage extends Veripay_Controller
{
    function __construct()
    {
        parent:: __construct();

        $this->load->model('admin/'.$this->router->fetch_class() . '_model', 'model');

    }




}