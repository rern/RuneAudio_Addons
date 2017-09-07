<?php
$changelog = '';
$file = fopen('changelog.md', 'r');
while(!feof($file)) {
	$line = preg_replace('/\n/', '', fgets($file));

	if (strpos($line, '- ') === 0) {
		$changelog .= preg_replace('/^- /', '<li>', $line)."</li>\n";
	} else if (strlen(trim($line)) !== 0) {
		$changelog .= "</ul>\n<span>".str_replace('**', '', $line)."</span>\n<ul>\n";
	}
}
$changelog = preg_replace('/<\/ul>/', '', $changelog, 1);
$changelog = preg_replace('/<\/span>/', '<a id="detail"> &nbsp; Detail &#x25BC</a></span>', $changelog, 1);
echo $changelog.'</ul>';
