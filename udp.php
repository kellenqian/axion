<?php
class UDP {
	public $port;
	public $host;
	private $socket;
	
	public function __construct($host, $port) {
		$this->port = $port;
		$this->host = $host;
		$this->socket = socket_create ( AF_INET, SOCK_DGRAM, SOL_UDP );
		$this->setTimeout ( 15 );
	}
	
	public function send($data) {
		socket_sendto ( $this->socket, $data, strlen ( $data ), 0, $this->host, $this->port );
	}
	
	public function read($length) {
		$buffer = false;
		@socket_recvfrom ( $this->socket, $buffer, $length, 0, $this->host, $this->port );
		return $buffer;
	}
	
	public function setTimeout($timeout) {
		$param = array ('sec' => $timeout, 'usec' => 0 );
		socket_set_option ( $this->socket, SOL_SOCKET, SO_RCVTIMEO, $param );
	}
	
	public function pack($format, $data) {
		if ($format == '') {
			return false;
		}
		$param [0] = $format;
		if (is_array ( $data )) {
			$param = array_merge ( $param, $data );
		} else {
			$param = array_merge ( $param, explode ( ',', $data ) );
		}
		
		return call_user_func_array ( 'pack', $param );
	}
}

function usage() {
	echo "Address : Destnation Host Address\n";
	echo "Port : Destnation Host Port\n";
	echo "maxBandwidth : Max Data Send Per Second(s)\n";
	echo "minBandwidth : Min Data Send Per Second(s)\n";
	echo "-silent : Silent Mode. Don`t Print Any Debug Info\n";
	echo "Example: udp Host Port [maxBandwidth] [minBandwidth] [-silent]\n";
	exit ();
}

define ( 'PACKAGE_SIZE', 4096 );

$args = $_SERVER ['argv'];

array_shift ( $args );

if (empty ( $args )) {
	usage ();
}

if ($args [0] == '-h' || $args [0] == '--help') {
	usage ();
}

$ip = $args [0];

$port = $args [1];

$maxBandwidth = $args [2];

$minBandwidth = $args [3];

$silent = $args [4] == '-silent' ? true : false;

if (empty ( $maxBandwidth )) {
	$maxBandwidth = 300; //k
}

if (empty ( $minBandwidth )) {
	$minBandwidth = 10; //k
}

$maxBandwidth = $maxBandwidth * 1024;
$minBandwidth = $minBandwidth * 1024;

if (empty ( $ip )) {
	die ( 'no address input' );
}

if (empty ( $port )) {
	die ( 'no port input' );
}

if (( int ) $port == 0) {
	die ( 'wrong port format' );
}

$udp = new udp ( $ip, $port );

$data = substr ( str_repeat ( "1,", PACKAGE_SIZE ), 0, - 1 );
$format = str_repeat ( "C", PACKAGE_SIZE );

$binaryData = $udp->pack ( $format, $data );

$sentCount = 0;

$start = time ();

$time = 0;

echo "Start Send Date To : $ip:$port\n";

while ( true ) {
	if (time () - $start == 1) {
		$randSize = rand ( $maxBandwidth, $minBandwidth );
		$count = ceil ( $randSize / PACKAGE_SIZE );
		for($i = 0; $i < $count; $i ++) {
			$udp->send ( $binaryData );
		}
		$sentCount += $randSize;
		$start ++;
		
		$Kbyte = number_format ( $sentCount / 1024, 2 );
		$Kbps = number_format ( $randSize * 8 / 1024 );
		if (! $silent) {
			echo "total sent $Kbyte Kb\n";
			echo "time last : " . $time ++ . "\n";
			echo "bps : " . $Kbps . "Kbps\n";
		}
		usleep ( 997 * 1000 );
	}
}
?>