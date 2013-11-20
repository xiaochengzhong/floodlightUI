floodlightUI
============
miniNet生成深度为4的二叉树拓扑命令（7switch，8host，1controller）：
sudo mn --topo tree,depth=3,fanout=2 --controller=remote --ip=xxx.xxx.xxx.xxx --port=6633

添加流表
curl -d '{"switch": "00:00:00:00:00:00:00:09", "name":"flow-mod-1", "cookie":"0", "priority":"32768", "ingress-port":"1","active":"true", "actions":"output=2"}' http://localhost:8080/wm/staticflowentrypusher/json


删除流表
curl -X DELETE -d '{"name":"flow-mod-1"}' http://localhost:8080/wm/staticflowentrypusher/json

