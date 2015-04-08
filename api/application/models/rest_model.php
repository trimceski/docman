<?php
class Rest_Model extends CI_Model {

    function __construct()
    {
      parent::__construct();
        $this->load->database();
    }

    function get_documents($input = array()) {
        //$this->load->database();
        $start  =   isset($input["offset"]) ? $this->db->escape_str($input["offset"])   : '0';
        $limit  =   isset($input["limit"])  ? $this->db->escape_str($input["limit"])    : '10';
        $sort   =   isset($input["sort"])   ? $this->db->escape_str($input["sort"])     : 'id';
        $order  =   isset($input["order"])  ? $this->db->escape_str($input["order"])    : 'ASC';
        $search =   isset($input["search"]) ? $this->db->escape_str($input["search"])   : NULL;

        if ($search != NULL) {
            $filter = " WHERE documents.`name` LIKE '%$search%' OR documents.description LIKE '%$search%' OR categories.title LIKE '%$search%' OR documents.created LIKE '%$search%' OR documents.modified LIKE '%$search%' ";
        } else {
            $filter =' ';
        }


        $count = $this->db->query("SELECT COUNT(documents.id) AS total FROM categories INNER JOIN documents ON documents.category = categories.id{$filter}");
        $query = $this->db->query("SELECT documents.id, documents.category AS category_id, categories.title AS category, documents.`name`, documents.description, documents.filename, documents.created, documents.modified FROM categories INNER JOIN documents ON documents.category = categories.id{$filter}ORDER BY $sort $order LIMIT $start, $limit");
        return array(
            "total" =>  $count->row()->total,
            "rows"  =>  $query->result_array()
        );
    }

    function get_document($id) {
        //$this->load->database();
        $query = $this->db->query("SELECT * FROM documents WHERE id='{$this->db->escape_str($id)}'");
        return $query->result_array();
    }

    function delete_document($id) {
        //$this->load->database();
        return $this->db->delete('documents', array("id" => $id));
    }

    function add_document ($input) {
        //$this->load->database();
        $this->db->insert('documents', $input);
    }

    function update_document($id, $input) {
        //$this->load->database();
        $input['modified'] = date("Y-m-d H:i:s");
        $this->db->where('id', $id);
        $this->db->update('documents', $input);
    }

    function document_file($id = "") {
        //$this->load->database();
        $this->db->select('filename');
        $this->db->from('documents');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row()->filename;
    }

    function categories ($input = array()) {
        $start  =   isset($input["offset"]) ? $input["offset"]   : NULL;
        $limit  =   isset($input["limit"])  ? $input["limit"]    : NULL;

        //$this->load->database();
        return array(
            "total" => $this->db->count_all_results('categories'),
            "rows" => $this->db->get('categories', $limit, $start)->result_array()
        );
    }

    function categories_add ($input) {
        //$this->load->database();
        return $this->db->insert('categories', $input);
    }
}
?>
