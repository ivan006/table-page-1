<?php
class Record_c extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		// $this->load->model('trip');
		// $this->load->library('../modules/trips/controllers/g_tbls');
		$this->load->library('g_tbls');
		$this->load->library('g_migrate');
	}

	public function index($table, $record_id)
	{
		$overview_table_singular = $this->g_migrate->grammar_singular($table);


		$header["title"] = $overview_table_singular." ".$record_id;
		$body = array();
		$body["name"]["plural"] = $table;
		$body["name"]["singular"] = $overview_table_singular;
		$record = $this->g_tbls->fetch_where($body["name"]["plural"], "id", $record_id)["posts"][0];

		$haystack = "id";
		$needle = $record_id;
		$body["data_endpoint"] = "fetch_where/h/$haystack/n/$needle";
		$body["name"]["type"] = "overview";
		$dont_scan = "";

		$body["rows"] = $this->children($body["name"], $record, $dont_scan);


		// header('Content-Type: application/json');
		// echo json_encode($body, JSON_PRETTY_PRINT);
		// exit;





		$this->load->view('table_header_v', $header);
		$this->load->view('table_block_v', $body);

		foreach ($body["rows"] as $key => $value) {
			if (!empty($value)) {
				// code...
				$this->load->view('table_block_v', $value);
			}
		}
		$this->load->view('table_footer_v');

	}


	public function suffix_remover($value, $suffix)
	{

		$suffix_strlen = strlen($suffix);
		$result = substr($value, 0, -$suffix_strlen);
		return $result;

	}

	public function children($parent_name, $parent_record, $dont_scan)
	{
		$rows = $this->g_tbls->table_rows($parent_name["plural"]);
		foreach ($rows as $key => $value) {
			if ($key !== $dont_scan) {
				if ($this->g_migrate->endsWith($key, "_id")) {

					$suffix = "_id";
					$name["singular"] = $this->suffix_remover($key, $suffix);


					$name["plural"] = $this->g_migrate->grammar_plural($name["singular"]);

					if (!empty($parent_record)) {
						$haystack = "id";
						$needle = $parent_record[$name["singular"]."_id"];
						$data_endpoint = "fetch_where/h/$haystack/n/$needle";

					} else {
						$data_endpoint = "";
					}


					$name["type"] = "parent";
					$sub_rows = $this->g_tbls->table_rows($name["plural"]);

					$rows[$key] = array(
						"name" => $name,
						"rows" => $sub_rows,
						"data_endpoint" => $data_endpoint,
					);


				} elseif ($this->g_migrate->endsWith($key, "_children")) {

					$suffix = "_children";
					$name["singular"] = $this->suffix_remover($key, $suffix);

					$name["plural"] = $this->g_migrate->grammar_plural($name["singular"]);

					if ($this->g_migrate->endsWith($name["plural"], "_links")) {


						if (!empty($parent_record)) {
							$haystack = $parent_name["singular"]."_id";
							$needle = $parent_record["id"];
							$data_endpoint = "fetch_where/h/$haystack/n/$needle";

						} else {
							$data_endpoint = "";
						}

						$name["type"] = "shared_children";
						$sub_rows = $this->g_tbls->table_rows($name["plural"]);

						$record = array();
						$dont_scan = $parent_name["singular"]."_id";

						$sub_rows = $this->children($name, $record, $dont_scan);




						$lookup_table = array();
						$rel_rows = array();
						foreach ($sub_rows as $sub_row_key => $sub_row_value) {
							if (!empty($sub_row_value)) {
								$rel_rows[] = $sub_row_value;
							}
						}
						if (!empty($rel_rows)) {
							$lookup_table = $rel_rows[0];
						}

						$join["lookup"] = $lookup_table;
						$join_merge = array_merge(array_keys($lookup_table["rows"]), array_keys($sub_rows));
						$join_merge = array_unique($join_merge);
						$join_merge = array_flip($join_merge);
						foreach ($join_merge as $join_merge_key => $join_merge_value) {
							$join_merge[$join_merge_key] = array();
						}

						// $join["join"] = $join_merge;
						$join["data_endpoint"] = "fetch_join_where/t/".$lookup_table["name"]["plural"];
						$join["rows"] = $join_merge;
						$join["name"] = $lookup_table["name"];

						$rows[$key] = array(
							"name" => $name,
							"rows" => $sub_rows,
							"data_endpoint" => $data_endpoint,
							"join" => $join,
						);





					} else {

						if (!empty($parent_record)) {
							$haystack = $parent_name["singular"]."_id";
							$needle = $record_id;

							$data = "fetch_where/h/$haystack/n/$needle";

						} else {
							$data_endpoint = "";
						}
						$name["type"] = "dedicated_children";
						$sub_rows = $this->g_tbls->table_rows($name["plural"]);



						$rows[$key] = array(
						"name" => $name,
						"rows" => $sub_rows,
						"data_endpoint" => $data_endpoint,
						);

					}
				}
			} else {
				$rows[$key] = array();
			}
		}

		return $rows;

	}


}
