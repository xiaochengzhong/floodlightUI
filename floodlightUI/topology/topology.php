<!-- 
    @author lvxiang
    @email 1992donkey@gmail.com
-->
<html>
<head>
<link href="../css/bootstrap.css" rel="stylesheet" />
<link href="../css/styles.css" rel="stylesheet" />
<link href="../css/taillog.css" rel="stylesheet" />
<style type="text/css">
#main_content{
    position: absolute;
    width: 900px;
    height: 400px;
    left: 50%;
    top: 100px;
    margin-left: -450px;
    user-select: none;
    -moz-user-select: none;
    -webkit-user-select: none;
}
#control_panel{
    position: absolute;
    width: 200px;
    height: 80px;
    background-color: #303030;
    opacity: 0.8;
    left: 10px;
    top: 10px;
    border-radius: 5px;
    color: #f0f0f0;
    padding-left: 5px;
    overflow: auto;
}
#node_prompt{
    position: absolute;
    display: none;
    border-radius: 3px;
    box-shadow: 0px 0px 3px #808080;
    z-index: 100;
    background-color: #ffffff;
    opacity: 0.8; 
}
</style>
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
							<li class="active"><a href="" onclick="location.href='/floodlightUI/topology/topology.php';return;">Topology</a></li>
							<li><a href="/floodlightUI/index.html#switches">Switches</a></li>
							<li><a href="/floodlightUI/index.html#hosts">Hosts</a></li>
							<li><a href="/floodlightUI/log/log.php">log</a></li>
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
    <div class="row">
        <div class="page-header">
        <h1>Topology</h1>
    </div>
</div>
<div id="main_content">
    <canvas id="topology_graph" width="900px" height="400px">
    Sorry, Your Browser Does Not Support HTML5 Canvas!
    </canvas>
    <div id="control_panel">
        <h5>Control Panel</h5>
        <label class="checkbox">
            <input type="checkbox"/>Show Static Flows
        </label>
    </div>
    <div id="node_prompt">
        <div id="slot1"></div>
        <div id="slot2"></div>
        <div id="slot3"></div>
    </div>

    <hr>
</div>
<!-- container ends -->
<script src="../lib/jquery.min.js"></script>
<script src="../js/topology.js"></script>
<script type="text/javascript">
var canvas, context, width, height, raw_x, raw_y, origin_x, origin_y, down_x, down_y, canvas_x, canvas_y;
var b_width = 20, b_height = 20, n_width = 40, n_height = 40;
var dragging = false;
var switches, hosts, links, flows;
var nodesmap = {}; // map from mac/dpid to each node
var root = null; // root of generated tree
var switchImg = new Image();
switchImg.src = "../img/switch.png";
var hostImg = new Image();
hostImg.src = "../img/server.png";


$(document).ready(function(){
    canvas  = document.getElementById("topology_graph");
    canvas_x = $('#topology_graph').offset().left;
    canvas_y = $('#topology_graph').offset().top;
    context = canvas.getContext('2d');
    width   = 900;
    height  = 400;

    // the middle point of canvas is set as the origin, that is (450, 250)
    // draw a matrix in 20 by 20 grid
    raw_x = 450;
    raw_y = 200;
    origin_x = raw_x;
    origin_y = raw_y;

    $('#topology_graph').mousedown(function(event){
    	dragging = true;
    	down_x = event.clientX - canvas_x;
    	down_y = event.clientY - canvas_y;
    });

    $('#topology_graph').mousemove(function(event){
    	if(dragging){
    		cur_x = event.clientX - canvas_x;
    		cur_y = event.clientY - canvas_y;
    		delta_x = cur_x - down_x;
    		delta_y = cur_y - down_y;
    		down_x = cur_x;
    		down_y = cur_y;
    		origin_x += delta_x;
    		origin_y += delta_y;
            clearCanvas();
    		drawMatrix();
    		drawTree();
    	}else{
            // see if mouse points to a node
            cur_x = event.clientX - origin_x - canvas_x;
            cur_y = event.clientY - origin_y - canvas_y;
            // find a node within range of which the cursor points
            result = searchBFS(root, cur_x, cur_y);
            if(result){
                // show prompt;
                if(result.type == 0){
                    $('#slot1').html('DPID:' + result.mac);
                    $('#slot2').html('IP Address:' + result.ip);
                    $('#slot3').html('Connected Since:' + new Date(result.ts));
                }else if(result.type == 1){
                    $('#slot1').html('MAC:' + result.mac);
                    $('#slot2').html('Switch Port:' + result.parent.mac + '-' + result.attachPort);
                    $('#slot3').html('Last Seen:' + new Date(result.ts));
                }
                $('#node_prompt').css('left', event.clientX - canvas_x + 15);
                $('#node_prompt').css('top',  event.clientY - canvas_y);
                $('#node_prompt').fadeIn('fast');
            }else{
                // dismiss prompt;
                $('#node_prompt').fadeOut('fast');
            }
        }
    });

    $('#topology_graph').mouseup(function(){
    	dragging = false;
    });

    $('#topology_graph').mouseout(function(){
    	dragging = false;
    });

    // get data and initialize
    getData();
});   

// search for a node using BFS
function searchBFS(node, x, y){
    if(x > node.x - b_width/ 2 && x <= node.x + b_width/2 &&
       y > node.y - b_height/2 && y <= node.y + b_height/2)
        return node;
    for(var i = 0; i < node.children.length; i ++){
        result = searchBFS(node.children[i], x, y);
        if(result) return result;
    }
    return null;
}

// get data throught ajax
function getData(){
	$.ajax({
		url: 'http://localhost/index.php/wm/core/controller/switches/json',
		type: 'get',
		dataType: 'json'
	}).success(function(data){
		switches = data;
		$.ajax({
			url: 'http://localhost/index.php/wm/device/',
			type: 'get',
			dataType: 'json'
		}).success(function(data){
			hosts = data;
            $.ajax({
                url: 'http://localhost/index.php/wm/topology/links/json',
                type: 'get',
                dataType: 'json'
            }).success(function(data){
                links = data;
                prepareData();
                drawMatrix();
                drawTree();    
            });
	        
		});
	});
}

// prepare data for drawing
function prepareData(){
    if(switches){
        // alert(switches.length);
        for(var i = 0; i < switches.length; i ++){
            var node = new Node(switches[i].dpid, switches[i].inetAddress, 0, switches[i].connectedSince);
            nodesmap[node.mac] = node;
            if(i == 0) root = node; 
        }

        if(links){
            for(var i = 0; i < links.length; i ++){
                var link = links[i];
                console.log(link['src-switch'] + ',' + link['dst-switch']);
                var src = nodesmap[link['src-switch']];
                var dst = nodesmap[link['dst-switch']];
                src.addChild(dst);
                dst.parent = src;
            }
        }

        if(hosts){
            for(var i = 0; i < hosts.length; i ++){
                var host = hosts[i];
                var node = new Node(host.mac, "", 1, host.lastSeen);
                nodesmap[node.mac] = node;
                if(host.attachmentPoint.length > 0){
                    var length = host.attachmentPoint.length;
                    node.parent = nodesmap[host.attachmentPoint[length - 1].switchDPID];
                    node.attachPort = host.attachmentPoint[length - 1].port;
                    node.parent.addChild(node);
                }
            }
        }

        if(root){
            while(root.parent)
                root = root.parent;
            root.calcWidth(); // calculate width recursively
            root.setPosition(); // set position recursively
        }
    }
}

// define Node object
function Node(mac, ip, type, timestamp){
    
    var self = this;

    self.x = 0;
    self.y = 0;
    self.mac  = mac; // mac address
    self.ip   = ip;  // ip address
    self.type = type; // type, 0 for switch, 1 for host
    self.parent = null;
    self.attachPort = null;
    self.children = new Array();
    self.level = 0;
    self.width = 0;
    self.ts = timestamp;

    self.addChild = function(node){
        self.children.push(node);
    }

    // calculate width needed recursively
    self.calcWidth = function(){
        if(self.children.length == 0){
            self.width = n_width;
            return self.width;
        }
        for(var i = 0; i < self.children.length; i ++){
            self.width += self.children[i].calcWidth();
        }
        return self.width;
    };

    // set position recursively, position are measured relative to 
    // the origin x and y
    self.setPosition = function(){
        if(self.parent){
            // not the root node
            self.x = self.parent.x - self.parent.width / 2 + self.parent.offsetOf(self) + self.width / 2;
            self.y = self.parent.y + b_height * 3;
        }
        else{
            // is the root node
            self.x = 0;
            self.y = -80;
        }
        // set position of children
        for(var i = 0; i < self.children.length; i ++) self.children[i].setPosition();
    };

    // find the offsetX of a child node
    self.offsetOf = function(node){
        var index = self.children.indexOf(node);
        var offset = 0;
        for(var i = 0; i < index; i ++)
            offset += self.children[i].width;
        return offset;
    }
}

function clearCanvas(){
    canvas.width = canvas.width;
}

// draw the matrix based on current origin
function drawMatrix(){
    context.fillStyle = '#f0f0f0';

    // draw horizontal lines
    var temp = origin_y;
    if(origin_y >= 0) while((temp -= b_height) >= 0);
    else while((temp += b_height) < 0);
    if(temp < 0) temp += b_height;
    while(temp <= height){
        context.fillRect(0, temp, width, 3);
        temp += b_height;
    }

    // draw vertical lines
    temp = origin_x;
    if(origin_x >=0) while((temp -= b_width) >=0);
    else while((temp += b_width) < 0);
    if(temp < 0) temp += b_width;
    while(temp <= width){
        context.fillRect(temp, 0, 3, height);
        temp += b_width;
    }
}

function drawTree(){
    if(root)
        drawBFS(root);
}

// draw the tree in a breadth first manner
function drawBFS(node){
    context.fillStyle = '#808080';
    var realX = node.x + origin_x;
    var realY = node.y + origin_y;
    context.fillRect(realX - 8, realY - 8, 20, 20);
    if(node.type == 0)
        context.drawImage(switchImg, realX - 8, realY - 8, 20, 20);
    else if(node.type == 1)
        context.drawImage(hostImg, realX - 8, realY - 8, 20, 20);
    // draw childing
    for(var i = 0; i < node.children.length; i ++){
        // draw line connecting current node and its children
        context.lineWidth = 2;
        context.strokeStyle = "#303030";
        var child = node.children[i];
        context.moveTo(realX + 1, realY + 12);
        context.lineTo(origin_x + child.x + 1, origin_y + child.y - 8);
        context.stroke();
        drawBFS(child);
    }
}
</script>
</body>
</html>