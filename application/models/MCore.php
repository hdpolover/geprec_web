<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MCore extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function list_data($table, $order = '')
    {
        $this->db->order_by($order);
        return $this->db->get($table);
    }

    public function get_data($table, $arrWhere, $order = '')
    {
        $this->db->order_by($order);
        $sql = $this->db->get_where($table, $arrWhere);
        return $sql;
    } 
    
    public function select_data($select, $table, $where = false, $order = '')
    {
        $this->db->select($select);

        $this->db->from($table);

        if ($where != false) {
            $this->db->where($where);
        }

        $this->db->order_by($order);

        $sql = $this->db->get();
        return $sql;
    }

    public function get_newid($table, $pk)
    {
        $this->db->select_max($pk);
        $dt = $this->db->get($table)->row_array();
        return abs($dt[$pk]) + 1;
    }

    public function get_lastid($table, $pk)
    {
        $this->db->select_max($pk);
        $dt = $this->db->get($table)->row_array();
        return abs($dt[$pk]);
    }

    public function save_data($table, $data, $is_update = false, $arrWhere = [])
    {
        if ($is_update) {
            $this->db->where($arrWhere);
            $sql = $this->db->update($table, $data);
        } else {
            $sql = $this->db->insert($table, $data);
        }
        return $sql;
    }

    public function delete_data($table, $where)
    {
        $this->db->where($where);
        $sql = $this->db->delete($table);
        return $sql;
    }

    public function join_table($options)
    {

        $select = false;
        $table = false;
        $join = false;
        $order = false;
        $limit = false;
        $offset = false;
        $where = false;
        $or_where = false;
        $single = false;
        $where_in = false;
        $like = false;
        $group = false;

        extract($options);

        if ($select != false)
            $this->db->select($select);

        if ($table != false)
            $this->db->from($table);

        if ($where != false)
            $this->db->where($where);

        if ($where_in != false) {
            foreach ($where_in as $key => $value) {
                if (count($value) > 0)
                    $this->db->where_in($key, $value);
            }
        }

        if ($like != false) {
            $this->db->like($like);
        }

        if ($or_where != false)
            $this->db->or_where($or_where);

        if ($limit != false) {

            if (!is_array($limit)) {
                $this->db->limit($limit);
            } else {
                foreach ($limit as $limitval => $offset) {
                    $this->db->limit($limitval, $offset);
                }
            }
        }


        if ($order != false) {

            foreach ($order as $key => $value) {

                if (is_array($value)) {
                    foreach ($order as $orderby => $orderval) {
                        $this->db->order_by($orderby, $orderval);
                    }
                } else {
                    $this->db->order_by($key, $value);
                }
            }
        }

        if ($group != false) {
            $this->db->group_by($group);
        }

        if ($join != false) {

            foreach ($join as $key => $value) {

                if (is_array($value)) {

                    if (count($value) == 3) {
                        $this->db->join($value[0], $value[1], $value[2]);
                    } else {
                        foreach ($value as $key1 => $value1) {
                            $this->db->join($key1, $value1);
                        }
                    }
                } else {
                    $this->db->join($key, $value);
                }
            }
        }

        $query = $this->db->get();

        if ($single) {
            return $query->row();
        }

        // return $query->result();
        return $query;
    }
    // datatables server side
    private function _get_datatables_query($options)
    {

        $select = false;
        $table = false;
        $join = false;
        $order = false;
        $column_order = false;
        $column_search = false;
        $limit = false;
        $offset = false;
        $where = false;
        $or_where = false;
        $single = false;
        $where_not_in = false;
        $where_in = false;
        $column_where_in = false;
        $like = false;
        $group = false;

        extract($options);

        if ($select != false) {
            $this->db->select($select);
        }

        if ($table != false) {
            $this->db->from($table);
        }

        $i = 0;
        if ($column_search != false) {
            foreach ($column_search as $item) // loop kolom 
            {
                if (isset($this->input->post('search')['value'])) // jika datatable mengirim POST untuk search
                {
                    if ($i === 0) // looping pertama
                    {
                        $this->db->group_start();
                        $this->db->like($item, $this->input->post('search')['value']);
                    } else {
                        $this->db->or_like($item, $this->input->post('search')['value']);
                    }
                    if (count($column_search) - 1 == $i) //looping terakhir
                        $this->db->group_end();
                }
                $i++;
            }
        }

        if ($group != false) {
            $this->db->group_by($group);
        }

        if ($where != false) {
            $this->db->where($where);
        }

        if ($where_in != false) {
            $this->db->where_in($column_where_in, $where_in);
        }

        // jika datatable mengirim POST untuk order
        if ($this->input->post('order')) {
            $this->db->order_by($column_order[$this->input->post('order')['0']['column']], $this->input->post('order')['0']['dir']);
        } else if (isset($order)) {
            if ($order != false) {

                foreach ($order as $key => $value) {

                    if (is_array($value)) {
                        foreach ($order as $orderby => $orderval) {
                            $this->db->order_by($orderby, $orderval);
                        }
                    } else {
                        $this->db->order_by($key, $value);
                    }
                }
            }
        }

        if ($join != false) {

            foreach ($join as $key => $value) {

                if (is_array($value)) {

                    if (count($value) == 3) {
                        $this->db->join($value[0], $value[1], $value[2]);
                    } else {
                        foreach ($value as $key1 => $value1) {
                            $this->db->join($key1, $value1);
                        }
                    }
                } else {
                    $this->db->join($key, $value);
                }
            }
        }
    }

    // datatables server side
    function get_datatables($options)
    {
        $this->_get_datatables_query($options);
        if ($this->input->post('length') != -1)
            $this->db->limit($this->input->post('length'), $this->input->post('start'));

        $query = $this->db->get();
        return $query->result();
    }

    // datatables server side
    function count_filtered($options)
    {
        $this->_get_datatables_query($options);

        $query = $this->db->get();
        return $query->num_rows();
    }

    // datatables server side
    public function count_all($table, $join = null, $where = null)
    {
        $this->db->from($table);

        if ($join != false) {

            foreach ($join as $key => $value) {

                if (is_array($value)) {

                    if (count($value) == 3) {
                        $this->db->join($value[0], $value[1], $value[2]);
                    } else {
                        foreach ($value as $key1 => $value1) {
                            $this->db->join($key1, $value1);
                        }
                    }
                } else {
                    $this->db->join($key, $value);
                }
            }
        }

        if ($where != false) {
            $this->db->where($where);
        }

        return $this->db->count_all_results();
    }
}