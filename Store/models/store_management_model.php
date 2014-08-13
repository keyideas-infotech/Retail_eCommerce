<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Store_management_model extends BF_Model
{

    protected $table = "store";
    protected $key = "store_id";
    protected $soft_deletes = false;
    protected $date_format = "datetime";
    protected $set_created = true;
    protected $set_modified = false;
    protected $created_field = "created_date";
    protected $status_field = "status";
    protected $role_id;
    protected $lang_code = 'EN';
    public $lang_id = 0;
    public $is_merchant = FALSE;

    const ID = "store_id";
    const STORE_NAME = "store_name";
    const STORE_STATUS = "status";
    const STORE_MERCHANT_ID = "merchant_id";
    const STORE_LANG_ID = "lang_id";

    public function __construct()
    {
        parent::__construct();

        $store_table = $this->db->dbprefix($this->table);
        $user_table = $this->db->dbprefix("users");

        $config = array(
            "key" => "store_id",
            "table" => $this->table,
            "status_field" => $this->status_field,
            "created_on_field" => $this->created_field,
            "action" => array(
                "toggleStatus" => "_toggle_status",
                "delete" => "_delete",
                "deleteSelected" => "_delete_selected",
                "changePosition" => "_change_position",
                "export" => "_export"
            ),
            "export_column" => array(
                "{$store_table}.store_id AS 'Store Id'",
                "store_name AS 'Store Name'",
                "{$user_table}.display_name AS 'Merchant'",
                "address AS 'Address'",
                "city_name AS 'City Name'",
                "state_name AS 'State Name'",
                "region_name AS 'Region Name'",
                "country_name AS 'Country Name'",
                "zipcode AS Zipcode",
                "latitude AS Latitude",
                "longitude AS Longitude",
                "{$store_table}.{$this->created_field} AS 'Created Date'",
                "IF({$store_table}.status = 1, 'Active', 'Inactive') AS Status"
            ),
            "export_column_label" => array(
                "Store Id",
                "Store Name",
                "Merchant",
                "Address",
                "City Name",
                "State Name",
                "Region Name",
                "Country Name",
                "Zipcode",
                "Latitude",
                "Longitude",
                "Created Date",
                "Status"
            ),
            "file_name" => "Store"
        );

        $this->load->model('merchant_management/merchant_management_model', 'merchantwa');
        $this->load->model("date_model");
        $this->role_id = $this->merchantwa->getRoleId();

        $this->load->model('lang_model', null, true);
        if ($lang = $this->lang_model->find_by('lang_code', $this->lang_code)) {
            $this->lang_id = $lang->lang_id;
        }

        $this->load->library("EX_CH_Grid_generator", $config, "grid");
        $this->grid = new EX_CH_Grid_generator($config);

        $mer_role_id = $this->merchantwa->get_merchant_role_id();
        $role_id = $this->session->userdata('role_id');
        if ($role_id == $mer_role_id) {
            $this->is_merchant = TRUE;
        }
    }

    public function read($req_data)
    {
        $store_table = $this->db->dbprefix($this->table);
        $user_table = $this->db->dbprefix("users");
        $user_id = $this->session->userdata('user_id');

        if (!isset($req_data['category'])) {
            $req_data['category'] = "active";
        }

        $select = array($this->table . '.store_id', $this->table . '.merchant_id', $this->table . '.zipcode', $this->table . '.latitude', $this->table . '.longitude', $this->table . '.status', $this->table . '.created_date', 'users.display_name as merchant_name', 'store_details.store_name', 'store_details.address', 'country.country_name', 'country.country_id', 'region.region_name', 'region.region_id', 'state.state_name', 'state.state_id', 'city.city_name', 'city.city_id');
        $order = array(
            "sortby" => $this->created_field,
            "order" => "DESC"
        );
        $group_by = $this->table . '.store_id';

        $join = array(
            'users' => array(
                'condition' => "users.id = {$store_table}.merchant_id AND {$user_table}.active = 1",
                'type' => 'inner'
            ),
            'city' => array(
                'condition' => 'city.city_id = ' . $this->table . '.city_id ',
                'type' => 'left',
            ),
            'state' => array(
                'condition' => 'state.state_id = ' . $this->table . '.state_id ',
                'type' => 'left',
            ),
            'region' => array(
                'condition' => 'region.region_id = ' . $this->table . '.region_id ',
                'type' => 'left',
            ),
            'country' => array(
                'condition' => 'country.country_id = ' . $this->table . '.country_id ',
                'type' => 'left',
            ),
            'store_details' => array(
                'condition' => 'store_details.store_id = ' . $this->table . '.store_id ',
                'type' => 'left',
            ),
            'lang' => array(
                'condition' => 'lang.lang_id = ' . 'store_details.lang_id',
                'type' => 'left'
            )
        );

        $where = array();
        $country_id = $this->session->userdata("default_country_id");
        if ($country_id != "all") {
            $where['store.country_id'] = $country_id;
        }


        $where['store_details.lang_id'] = $this->lang_id;
        if (isset($req_data['search_city_id']) && !empty($req_data['search_city_id'])) {
            $where['store.city_id'] = $req_data['search_city_id'];
        }
        if (isset($req_data['search_state_id']) && !empty($req_data['search_state_id'])) {
            $where['store.state_id'] = $req_data['search_state_id'];
        }
        if (isset($req_data['search_region_id']) && !empty($req_data['search_region_id'])) {
            $where['store.region_id'] = $req_data['search_region_id'];
        }
        if (isset($req_data['search_country_id']) && !empty($req_data['search_country_id']) && ($req_data['search_country_id'] != "all")) {
            $where['store.country_id'] = $req_data['search_country_id'];
        }
        if (isset($req_data['search_merchant_id']) && !empty($req_data['search_merchant_id'])) {
            $where['store.merchant_id'] = $req_data['search_merchant_id'];
        }

        if ($this->is_merchant) {
            $where['store.merchant_id'] = $user_id;
        }


        if (isset($req_data['date_range_filter']) && !empty($req_data['date_range_filter'])) {
            $between = array();
            $date = new Date_model();
            if ($req_data['date_range_filter'] == 4) {
                if (!empty($req_data["from_date"]) && !empty($req_data["to_date"])) {
                    /*$between['created_date'] = array(
                        "from" => $req_data["from_date"],
                        "to" => $req_data["to_date"]
                    );*/
                    $between['created_date'] = date_like_hell($req_data);
                }
            } else {
                $date->setNo($req_data['date_range_filter']);
                $dateRange = $date->getStartEndDate();
                $between['created_date'] = array(
                    "from" => $dateRange["start_date"],
                    "to" => $dateRange["end_date"]
                );
            }
            if (!empty($between)) {
//                $req_data['between'] = $between;
                $where[] = "DATE({$store_table}.{$this->created_field}) BETWEEN '{$between[$this->created_field]['from']}' AND '{$between[$this->created_field]['to']}'";
            }
        }

//        dump($req_data);

        $this->grid->initialize(array(
            "req_data" => $req_data,
            "select" => $select,
            "where" => $where,
            "join" => $join,
            "order" => $order,
            "group_by" => $group_by
        ));
        $r = $this->grid->get_result();
        /*echo "<br/><br/><br/><br/><br/><br/>";
        echo "<pre>";
        print_r($this->db->last_query());
        echo "</pre>";*/
        return $r;
//        return $this->grid->get_result();
    }

    public function get_stores_by_filter($filter)
    {
        $this->db->select('store.store_id, store_details.store_name')
            ->from('store')
            ->join('store_details', 'store_details.store_id = store.store_id', 'left')
            ->join('city', 'city.city_id = store.city_id', 'left')
            ->join('state', 'state.state_id = store.state_id', 'left')
            ->join('region', 'region.region_id = store.region_id', 'left')
            ->join('country', 'country.country_id = store.country_id', 'left')
            ->where('store_details.lang_id', $this->lang_id)
            ->where('store.status', 1)
            ->group_by('store.store_id');
        if (isset($filter['country_id'])) {
            $this->db->where('store.country_id', $filter['country_id']);
        }
        if (isset($filter['region_id'])) {
            $this->db->where('store.region_id', $filter['region_id']);
        }
        if (isset($filter['state_id'])) {
            $this->db->where('store.state_id', $filter['state_id']);
        }
        if (isset($filter['city_id'])) {
            $this->db->where('store.city_id', $filter['city_id']);
        }

        $mer_role_id = $this->role_id;
        $role_id = $this->session->userdata('role_id');
        $user_id = $this->session->userdata('user_id');
        if ($role_id == $mer_role_id) {
            $this->db->where('store.merchant_id', $user_id);
        } elseif (isset($filter['merchant_id'])) {
            $this->db->where('store.merchant_id', $filter['merchant_id']);
        }

        $result = $this->db->get();
        if ($result->num_rows()) {
            return $result->result_array();
        }
        return FALSE;
    }

    public function validateMerchantStore($data)
    {
        $store_id = isset($data['store_id']) ? $data['store_id'] : 0;
        $merchant_id = $data['merchant_id'];
        $store_name = trim($data['store_name']);
        if (!$store_id) {
            //check unique store name for selected merchant
            $select = $this->db->select('store_id')->from('store')
                ->where('merchant_id', $merchant_id)
                ->where('store_name', $store_name)
                ->where('status', 1);
        } else {
            //check unique store name for selected merchant except changed name
            $select = $this->db->select('store_id')->from('store')
                ->where('merchant_id', $merchant_id)
                ->where('store_name', $store_name)
                ->where('store_id !=', $store_id)
                ->where('status', 1);
        }
        $result = $select->get(); //Get Resultset

        if ($result->num_rows()) {
            return FALSE;
        }
        return TRUE;
    }

    public function recalcLatLong($store_id, $store_address)
    {
        $store = $this->find($store_id);
        if ($store) {
            $this->load->model('address_model');
            $city_info = $this->address_model->get_city_info_by_id($store->city_id);

            $address = $store_address . ' ' . $city_info['city_name'] . ' ' . $store->zipcode . ', ' . $city_info['state_name'] . ', ' . $city_info['country_name'];

            $latLng = $this->address_model->getLatLngByAddress($address);
            $lat = isset($latLng['lat']) ? $latLng['lat'] : 0.0;
            $lng = isset($latLng['lng']) ? $latLng['lng'] : 0.0;
            $data = array(
                'latitude' => $lat,
                'longitude' => $lng,
            );
            $this->update($store_id, $data);
        }
    }

    //Dashboard methods...

    public function get_merchant_stores($filter)
    {

        $store_table = $this->db->dbprefix("store");
        $store_detail_table = $this->db->dbprefix("store_details");
        $merchant_table = $this->db->dbprefix("users");
        $merchant_detail_table = $this->db->dbprefix("user_details");

        $this->db->select("{$merchant_detail_table}.name, {$merchant_detail_table}.user_id, COUNT(*) AS no_of_store");
        $this->db->from($this->table);
        $this->db->join($store_detail_table, "{$store_table}.store_id = {$store_detail_table}.store_id AND {$store_detail_table}.lang_id = {$this->lang_id}");
        $this->db->join($merchant_table, "{$store_table}.merchant_id = {$merchant_table}.id AND {$merchant_table}.active = 1");
        $this->db->join($merchant_detail_table, "{$store_table}.merchant_id = {$merchant_detail_table}.user_id AND {$merchant_detail_table}.lang_id = {$this->lang_id}");
//        $this->db->where(array("{$store_table}.country_id" => $country_id, "{$store_table}.status" => 1));
        if (isset($filter['country_id']) && !empty($filter['country_id']) && ($filter['country_id'] != "all")) {
            $this->db->where(array("{$store_table}.country_id" => $filter['country_id']));
        }
        if (isset($filter['date_range']) && is_array($filter['date_range']) && !empty($filter['date_range'])) {
            $this->db->where("DATE({$store_table}.created_date) BETWEEN '{$filter['date_range']['start_date']}' AND '{$filter['date_range']['end_date']}' ");
        }
        $this->db->where(array("{$store_table}.status" => 1));
        $this->db->group_by("{$store_table}.merchant_id");
        $query = $this->db->get();
//        dump($this->db->last_query());
        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return FALSE;

    }

    public function get_state_stores($filter)
    {
        $country_id = $filter['country_id'];
        $store_table = $this->db->dbprefix("store");
        $state_table = $this->db->dbprefix("state");
        $merchant_table = $this->db->dbprefix("users");

        $this->load->model("address_model");
        $state_filter = array();
        if ($country_id != "all") {
            $state_filter['country_id'] = $country_id;
        }
        $states = $this->address_model->get_states_by_filter($state_filter);
        $state_ids = array();

        $role_id = $this->session->userdata("role_id");
        $user_id = $this->session->userdata("user_id");
        $is_merchant = FALSE;
        if ($role_id == "7") {
            $is_merchant = TRUE;
        }

        if ($states) {
            foreach ($states as $state) {
                $state_ids[] = $state['state_id'];
            }
        }

        if (!empty($state_ids)) {
            $this->db->select("{$state_table}.state_name, {$state_table}.state_id,  COUNT(*) AS no_of_stores");
            $this->db->from($this->table);
            $this->db->join($merchant_table, "{$store_table}.merchant_id = {$merchant_table}.id AND {$merchant_table}.active = 1", "inner");
            $this->db->join($state_table, "{$store_table}.state_id = {$state_table}.state_id", "left");
            if ($is_merchant) {
                $this->db->where("{$store_table}.merchant_id", $user_id);
            }
            $this->db->where("{$store_table}.state_id IN(" . implode(",", $state_ids) . ")");
            if (isset($filter['date_range']) && is_array($filter['date_range']) && !empty($filter['date_range'])) {
                $this->db->where("DATE({$store_table}.created_date) BETWEEN '{$filter['date_range']['start_date']}' AND '{$filter['date_range']['end_date']}' ");
            }
            $this->db->where("{$store_table}.status", 1);
            $this->db->group_by("{$state_table}.state_id");
            $query = $this->db->get();
//            dump($this->db->last_query());

            if ($query->num_rows() > 0) {
                return $query->result();
            }
        }

        return FALSE;

    }

    public function get_state_beacons($filter)
    {

        $country_id = $filter['country_id'];
        $store_table = $this->db->dbprefix("store");
        $beacon_table = $this->db->dbprefix("beacon");
        $state_table = $this->db->dbprefix("state");
        $merchant_table = $this->db->dbprefix("users");


        $this->load->model("address_model");
        $state_filter = array();
        if ($country_id != "all") {
            $state_filter['country_id'] = $country_id;
        }
        $states = $this->address_model->get_states_by_filter($state_filter);
        $state_ids = array();

        if ($states) {
            foreach ($states as $state) {
                $state_ids[] = $state['state_id'];
            }
        }

        if (!empty($state_ids)) {
            $this->db->select("{$state_table}.state_name, {$state_table}.state_id, COUNT(*) AS no_of_beacons");
            $this->db->from($beacon_table);
            $this->db->join($store_table, "{$beacon_table}.store_id = {$store_table}.store_id AND {$store_table}.status = 1", "inner");
            $this->db->join($merchant_table, "{$store_table}.merchant_id = {$merchant_table}.id AND {$merchant_table}.active = 1", "inner");
            $this->db->join($state_table, "{$store_table}.state_id = {$state_table}.state_id", "left");
            $this->db->where("{$store_table}.state_id IN(" . implode(",", $state_ids) . ")");
            if (isset($filter['date_range']) && is_array($filter['date_range']) && !empty($filter['date_range'])) {
                $this->db->where("DATE({$beacon_table}.created_date) BETWEEN '{$filter['date_range']['start_date']}' AND '{$filter['date_range']['end_date']}' ");
            }
            $this->db->where("{$beacon_table}.status", 1);
            $this->db->group_by("{$state_table}.state_id");
            $query = $this->db->get();
//            dump($this->db->last_query());

            if ($query->num_rows() > 0) {
                return $query->result();
            }
        }

        return FALSE;

    }

    public function get_region_stores($filter)
    {

        $country_id = $filter['country_id'];
        $store_table = $this->db->dbprefix("store");
        $region_table = $this->db->dbprefix("region");
        $merchant_table = $this->db->dbprefix("users");

        $role_id = $this->session->userdata("role_id");
        $user_id = $this->session->userdata("user_id");
        $is_merchant = FALSE;
        if ($role_id == "7") {
            $is_merchant = TRUE;
        }

        $this->load->model("address_model");
        $region_filter = array();
        if ($country_id != "all") {
            $region_filter['country_id'] = $country_id;
        }
        $regions = $this->address_model->get_regions_by_filter($region_filter);
        $region_ids = array();

        if ($regions) {
            foreach ($regions as $region) {
                $region_ids[] = $region['region_id'];
            }
        }
        //dump($regions);

        if (!empty($region_ids)) {

            $this->db->select("{$region_table}.region_name, {$region_table}.region_id , COUNT(*) AS no_of_stores");
            $this->db->from($this->table);
            $this->db->join($merchant_table, "{$store_table}.merchant_id = {$merchant_table}.id AND {$merchant_table}.active = 1");
            $this->db->join($region_table, "{$store_table}.region_id = {$region_table}.region_id", "left");
            if ($is_merchant) {
                $this->db->where("{$store_table}.merchant_id", $user_id);
            }
            $this->db->where("{$store_table}.region_id IN(" . implode(",", $region_ids) . ")");
            if (isset($filter['date_range']) && is_array($filter['date_range']) && !empty($filter['date_range'])) {
                $this->db->where("DATE({$store_table}.created_date) BETWEEN '{$filter['date_range']['start_date']}' AND '{$filter['date_range']['end_date']}' ");
            }
            $this->db->where("{$store_table}.status", 1);
            $this->db->group_by("{$region_table}.region_id");
            $query = $this->db->get();
            //dump($this->db->last_query());

            if ($query->num_rows() > 0) {
                return $query->result();
            }
        }

        return FALSE;

    }

    public function get_region_beacons($filter)
    {
        $country_id = $filter['country_id'];
        $store_table = $this->db->dbprefix("store");
        $beacon_table = $this->db->dbprefix("beacon");
        $region_table = $this->db->dbprefix("region");
        $merchant_table = $this->db->dbprefix("users");

        $this->load->model("address_model");
        $region_filter = array();
        if ($country_id != "all") {
            $region_filter['country_id'] = $country_id;
        }
        $regions = $this->address_model->get_regions_by_filter($region_filter);
        $region_ids = array();

        if ($regions) {
            foreach ($regions as $region) {
                $region_ids[] = $region['region_id'];
            }
        }

        if (!empty($region_ids)) {
            $this->db->select("{$region_table}.region_name, {$region_table}.region_id, COUNT(*) AS no_of_beacons");
            $this->db->from($beacon_table);
            $this->db->join($store_table, "{$beacon_table}.store_id = {$store_table}.store_id AND {$store_table}.status = 1", "left");
            $this->db->join($merchant_table, "{$store_table}.merchant_id = {$merchant_table}.id AND {$merchant_table}.active = 1");
            $this->db->join($region_table, "{$store_table}.region_id = {$region_table}.region_id", "left");
            $this->db->where("{$store_table}.region_id IN(" . implode(",", $region_ids) . ")");
            if (isset($filter['date_range']) && is_array($filter['date_range']) && !empty($filter['date_range'])) {
                $this->db->where("DATE({$beacon_table}.created_date) BETWEEN '{$filter['date_range']['start_date']}' AND '{$filter['date_range']['end_date']}' ");
            }
            $this->db->where("{$beacon_table}.status", 1);
            $this->db->group_by("{$region_table}.region_id");
            $query = $this->db->get();
            //dump($this->db->last_query());

            if ($query->num_rows() > 0) {
                return $query->result();
            }
        }

        return FALSE;

    }

    public function get_store_beacons($filter)
    {

        //$this->load->model("merchant_management/merchant_management_model");

        $country_id = $filter['country_id'];
        $store_table = $this->db->dbprefix("store");
        $store_detail_table = $this->db->dbprefix("store_details");
        $beacon_table = $this->db->dbprefix("beacon");
        $merchant_table = $this->db->dbprefix("users");
        $region_table = $this->db->dbprefix("region");

        $role_id = $this->session->userdata("role_id");
        $user_id = $this->session->userdata("user_id");
        $is_merchant = FALSE;
        if ($role_id == "7") {
            $is_merchant = TRUE;
        }

        $this->db->select("{$store_detail_table}.store_name, COUNT(*) AS no_of_beacons");
        $this->db->from($beacon_table);
        $this->db->join($store_table, "{$beacon_table}.store_id = {$store_table}.store_id AND {$store_table}.status = 1 ", "inner");
        $this->db->join($store_detail_table, "{$store_table}.store_id = {$store_detail_table}.store_id AND {$store_detail_table}.lang_id = {$this->lang_id} ", "left");
        $this->db->join($merchant_table, "{$store_table}.merchant_id = {$merchant_table}.id AND {$merchant_table}.active = 1", "inner");
        if ($is_merchant) {
            $this->db->where("{$store_table}.merchant_id", $user_id);
        }
        if ($country_id != "all") {
            $this->db->where("{$store_table}.country_id", $country_id);
        }
        if (isset($filter['date_range']) && is_array($filter['date_range']) && !empty($filter['date_range'])) {
            $this->db->where("DATE({$beacon_table}.created_date) BETWEEN '{$filter['date_range']['start_date']}' AND '{$filter['date_range']['end_date']}' ");
        }
        $this->db->where("{$beacon_table}.status", 1);
        $this->db->group_by("{$store_table}.store_id");

        $query = $this->db->get();

        //dump($this->db->last_query());

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return FALSE;

    }

    public function find_all_offers($store_id)
    {

//Find all instore or walk-in offers on this beacon...
        $beacon_table = $this->db->dbprefix("beacon");
        $beacon_offer_table = $this->db->dbprefix("beacon_offers");
        $offer_table = $this->db->dbprefix("offer");
        $offer_details_table = $this->db->dbprefix("offer_details");

        $sql = "SELECT {$offer_table}.*, {$offer_details_table}.offer_message FROM ({$beacon_offer_table}) ";
        $sql .= "INNER JOIN  {$offer_table} ON {$beacon_offer_table}.`offer_id` = {$offer_table}.`offer_id` AND {$offer_table}.offer_type != 2 ";
        $sql .= "INNER JOIN {$offer_details_table} ON {$offer_details_table}.`offer_id` = {$offer_table}.`offer_id` AND {$offer_details_table}.lang_id = {$this->lang_id} ";
        $sql .= "WHERE {$beacon_offer_table}.`beacon_id` IN ";
        $sql .= "(SELECT DISTINCT(beacon_id) from {$beacon_table} as b  where b.store_id = {$store_id} AND b.status = 1) ";
        $sql .= "GROUP BY {$beacon_offer_table}.offer_id ";
        $sql .= "UNION ALL (SELECT of.*, ofd.offer_message FROM ib_store_offers sof ";
        $sql .= "INNER JOIN ib_offer of ON sof.`offer_id` = of.`offer_id` AND of.offer_type = 2 ";
        $sql .= "INNER JOIN ib_offer_details ofd ON ofd.`offer_id` = of.`offer_id` AND ofd.lang_id = {$this->lang_id} ";
        $sql .= "WHERE sof.store_id = {$store_id} GROUP BY sof.offer_id )";
        $query = $this->db->query($sql);
//        dump($this->db->last_query());
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return FALSE;
    }

    public function find_all_beacons($store_id)
    {
        $this->load->model('beacon_management/beacon_management_model');
        $beacons = $this->beacon_management_model->find_all_by(array('status' => 1, 'store_id' => $store_id));
        return $beacons;
    }

    /*public function getStores()
    {
        $select = $this->db->select('store.store_id, store_details.store_name')->from('store_details')
            ->join('store', 'store.store_id = store_details.store_id', 'left')
            ->where('store.status', '1');

        $result = $select->get(); //Get Resultset
        if ($result->num_rows()) {
            $store_data = $result->result_array();
            return $store_data;
        }
        return FALSE;
    }*/

    public function getStores($id = 0,$is_merchant=false)
    {
        $select = $this->db->select('store.store_id, store_details.store_name')->from('store_details')
            ->join('store', 'store.store_id = store_details.store_id', 'left')
            ->where('store.status', '1');
        if ($id > 0) {
            $select->where('store.country_id', $id);
        }
		
		if($is_merchant){
			$select->where('store.merchant_id', $this->session->userdata("user_id"));
		}

        $this->db->group_by("store_details.store_id");

        $result = $select->get(); //Get Resultset
        //dump($this->db->last_query());
        if ($result->num_rows()) {
            $store_data = $result->result_array();
            return $store_data;
        }
        return FALSE;
    }

}
