<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class store extends Admin_Controller
{

    private $codes;

    //--------------------------------------------------------------------
    public function __construct()
    {
        parent::__construct();
        $this->codes = array(
            'SN' => array(
                'type' => 'input',
                'field' => 'store_name',
                'label' => 'Store Name'
            ),
            'SA' => array(
                'type' => 'textarea',
                'field' => 'address',
                'label' => 'Address'
            )
        );
        $this->auth->restrict('Store_Management.Store.View');

        //loading models
        $this->load->model('merchant_management/merchant_management_model', null, true);
        $this->load->model('address_model');
        $this->load->model('store_management/store_management_model');
        $this->load->model('store_management/store_details_model');
        $this->load->model('lang_model', null, true);

        //loading language
        $this->lang->load('store_management');

        //setting block
        Template::set_block('sub_nav', 'store/_sub_nav');
    }

    //--------------------------------------------------------------------

    /*
      Method: index()

      Displays a list of form data.
     */
    public function index()
    {
//        dump($this->input->all());
        $result = $this->store_management_model->read($this->input->all());
        if ($this->input->get_post("d_link")) {
            Template::set("d_link", TRUE);
        }
        if ($this->input->is_ajax_request()) {
            Template::set("ajax", TRUE);
            Template::set('req_data', (isset($result['data']) && !empty($result['data'])) ? $result['data'] : "");
        }
        Template::set('records', (isset($result['result']) && !empty($result['result'])) ? $result['result'] : '');
        Template::set('pagination', (isset($result['pagination']) && !empty($result['pagination'])) ? $result['pagination'] : '');
        Assets::add_js('grid.js');

        $country_list = $this->address_model->get_country_list();
        $country_id = $this->session->userdata('default_country_id') ? $this->session->userdata('default_country_id') : $country_list[0]['country_id'];
        $country_filter = array();
        if ($country_id != "all") {
            $country_filter['country_id'] = $country_id;
        }

        Template::set('city_list', $this->address_model->get_cities_by_filter($country_filter));
        Template::set('state_list', $this->address_model->get_states_by_filter($country_filter));
        Template::set('region_list', $this->address_model->get_regions_by_filter($country_filter));
        Template::set('country_list', $country_list);

        Template::set('is_merchant', $this->store_management_model->is_merchant);

        //Merchant list
        $merchantArr = $this->merchant_management_model->getMerchants(0, true);
        Template::set('merchant_list', $merchantArr);

        Template::set('toolbar_title', 'Store List');

        Assets::add_css("flick/jquery-ui-1.8.13.custom.css");
        Assets::add_js("jquery-ui-1.8.13.min.js");

        Template::render();
    }

    public function export()
    {
        $this->store_management_model->read($this->input->post());
    }


    //--------------------------------------------------------------------


    /*
      Method: create()

      Creates a Store object.
     */
    public function create()
    {

        $this->load->model('category/category_model');

        $this->auth->restrict('Store_Management.Store.Create');
        Assets::add_module_css('store_management', 'store_management.css');
        Assets::add_module_js('store_management', 'store_management.js');

        if ($this->input->post('store_name')) {
            $response = array();

            if ($this->input->post('id')) {
                $id = $this->save_store_management($this->input->post('id'), 'update');
            } else {
                $id = $this->save_store_management();
            }

            if (!empty($id)) {
                $response['success'] = $id;
            } else {
                $response['error'] = 'error';
            }
            echo json_encode($response);
            die();
        }


        //Merchant list
        $merchantArr = $this->merchant_management_model->getMerchants(0, true);
        Template::set('merchants', $merchantArr);

        //country list
        Template::set('country_list', $this->address_model->get_country_list());
        Template::set('categories', $this->category_model->find_all_active_categories(array("status" => 1)));

        Template::set('toolbar_title', lang('store_management_create') . ' Store');
        Template::render();
    }

    //--------------------------------------------------------------------


    /*
      Method: edit()

      Allows editing of Store data.
     */
    public function edit()
    {
        Assets::add_module_css('store_management', 'store_management.css');
        Assets::add_module_js('store_management', 'store_management.js');

        $id = $this->uri->segment(5);
        if (empty($id)) {
            Template::set_message(lang('store_management_invalid_id'), 'error');
            redirect(SITE_AREA . '/store/store_management');
        }

        if (isset($_POST['save'])) {
            $this->auth->restrict('Store_Management.Store.Edit');

            $valid = TRUE;
            $this->form_validation->set_rules('store_name', 'Store Name', 'required|xss_clean|max_length[255]');
            $this->form_validation->set_rules('merchant_id', 'Merchant Id', 'required|max_length[11]');
            $this->form_validation->set_rules('address1', 'Address1', 'required|max_length[255]');
            $this->form_validation->set_rules('address2', 'Address2', 'max_length[255]');
            $this->form_validation->set_rules('city_id', 'City', 'required|max_length[255]');

            if ($this->form_validation->run()) {
                $data = array();
                $data['store_id'] = $id;
                $data['address_id'] = $_POST['address_id'];
                $data['store_name'] = trim($_POST['store_name']);
                $data['merchant_id'] = trim($_POST['merchant_id']);
                $data['address1'] = trim($_POST['address1']);
                $data['address2'] = $_POST['address2'];
                $data['zip'] = $_POST['zip'];
                $data['city_id'] = trim($_POST['city_id']);
                $data['status'] = $_POST['status'];
                $data['lang_id'] = trim($_POST['lang_id']);
                $data['category'] = $_POST['category'];
                if (!$this->update_store_management($data)) {
                    $valid = FALSE;
                }
            } else {
                $valid = FALSE;
            }
            if ($valid) {
                Template::set_message(lang('store_management_edit_success'), 'success');
            } else {
                Template::set_message(lang('store_management_edit_failure') . $this->store_manager_model->error, 'error');
            }
        } else if (isset($_POST['delete'])) {
            $this->auth->restrict('Store_Management.Store.Delete');

            if ($this->store_management_model->delete($id)) {
                // Log the activity
                $this->activity_model->log_activity($this->current_user->id, lang('store_management_act_delete_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'store_management');

                Template::set_message(lang('store_management_delete_success'), 'success');

                redirect(SITE_AREA . '/store/store_management');
            } else {
                Template::set_message(lang('store_management_delete_failure') . $this->store_management_model->error, 'error');
            }
        }
        $store_data = $this->store_management_model->find($id);
        Template::set('store_management', $store_data);
        Assets::add_module_js('store_management', 'store_management.js');

        Template::set('toolbar_title', lang('store_management_edit') . ' Store');
        //S--City list
        $city_list = $this->address_model->get_city_list();
        $city_list_dropdown = array();
        foreach ($city_list as $value) {
            $k = $value['city_id'];
            $city_list_dropdown[$k] = $value['city_name'];
        }
        Template::set('city_list_dropdown', $city_list_dropdown);
        //E--City list        
        $merchantArr = $this->store_management_model->getMerchants();
        Template::set('merchants', $merchantArr);
        $address_data = $this->merchant->get_merchant_info_by_id($store_data->merchant_id);
        Template::set('store_categories', $this->store_categories_model->getCategoriesByStoreid($store_data->store_id));
        Template::set('store_address', $address_data);
        Template::set('categories', $this->category->get_all_active_categories());
        Template::set('languages', $this->lang_model->find_all_languages());

        Template::render();
    }

    private function save_store_management($id = 0, $type = 'insert')
    {

        /*dump($_POST);
    die;*/

        $this->form_validation->set_rules('store_name', 'lang:store_management_store_name', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('address', 'lang:store_management_address1', 'required|trim');
        $this->form_validation->set_rules('zip', 'lang:store_management_zip', 'required');
        $this->form_validation->set_rules('city_id', 'lang:store_management_city', 'required');
        if (!$id) {
            $this->form_validation->set_rules('category_ids', 'lang:store_management_category_ids', 'required');
        }
        if ($this->form_validation->run() === FALSE) {
            return FALSE;
        }

        $data = (array)$this->input->post();

        //1st step Save store                       
        $city_info = $this->address_model->get_city_info_by_id($data['city_id']);

        $address = $data['address'] . ' ' . $city_info['city_name'] . ' ' . $data['zip'] . ', ' . $city_info['state_name'] . ', ' . $city_info['country_name'];

        if ($this->input->post('lat') && $this->input->post('lng')) {
            $lat = $this->input->post('lat');
            $lng = $this->input->post('lng');
        } else {
            $latLng = $this->address_model->getLatLngByAddress($address);
            $lat = isset($latLng['lat']) ? $latLng['lat'] : 0.0;
            $lng = isset($latLng['lng']) ? $latLng['lng'] : 0.0;
        }

        $lat = round($lat, 4);
        $lng = round($lng, 4);

        $store_data = array();
        $store_data['merchant_id'] = $data['merchant_id'];
        $store_data['country_id'] = $city_info['country_id'];
        $store_data['region_id'] = $city_info['region_id'];
        $store_data['state_id'] = $city_info['state_id'];
        $store_data['city_id'] = $city_info['city_id'];
        $store_data['zipcode'] = $data['zip'];
        $store_data['latitude'] = $lat;
        $store_data['longitude'] = $lng;
        $category_ids = $this->input->post("category_ids");

//        $store_data['status'] = 1;

        if (!$id) {
            $store_data['status'] = 1;
        }

        if ($type == 'insert') {
            $store_data['created_date'] = date('Y-m-d H:i:s');
            $store_data['modified_date'] = date('Y-m-d H:i:s');
        }

        if ($id) {
            if (!$this->store_management_model->update($id, $store_data)) {
                return FALSE;
            }
            if (!$this->input->post("from")) {
                $this->save_categories($category_ids, $id, "update");
            }
        } else {
            $id = $this->store_management_model->insert($store_data);
            $this->save_categories($category_ids, $id, "insert");
        }

        if (!$id) {
            return FALSE;
        }

        //2nd step Save store details
        $langs = $this->lang_model->find_all_languages();
        if ($type == 'insert') {
            $store_details_data = array();
            foreach ($langs as $lang) {
                $prefix = '';
                if ($lang->lang_code != 'EN') {
                    $prefix = $lang->lang_code . '-';
                }
                $store_details_data[] = array(
                    'store_id' => $id,
                    'store_name' => $prefix . $data['store_name'],
                    'address' => $prefix . $data['address'],
                    'lang_id' => $lang->lang_id
                );
            }
            if (!empty($store_details_data)) {
                $this->db->insert_batch('store_details', $store_details_data);
            }
        } else {
            foreach ($langs as $lang) {
                if ($lang->lang_code == 'EN') {
                    $store_details_data = array(
                        'store_name' => $data['store_name'],
                        'address' => $data['address'],
                    );
                    $where = array(
                        'store_id' => $id,
                        'lang_id' => $lang->lang_id
                    );
                    $this->db->update('store_details', $store_details_data, $where);
                }
            }
        }

        $return_data = array(
            'store_id' => $id,
            'lat' => $lat,
            'lng' => $lng,
            'merchant_id' => $data['merchant_id'],
            'city_id' => $data['city_id']
        );
        return $return_data;
    }

    public function delete()
    {
        $this->auth->restrict('Store_Management.Store.Delete');
        if ($this->input->get('id')) {
            $response = array();
            if ($this->store_management_model->delete($this->input->get('id'))) {
                $response['success'] = '';
            } else {
                $response['error'] = '';
            }
            echo json_encode($response);
            die;
        } else {
            Template::redirect();
        }
    }

    public function save_categories($category, $store_id, $type)
    {
        $data = array();
        $store_category_table = $this->db->dbprefix("store_categories");

        if ($type == "update") {
            $this->db->delete($store_category_table, array('store_id' => $store_id));
        }

        if ($category && is_array($category)) {
            foreach ($category as $value) {
                $data[] = array(
                    'store_id' => $store_id,
                    'category_id' => $value
                );
            }
        }

        if (!empty($data)) {
            $this->db->insert_batch($store_category_table, $data);
        }
    }

    //--------------------------------------------------------------------

    public function validate_store()
    {
        if ($this->input->is_ajax_request()) {
            $result = 'success';
            if ($this->input->get('store_name')) {
                $where = array(
                    'store_name' => urldecode($this->input->get('store_name'))
                );
                if ($this->input->get('id')) {
                    $where['store_id !='] = $this->input->get('id');
                }
                $data = $this->store_details_model->find_by($where);
                if (!empty($data)) {
                    $result = 'fail';
                }
            }
            echo $result;
            die;
        }
    }

    public function get_multilang_fields()
    {
        $codes = $this->codes;
        $code = $this->input->get('code');
        $id = $this->input->get('id');
        $lang_used = array();
        $lang_not_used = array();
        $additional = array();

//        $this->load->model("lang");
        $langs = $this->lang_model->find_all_languages_in_array();


        if ($id && $code && isset($codes[$code])) {
            $fields = $this->store_details_model->get_multilang_field($id);

//            d($fields);
            if (is_array($fields)) {
                foreach ($fields as $field) {
                    $lang_used[$field->lang_id] = $field;
                }
            }

            $lang_not_used = array_diff_key($langs, $lang_used);

            if (!empty($lang_not_used)) {
                foreach ($lang_not_used as $l) {
                    $obj = new stdClass();
                    $obj->store_id = $id;
                    if ($code == "SN") {
                        $obj->store_name = "";
                    }
                    $obj->store_desc = "";
                    if ($code == "SA") {
                        $obj->address = "";
                    }
                    $obj->lang_id = $l->lang_id;
                    $obj->lang_name = $l->lang_name;
                    $obj->lang_code = $l->lang_code;
                    $fields[] = $obj;
                }
            }

//            d($fields);


            Template::set('codes', $codes);
            Template::set('code', $code);
            Template::set('id', $id);
            Template::set('fields', $fields);
            Template::render('ajax');
        } else {
            Template::redirect();
        }
    }

    public function save_multilang_fields()
    {
        $codes = $this->codes;
        $code = $this->input->post('code');
        $id = $this->input->post('id');
        if ($id && $code && isset($codes[$code])) {

            $data = $this->input->post('data');
            $success = true;
            foreach ($data as $key => $value) {

                $store_details_data = $value;
                $where = array(
                    'store_id' => $id,
                    'lang_id' => $key
                );

                $this->db->select("*");
                $this->db->from('store_details');
                $this->db->where($where);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
//Update...
                    if (!$this->db->update('store_details', $store_details_data, $where)) {
                        $success = false;
                    }
                } else {
//Insert...
                    $insert_data = array_merge($where, $store_details_data);
//                    dd($insert_data);

                    if (!$this->db->insert('store_details', $insert_data)) {
                        $success = false;
                    }

                }
            }
            if ($code == 'SA') {
                $this->load->model('lang_model', null, true);
                if ($lang = $this->lang_model->find_by('lang_code', 'EN')) {
                    $this->store_management_model->recalcLatLong($id, $data[$lang->lang_id]['address']);
                }
            }
            if ($success) {
                echo json_encode(array(
                    'success' => ''
                ));
            } else {
                echo json_encode(array(
                    'error' => ''
                ));
            }
            die();
        } else {
            Template::redirect();
        }
    }

    public function add_store_category_view()
    {
        $this->load->model('category/category_model');
        $this->load->model('store_management/store_details_model');

        $store_id = $this->input->get('store_id');
        if ($this->input->is_ajax_request() && $store_id) {

            $store_info = $this->store_details_model->find_store_info_by_id($store_id);

            Template::set('store_id', $store_id);
            Template::set('store_info', $store_info);

            Template::set('country_list', $this->address_model->get_country_list());
            Template::set('categories', $this->category_model->find_all_active_categories(array("status" => 1)));
            Template::render();
        } else {
            Template::redirect();
        }
    }


    public function edit_store_category_view()
    {
        $this->load->model('category/category_model');
        $this->load->model('store_management/store_details_model');

        $store_id = $this->input->get('store_id');
        if ($this->input->is_ajax_request() && $store_id) {

            //find selected beacons of this offer...
            $this->load->model("store_management/store_category_model");
            $store_info = $this->store_details_model->find_store_info_by_id($store_id);

            $categories = $this->store_category_model->find_all_by(array("store_id" => $store_id));
            $assigned_categories = array();
            if ($categories) {
                foreach ($categories as $category) {
                    $assigned_categories[] = $category->category_id;
                }
            }
            Template::set('store_id', $store_id);
            Template::set('store_info', $store_info);

            Template::set('categories', $this->category_model->find_all_active_categories(array("status" => 1)));
            Template::set('assigned_categories', $assigned_categories);
            Template::render();
        } else {
            Template::redirect();
        }
    }

    public function save_store_category()
    {

//        dump($_POST); die;
        $store_id = $this->input->post('store_id');
        $category_ids = $this->input->post('category_ids');
        $store_category_table = $this->db->dbprefix("store_categories");

        if ($this->input->post("type")) {
            $this->db->delete($store_category_table, array('store_id' => $store_id));
        }

        if ($this->input->is_ajax_request() && $store_id) {
            $data = array();
            if (is_array($category_ids)) {
                foreach ($category_ids as $value) {
                    $data[] = array(
                        'store_id' => $store_id,
                        'category_id' => $value
                    );
                }
            }
//            dd($data);
            if (!empty($data)) {
                $this->db->insert_batch($store_category_table, $data);
            }
            echo json_encode(array(
                'success' => ''
            ));
        }
        /*if ($this->input->is_ajax_request() && $store_id && $category_ids) {
            $data = array();
            foreach ($category_ids as $value) {
                $data[] = array(
                    'store_id' => $store_id,
                    'category_id' => $value
                );
            }

            if ($this->input->post("type")) {
                $this->db->delete($store_category_table, array('store_id' => $store_id));
            }
            //dump($data);die;

            if (!empty($data)) {
                $this->db->insert_batch($store_category_table, $data);
            }
            echo json_encode(array(
                'success' => ''
            ));
        } else {
            if ($this->input->post("type")) {
                if (empty($category_ids)) {
                    $this->db->delete('category_stores', array('store_id' => $store_id));
                }
                echo json_encode(array(
                    'success' => ''
                ));
            } else {
                echo json_encode(array(
                    'error' => ''
                ));
            }
        }*/

        die;
    }

    public function find_store_offers()
    {
        $store_id = $this->input->get("store_id");
        if ($store_id) {
            $offers = $this->store_management_model->find_all_offers($store_id);
            $beacons = $this->store_management_model->find_all_beacons($store_id);
            Template::set("records", $offers);
            Template::set("beacons", $beacons);
            Template::render();
        } else {
            show_404();
        }
    }
}