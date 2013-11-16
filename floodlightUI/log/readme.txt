log文件夹放在floodlightUI下
taillog.css放在css下
taillog.js放在js下

修改：
log/log_reader.php方法getLogPath()，把返回改为具体log路径
tpl/header.html log标签改为<li><a href="" onclick="location.href='/floodlightUI/log/log.php';return;">log</a></li>