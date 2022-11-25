<?php
// Max Base
// https://github.com/BaseMax/WordpressFunctions
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

file_put_contents("functions-list.txt", json_encode($item, JSON_PRETTY_PRINT));

// convert the above array to a tree array with children=[]
$tree = [];
foreach ($item as $main_title => $sub_title) {
	$tree[$main_title] = [];
	foreach ($sub_title as $sub_title => $item) {
		$tree[$main_title][] = [
			"name" => $sub_title,
			"children" => []
		];
		foreach ($item as $item => $value) {
			$tree[$main_title][count($tree[$main_title]) - 1]["children"][] = [
				"name" => $item,
			];
		}
		// remove empty children
		if (empty($tree[$main_title][count($tree[$main_title]) - 1]["children"])) {
			unset($tree[$main_title][count($tree[$main_title]) - 1]["children"]);
		}
	}
	// remove empty children
	if (empty($tree[$main_title])) {
		unset($tree[$main_title]);
	}
}
print_r($tree);

file_put_contents("functions.json", json_encode($tree, JSON_PRETTY_PRINT));

// convert the tree to markdown
$markdown = "# WordPress Functions List\n";
foreach ($tree as $main_title => $sub_title) {
	$markdown .= "\n## $main_title\n";
	foreach ($sub_title as $sub_title) {
		if (isset($sub_title["children"])) {
			$markdown .= "\n### $sub_title[name]\n\n";
			foreach ($sub_title["children"] as $item) {
				$markdown .= "- $item[name]\n";
			}
		} else {
			$markdown .= "- $sub_title[name]\n";
		}
	}
}
file_put_contents("functions.md", $markdown);

$data = file_get_contents("README.backup");
$data = str_replace("<!-- functions-list -->", $markdown, $data);
file_put_contents("README.md", $data);
