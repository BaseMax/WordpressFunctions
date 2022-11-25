<?php
// Note: This is not a optimized code, just for demo purpose with simple logic
// As this is only a script, code is not clean and optimized

$data = file_get_contents("functions.txt");

$lines = explode("\n", $data);
print_r($lines);

// Category, Tag and Taxonomy Functions

// 	Categories

// 		cat_is_ancestor_of
// 		get_all_category_ids (deprecated)
// 		get_ancestors
// 		get_cat_ID
// 		get_cat_name
// 		get_categories
// Convert the above string to a tre array:
// [
// 	"Category, Tag and Taxonomy Functions" => [
// 		"Categories" => [
// 			"cat_is_ancestor_of",
// 			"get_all_category_ids (deprecated)",
// 			"get_ancestors",
// 			"get_cat_ID",
// 			"get_cat_name",
// 			"get_categories"
// 		]
// 	]
// ]

$main_title = null;
$sub_title = null;
$item = [];
foreach ($lines as $line_item) {
	$line = trim($line_item);

	if (empty($line)) {
		continue;
	}

	if (str_starts_with($line_item, "		")) {
		$last_title = $line;
		$item[$main_title][$sub_title][$last_title] = [];
	}
	else if (str_starts_with($line_item, "	")) {
		$sub_title = $line;
		$item[$main_title][$sub_title] = [];
	}
	else {
		$main_title = $line;
		$item[$main_title] = [];
	}
}

print_r($item);

// convert the above array to a tree array with children=[]
$tree = [];
foreach ($item as $main_title => $sub_title) {
	$tree[] = [
		"name" => $main_title,
		"children" => []
	];

	foreach ($sub_title as $sub_title => $functions) {
		$item = [
			"name" => $sub_title,
			"children" => []
		];
		// append item to child of last item in $tree
		$tree[count($tree) - 1]["children"][] = $item;

		foreach ($functions as $function) {
			$item = [
				"name" => $function,
				"children" => []
			];
			// append item to child of last item in $tree
			$tree[count($tree) - 1]["children"][count($tree[count($tree) - 1]["children"]) - 1]["children"][] = $item;
		}

		// remove empty children
		if (empty($tree[count($tree) - 1]["children"][count($tree[count($tree) - 1]["children"]) - 1]["children"])) {
			unset($tree[count($tree) - 1]["children"][count($tree[count($tree) - 1]["children"]) - 1]["children"]);
		}
	}

	// remove empty children
	if (empty($tree[count($tree) - 1]["children"])) {
		unset($tree[count($tree) - 1]["children"]);
	}
}
print_r($tree);

file_put_contents("functions.json", json_encode($tree, JSON_PRETTY_PRINT));
