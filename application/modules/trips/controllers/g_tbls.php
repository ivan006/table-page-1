<?php
class G_tbls extends MY_Controller
{

	function __construct()
	{
		parent::__construct();

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	}

  public function insert($table)
  {
    // if ($this->input->is_ajax_request()) {


      // $this->form_validation->set_rules('name', 'Name', 'required');
      // $this->form_validation->set_rules('event_children', 'Event_children');

      // if ($this->form_validation->run() == FALSE) {
      //   $data = array('responce' => 'error', 'message' => validation_errors());
      // } else {
        $ajax_data = $this->input->post();
        if ($this->db->insert($table, $ajax_data)) {
          $data = array('responce' => 'success', 'message' => 'Record added Successfully');
        } else {
          $data = array('responce' => 'error', 'message' => 'Failed to add record');
        }
      // }

			return $data;
    // } else {
    // 	return "No direct script access allowed";
    // }
  }

  public function fetch($table)
  {
    // if ($this->input->is_ajax_request()) {
    // // if ($posts = $this->db->get($table)->result()) {
    // // 	$data = array('responce' => 'success', 'posts' => $posts);
    // // }else{
    // // 	$data = array('responce' => 'error', 'message' => 'Failed to fetch data');
    // // }
    $posts = $this->db->get($table)->result();
    $data = array('responce' => 'success', 'posts' => $posts);
    return $data;
    // } else {
    // 	return "No direct script access allowed";
    // }

  }

  public function delete($table)
  {
    // if ($this->input->is_ajax_request()) {
    $del_id = $this->input->post('del_id');

    if ($this->db->delete($table, array('id' => $del_id))) {
      $data = array('responce' => 'success');
    } else {
      $data = array('responce' => 'error');
    }
    return $data;
    // } else {
    // 	return "No direct script access allowed";
    // }
  }

  public function edit($table)
  {
    // if ($this->input->is_ajax_request()) {
    $edit_id = $this->input->post('edit_id');

    $this->db->select("*");
    $this->db->from($table);
    $this->db->where("id", $edit_id);
    $query = $this->db->get();
    $post = null;
    if (count($query->result()) > 0) {
      $post = $query->row();
    }
    if ($post) {
      $data = array('responce' => 'success', 'post' => $post);
    } else {
      $data = array('responce' => 'error', 'message' => 'failed to fetch record');
    }
    return $data;
    // } else {
    // 	return "No direct script access allowed";
    // }
  }

  public function update($table)
  {
    // if ($this->input->is_ajax_request()) {
	  //   $this->form_validation->set_rules('edit_name', 'Name', 'required');
	  //   $this->form_validation->set_rules('edit_event_children', 'Event_children');
	  //   if ($this->form_validation->run() == FALSE) {
	  //     $data = array('responce' => 'error', 'message' => validation_errors());
	  //   } else {

	      $data['id'] = $this->input->post('edit_record_id');


	      // $data['name'] = $this->input->post('edit_name');
	      // $data['event_children'] = $this->input->post('edit_event_children');
				$rows = $this->table_rows($table);
				foreach ($rows as $key => $value) {
					if ($value !== "id") {
						$data[$value] = $this->input->post('edit_'.$value);
					}
				}

	      if ($this->db->update($table, $data, array('id' => $data['id']))) {
	        $data = array('responce' => 'success', 'message' => 'Record update Successfully');
	      } else {
	        $data = array('responce' => 'error', 'message' => 'Failed to update record');
	      }
			  return $data;


	  //   }
	  //   return $data;
    // } else {
    // 	return "No direct script access allowed";
    // }
  }

  public function table_rows($table)
  {

    $row_query = array(
      "SHOW COLUMNS FROM $table",
    );
    $row_query = implode(" ", $row_query);
    $result = $this->db->query($row_query)->result_array();
    $result = array_column($result, 'Field');

    return $result;
  }

}
