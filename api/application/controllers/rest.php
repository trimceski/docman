<?php
require(APPPATH.'libraries/REST_Controller.php');

class Rest extends REST_Controller {

    function index_get() {
        $this->load->model("Rest_Model");
        $this->response($this->Rest_Model->get_documents($this->input->get(NULL, TRUE)));
    }

    function index_post() {
        $config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'doc|docx|pdf';
		$config['max_size']	= '0';

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload("file")) {
			$this->response(array(
                "error" => true,
                "message" => $this->upload->display_errors()
            ));
		} else {
            $input = $this->input->post(NULL, TRUE);
            $data = $this->upload->data();
            $input['filename'] = $data['file_name'];
            $input['created'] = date("Y-m-d H:i:s");


            $this->load->model("Rest_Model");
            $this->Rest_Model->add_document($input);

			$this->response(array(
                "error" => false
            ));
		}
    }

    function document_delete() {
        $id = $this->uri->segment(2);

        $this->load->model("Rest_Model");
        $name = $this->Rest_Model->document_file($id);
        if ($this->Rest_Model->delete_document($id)) {
            @unlink("./uploads/$name");

            $this->response(array(
                "error" => false
            ));
        } else {
            $this->response(array(
                "error" => true
            ));
        }
    }

    function document_put() {
        $id = $this->uri->segment(2);
        $this->load->model("Rest_Model");
        $this->Rest_Model->update_document($id, $this->put());

        $this->response(array(
            "error" => false
        ));
    }

    function document_get() {
        $this->load->model("Rest_Model");
        $this->response($this->Rest_Model->get_document($this->uri->segment(2)));
    }

    function error_get() {
        $this->response(array(
            "error" => true,
            "techincal" => true
        ));
    }

    function download_get() {
        $id = $this->uri->segment(2);

        $this->load->model("Rest_Model");

        $name = $this->Rest_Model->document_file($id);
        $data = file_get_contents("./uploads/$name");


        $this->load->helper('download');
        force_download($name, $data);
    }

    function categories_get() {
        $this->load->model("Rest_Model");
        $this->response($this->Rest_Model->categories($this->get()));
    }

    function categories_post() {
        $this->load->model("Rest_Model");
        if($this->Rest_Model->categories_add($this->post())) {
            $this->response(array(
                "error" => false
            ));
        } else {
            $this->response(array(
                "error" => true
            ));
        }
    }
}
