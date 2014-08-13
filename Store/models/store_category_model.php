<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Store_category_model extends BF_Model
{

    protected $table = "store_categories";

    public function __construct()
    {
        parent::__construct();
    }

}

?>
