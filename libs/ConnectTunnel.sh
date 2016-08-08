ps -A | grep hehe
if [ $? -eq 0 ]
then
	echo tunnel is running
else
	echo tunnel is not running, connecting
	curl -sSL http://get.hehecloud.com/yunlian/install.sh | sudo sh -s 177bfe50-3-5772600f-478-c001d8abb5 80
fi

