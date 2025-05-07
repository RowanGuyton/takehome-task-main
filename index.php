<?php

// TODO A: Improve the readability of this file through refactoring and documentation.
// TODO B: Review the HTML structure and make sure that it is valid and contains
// required elements. Edit and re-organize the HTML as needed.
// TODO C: Review the index.php entrypoint for security and performance concerns
// and provide fixes. Note any issues you don't have time to fix.
// TODO D: The list of available articles is hardcoded. Add code to get a
// dynamically generated list.
// TODO E: Are there performance problems with the word count function? How
// could you optimize this to perform well with large amounts of data? Code
// comments / psuedo-code welcome.
// TODO F (optional): Implement a unit test that operates on part of App.php

use App\App;

require_once __DIR__ . '/vendor/autoload.php';

// Get word count for current article
function wfGetWc()
{
	global $wgBaseArticlePath;
	$wgBaseArticlePath = 'articles/';
	$wc = 0;
	$dir = new DirectoryIterator($wgBaseArticlePath);
	foreach ($dir as $fileinfo) {
		if ($fileinfo->isDot()) {
			continue;
		}
		$c = file_get_contents($wgBaseArticlePath . $fileinfo->getFilename());
		$ch = explode(" ", $c);
		$wc += count($ch);
	}
	return "$wc words written";
}

// Instantiate our app class
$app = new App();

// Retrieve all current articles
$articles = $app->getCurrentArticles();

// Set necessary variables
$title = '';
$body = '';
if (isset($_GET['title'])) {
	$title = htmlentities($_GET['title']);
	$body = $app->fetchArticle($_GET);
	$body = file_get_contents(sprintf('articles/%s', $title));
}

$wordCount = wfGetWc();

?>

<head>
	<link rel='stylesheet' href='http://design.wikimedia.org/style-guide/css/build/wmui-style-guide.min.css'>
	<link rel='stylesheet' href='styles.css'>
	<script src='main.js'></script>
</head>

<body>
	<div id=header class=header>
		<a href='/'>Article editor</a>
		<div><?php htmlspecialchars($wordCount) ?></div>
	</div>
	<div class='page'>
		<div class='main'>
			<h2>Create/Edit Article</h2>
			<p>Create a new article by filling out the fields below. Edit an article by typing the beginning of the title in the title field, selecting the title from the auto-complete list, and changing the text in the textfield.</p>
			<form action='index.php' method='post'>
				<input name='title' type='text' placeholder='Article title...' value=<?php htmlspecialchars($title) ?>>
				<br />
				<textarea name='body' placeholder='Article body...'><?php htmlspecialchars($body) ?></textarea>
				<br />
				<a class='submit-button' href='#' />Submit</a>
				<br />
				<h2>Preview</h2>
				<?php htmlspecialchars($title) ?>
				<?php htmlspecialchars($body) ?>
				<h2>Articles</h2>
				<ul>

					<?php foreach ($articles as $article): ?>
						<li>
							<a href="index.php?title=<?= urlencode($article) ?>">
								<?= htmlspecialchars($article) ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</form>

			<?php if ($_POST) {
				$app->saveArticle(sprintf("articles/%s", $_POST['title']), $_POST['body']);
			}
			?>
		</div>
	</div>
</body>