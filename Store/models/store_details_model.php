<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Store_details_model extends BF_Model
{

    protected $table = "store_details";
    protected $lang_code = 'EN';
    public $lang_id = 0;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('lang_model', null, true);
        if ($lang = $this->lang_model->find_by('lang_code', $this->lang_code)) {
            $this->lang_id = $lang->lang_id;
        }
    }

    public function get_multilang_field($id)
    {
        $this->db->select('*')
            ->from('store_details')
            ->join('lang', 'lang.lang_id = store_details.lang_id', 'left')
            ->where('store_id', $id);
        $result = $this->db->get();
        if ($result->num_rows()) {
            return $result->result();
        }
        return FALSE;
    }

    public function find_store_info_by_id($id){
        return $this->find_by(array("store_id" => $id, "lang_id" => $this->lang_id));
    }



}

?>
