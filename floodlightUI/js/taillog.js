$(document).ready(function(){
	var interval;
	var pos = 0;
	$("#tail_btn").click(function(){
		if(0 == $("#tail_btn").attr("status"))
		{
			$("#tail_btn").addClass("inset");
			clearTimeout(interval);
			interval = setInterval(ajaxgetlog, '1000');
			$(this).attr('status', 1);
		}
	});
	
	$("#stop_btn").click(function(){
		clearTimeout(interval);
		$("#tail_btn").removeClass("inset");
		$("#tail_btn").attr("status", 0);
	});

	function ajaxgetlog()
	{
		$.ajax({
			url: '/floodlightUI/log/log_reader.php',
			data: {ajax:true, operation:'tail', pos:pos},
			type: 'get'
		}).done(function(data){
			loading = false;
			try{
				var obj = $.parseJSON(data);
				pos = obj.pos;
				$.each(obj.log_list, function(key, value){
					if(null != value)
					{
						var s_log = "<p class='log'>" + value + "</p>";
						$('#log_container').prepend(s_log);
					}
				});
				
				while($('p[class=log]').size() > 100)
				{
					$('p[class=log]:last').remove();
				}
			}catch(e){
				$('#log_container').prepend('<p class="log">There is no log!</p>');
				clearTimeout(interval);
			}
		});
	}
});