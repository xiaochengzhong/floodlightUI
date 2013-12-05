<html>
<head>
<link href="../css/bootstrap.css" rel="stylesheet" />
<link href="../css/styles.css" rel="stylesheet" />
<link href="../css/taillog.css" rel="stylesheet" />
<script src="../lib/jquery.min.js"></script>
</head>
</body>
<!-- header starts -->
<div class="header">
	<div>
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<div class="nav-collapse">
						<ul class="nav">
							<li><a href="/floodlightUI/index.html">Dashboard</a></li>
							<li><a href="/floodlightUI/topology/topology.php">Topology</a></li>
							<li><a href="/floodlightUI/index.html#switches">Switches</a></li>
							<li><a href="/floodlightUI/index.html#hosts">Hosts</a></li>
							<li class="active"><a href="" onclick="location.href='/floodlightUI/log/log.php';return;">log</a></li>
						</ul>
					</div>
					<form class="navbar-form pull-right">
						<label class="checkbox" style="display: none">
							<input type="checkbox" id="live-updates" checked="yes">Live updates
						</label>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- header ends -->

<!-- container starts -->
<div class="content">

<div id="content"><div><div class="row">
<div class="span12">
<div class="page-header">
    <h1>Log Info</h1>
	<input type="button" id="tail_btn" class="btn" value="Tail" status=0 />
	<input type="button" id="stop_btn" class="btn" value="Stop" />
</div>
<div id="log_container"></div>
</div>
</div>
</div></div>

<hr>
<footer class="footer">
</footer>

</div>
<!-- container ends -->
<script src="../js/taillog.js"></script>
</body>
</html>