<?php
	require('lib/globals.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title><?= Utilities::FindKey('SiteTitle', $Config); ?> - Documentation</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-2"></div>
			<div class="col-lg-8">
				<div class="page-header">
				  <h1><?= Utilities::FindKey('SiteTitle', $Config); ?> <small>Documentation</small></h1>
				</div>
				<nav class="navbar navbar-default">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#nozframework-navbar-collapse" aria-expanded="false">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
					</div>
					<div class="collapse navbar-collapse" id="nozframework-navbar-collapse">
						<ul class="nav navbar-nav navbar-left">
							<li><a href="#">Getting Started</a></li>
							<li><a href="#">Modules</a></li>
							<li><a href="#">Functions</a></li>
							<li><a href="#">Configuration</a></li>
							<li><a href="#">About</a></li>
						</ul>
						<ul class="nav navbar-nav navbar-right">
							<li><a href="#">Report Bugs</a></li>
							<li><a href="#">Help</a></li>						
						</ul>
					</div>
				</nav>
				<div class="well well-sm">
					So what is this good for? Well, mostly I make this for my own projects, and I will develop this as I go.
					Again (in other words) this is mostly to make things easier for my self, but I choose to share the code
					because I am a nice guy. The question whether this is of use or not, that I will leave with you.
				</div>
				<hr>
				<div class="panel panel-primary">
					<div class="panel-heading">
						NozFramework Functions
					</div>
					<div class="panel-body">
						Below is a description and how to use each function of the framework.<hr>
						<div class="panel panel-default">
							<div class="panel-heading">
								Config Loader Module
							</div>
							<div class="panel-body">
								This is the config loader module that I made. This is optional to use,
								you can just remove the class file from the modules folder in the lib folder.
								Otherwise, this will autoload every config file that you place in Config folder
								in the website root folder. Though they need to end with .conf.json. Also the JSON
								format needs to be correct in order for the function to load the config.
							</div>
							<div class="table-responsive">
								<table class="table table-striped">
									<thead>
										<tr>
											<th width="15%">Function</th>
											<th width="20%">Arguments</th>
											<th width="40%">Description</th>
											<th width="25%">Example</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>Config::C()</td>
											<td>N/A</td>
											<td>This is the main function, this is how you load all the config
											variables into a php array.</td>
											<td>$Config = Config::C();</td>
										</tr>
										<tr>
											<td>Config::cKey()</td>
											<td>String <strong>KeyName</strong>, Array <strong>ContainingKeyName</strong></td>
											<td>This function will get the variable that the <strong>KeyName</strong> is holding.
											This is useful when you know the key is in the array, but do not want to "navigate" all
											the way through the array manually. <strong>This will return a string or array</strong>.</td>
											<td>Config::cKey('SiteTitle', Config::C());</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<hr>
				<?php
					$result = SQL::Query(2, 'SELECT `usernae` FROM `test` LIMIT 10');
					if($result['Success'] === false) {						
						if(class_exists('MessageHandler')) {
							new MessageHandler($result['Result'], 'ERR');
						} else {
							echo $result['Result'];
						}
					}
				?>
			</div>
			<div class="col-lg-2"></div>
		</div>
	</div>
</body>
</html>