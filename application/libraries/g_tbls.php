<?php
class G_tbls extends MY_Controller
{

	function __construct()
	{
		parent::__construct();

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->library('g_migrate');
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
					if ($key !== "id") {
						$data[$key] = $this->input->post('edit_'.$key);
					}
				}

	      if ($this->db->update($table, $data, array('id' => $data['id']))) {
	        $data = array('responce' => 'success', 'message' => 'Record update Successfully');
	        // $data = $this->db->last_query();
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
    $rows = $this->db->query($row_query)->result_array();
    $rows = array_column($rows, 'Field');

		$result = array();
		foreach ($rows as $key => $value) {
			$result[$value] = array();
		}

    return $result;
  }

  public function fetch_where($table, $haystack, $needle)
  {
		$posts = $this->db->where($haystack, $needle)->get($table)->result_array();
    $data = array('responce' => 'success', 'posts' => $posts);
    return $data;
  }

  public function fetch_join_where($table_1, $table_2, $haystack,$needle)
  {

		// $posts = $this->db->select('*')->where($haystack, $needle)->from($table_1)->join($table_2, "$table_1.$table_2_key = $table_2.id")->get()->result_array();
		$table_2_singular = $this->g_migrate->grammar_singular($table_2);
		$table_2_singular = $table_2_singular."_id";
		// $table_1_singular = $this->g_migrate->grammar_singular($table_1);
		// $haystack = $table_1_singular.".".$haystack;


		$posts = $this->db->select('*')->where($haystack, $needle)->from($table_2)->join($table_1, "$table_1.$table_2_singular = $table_2.id", "right")->get()->result_array();

		$data = array('responce' => 'success', 'posts' => $posts);
    return $data;

  }

  public function mergetest()
  {





		// $posts = $this->db
		// ->select("`event_resource_links`.*")
    // ->select("DAY() AS wp_name")
		// ->from("event_resource_links")
		// ->join("resources", "event_resource_links.bedding_specialty_resource_id = resources.id", "right")
		// ->get()
		// ->result_array();


		$sql="Select 'aaa' as asd";
		// $sql="SELECT '' as table1_dummy, table1.*, '' as table2_dummy, table2.*, '' as table3_dummy, table3.* FROM table1 JOIN table2 ON table2.table1id = table1.id JOIN table3 ON table3.table1id = table1.id";
		$posts = $this->db->query($sql)->result_array();

		$data = array('responce' => 'success', 'posts' => $posts);
    return $data;

  }

  public function database_api()
  {

    $row_query = array(
      "SHOW TABLES",
    );
    $row_query = implode(" ", $row_query);
    $rows = $this->db->query($row_query)->result_array();
    $rows = array_column($rows, 'Tables_in_greenbluegpyuty_db5');
		foreach ($rows as $key => $value) {
			$rows_formatted[]["name"] = $value;
		}

		// $result = array();
		foreach ($rows_formatted as $key => $value) {
			if (!$this->g_migrate->endsWith($value["name"], "_links")) {
				$rows_no_links[]["name"] = $value["name"];
			}
		}

		$data = array('responce' => 'success', 'posts' => $rows_no_links);
		return $data;
  }

}
