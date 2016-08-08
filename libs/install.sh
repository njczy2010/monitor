#!/bin/sh
set -e
#
# Usage:
# curl -sSL http://get.hehecloud.com/install.sh | sudo -H sh -s [HeheToken]
#



if [ -z "$1" ] || [ -z "$2" ]
then
  echo 'Error: you should install hehe-agent with token, like:'
  echo 'curl -sSL http://get.hehecloud.com/yunlian/install.sh | sudo -H sh -s [Token] [Port]'
  exit 0
fi

DEIS_RELEASE="v1.12.2"
SUPPORT_URL='http://hehecloud.com/'
arg_token=$1
arg_port=$2
# url='http://get.hehecloud.com'
url='http://cdn.sinacloud.net/hehe'
API_URL='http://api.juhe.baishancloud.com'

check_token() {
    expr $arg_port + 0 > /dev/null 2>&1 || (echo "Error: Port validation failed" && exit 1);
    if [ $arg_port -lt 0 ] || [ $arg_port -gt 65535 ]
    then
    	echo 'Error: Port validation failed'
        exit 1
    fi
    curl -H "Authorization: HeheAgentToken ${arg_token}" "${API_URL}/agent/" | grep "api_path_id" > /dev/null 2>&1 || (echo "Error: Token validation failed" && exit 1);
    # rm -rf /etc/hehe
}

check_token

command_exists() {
	command -v "$@" > /dev/null 2>&1
}

UNAME=`uname`
if [[ ($UNAME == *"mac os x"*) || ($UNAME == *darwin*) || ($UNAME == *Darwin*) ]]; then
    PLATFORM="darwin"
elif [[ ($UNAME == *"freebsd"*) ]]; then
    PLATFORM="freebsd"
else
    PLATFORM="linux"
fi

if [ -f "/etc/hehe/agent/hehe-agent.conf" ]; then
    cat <<EOF
ERROR: Yunlian Agent is already installed in this host
EOF
    if [ "$PLATFORM" == "darwin" ]; then
        cat <<EOF
Try: 'sudo /opt/bin/hehe-agent -stdout'
EOF
    else
        cat <<EOF
Try: 'sudo service yunlian-agent restart'
 Or: 'sudo systemctl restart yunlian-agent.service'
EOF
    fi
	exit 1
fi

if [ "$(uname -m)" != "x86_64" ]; then
	cat <<EOF
ERROR: Unsupported architecture: $(uname -m)
Only x86_64 architectures are supported at this time
Learn more: $SUPPORT_URL
EOF
	exit 1
fi

case "$(uname -m)" in
	*64)
		;;
	*)
		echo >&2 'Error: you are not using a 64bit platform.'
		echo >&2 'yunlian-agent currently only supports 64bit platforms.'
		exit 1
		;;
esac


user="$(id -un 2>/dev/null || true)"

sh_c='sh -c'
if [ "$user" != 'root' ]; then
	if command_exists sudo; then
		sh_c='sudo -E sh -c'
	elif command_exists su; then
		sh_c='su -c'
	else
		echo >&2 'Error: this installer needs the ability to run commands as root.'
		echo >&2 'We are unable to find either "sudo" or "su" available to make this happen.'
	fi
fi

curl=''
if command_exists curl; then
	curl='curl --retry 20 --retry-delay 5 -L'
else
	echo >&2 'Error: this installer needs curl. You should install curl first.'
	exit 1
fi

# download_flanneld () {
#   mkdir -p /opt/bin && \
#   wget -N -P /opt/bin ${url}/flannel/flanneld && \
#   wget -N -P /opt/bin ${url}/flannel/mk-docker-opts.sh && \
#   chmod +x /opt/bin/flanneld && \
#   chmod +x /opt/bin/mk-docker-opts.sh
# }
#
# download_flanneld

# check_hehe_agent () {
# 	if ps ax | grep -v grep | grep "hehe-agent" > /dev/null
# 	then
# 	    echo "hehe-agent.service already running."
# 	    if command_exists systemctl; then
#         echo "stopping hehe-agent.service ..."
# 	    	$sh_c "systemctl stop hehe-agent.service"
# 	    fi
# 	fi
# }
#
# check_hehe_agent

# HEHE_REGISTRY=daocloud.io/cloudmario
# # HEHE_REGISTRY=registry.hehecloud.com:5000/deis
#
# hehe_images_preseed_alpine () {
#   docker history alpine:3.2 >/dev/null 2>&1 || docker pull daocloud.io/cloudmario/alpine:3.2
# }
#
# hehe_images_preseed () {
#   echo "Pulling images..."
#   set +e
#   hehe_images_preseed_alpine
#   COMPONENTS=(builder controller database logger logspout publisher registry router store-daemon store-gateway store-metadata store-monitor)
#   for c in "${COMPONENTS[@]}"; do
#     image=${HEHE_REGISTRY}/${c}:${DEIS_RELEASE}
#     docker history $image >/dev/null 2>&1 || docker pull $image
#   done
#   set -e
# }

# hehe_images_preseed
# hehe_images_preseed_alpine

# perform some very rudimentary platform detection
get_distribution_type() {
	local lsb_dist
	lsb_dist="$(lsb_release -si 2> /dev/null || echo "unknown")"
	if [ "$lsb_dist" = "unknown" ]; then
		if [ -r /etc/lsb-release ]; then
			lsb_dist="$(. /etc/lsb-release && echo "$DISTRIB_ID")"
		elif [ -r /etc/debian_version ]; then
			lsb_dist='debian'
		elif [ -r /etc/fedora-release ]; then
			lsb_dist='fedora'
		elif [ -r /etc/centos-release ]; then
			lsb_dist='centos'
		elif [ -r /etc/os-release ]; then
			lsb_dist="$(. /etc/os-release && echo "$ID")"
		fi
	fi
	lsb_dist="$(echo "$lsb_dist" | tr '[:upper:]' '[:lower:]')"
	echo $lsb_dist
}

UNAME=$(uname -sm | awk '{print tolower($0)}')


echo " * Installing yunlian-agent..."
echo " * Downloading yunlian-agent..."
$sh_c "mkdir -p /opt/bin"
$sh_c "$curl -o /opt/bin/hehe-agent  ${url}/yunlian-agent-${PLATFORM}"
$sh_c "chmod +x /opt/bin/hehe-agent"

echo " * Configuring yunlian-agent..."
$sh_c "/opt/bin/hehe-agent set HeheToken=\"${arg_token}\""
$sh_c "/opt/bin/hehe-agent set TunnelPort=\"${arg_port}\""

if [ "$PLATFORM" == "darwin" ]
then
	$sh_c "/opt/bin/hehe-agent -stdout"
fi


case "$(get_distribution_type)" in
    fedora|centos|amzn|ubuntu|debian)
        (
            $sh_c "mkdir -p /etc/systemd/system"
            $sh_c "mkdir -p /etc/init.d"
            $sh_c "$curl -o /etc/systemd/system/yunlian-agent.service ${url}/yunlian/yunlian-agent.service"
            $sh_c "$curl -o /etc/init.d/yunlian-agent ${url}/yunlian/yunlian-agent"
            $sh_c "chmod +x /etc/init.d/yunlian-agent"
            $sh_c "service yunlian-agent start"
            echo "-> Done!"
            cat <<EOF

***********************************************
Yunlian Agent installed successfully
***********************************************

EOF
        )
		exit 0
		;;
	coreos)
		(
            $sh_c "mkdir -p /etc/systemd/system"
			$sh_c "$curl -o /etc/systemd/system/yunlian-agent.service ${url}/yunlian/yunlian-agent.service"
			$sh_c "systemctl enable /etc/systemd/system/yunlian-agent.service"
			$sh_c "systemctl start yunlian-agent.service"

            echo "-> Done!"
            cat <<EOF

***********************************************
Yunlian Agent installed successfully
***********************************************

EOF
		)
		exit 0
		;;
  *)
    echo "ERROR: Cannot detect Linux distribution or it's unsupported"
  	exit 1
  	;;
esac
