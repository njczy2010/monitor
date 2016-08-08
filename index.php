<?php
//test12345
$root = dirname(__file__);

require_once("$root/config.php");
require_once("$root/simpletest/unit_tester.php");
require_once("$root/simpletest/web_tester.php");
//require_once("$root/simpletest/net_work_lib.php");
require_once("$root/linecache.php");
require_once("$root/libs/function.php");

if (@$_GET['page']) {
    require "$root/tests/{$_GET['page']}.php";
    exit(0);
}

$testcase = new TestSuite('MONITOR');

//if (isset($_GET['mode2'])) {
//	echo $_GET['mode2'] . "<br/>";
//}

if (@$_GET['service']) {
    $f = "$root/tests/{$_GET['service']}.php";
    if (is_file($f)) {
        $testcase->addFile($f);
    } else {
        echo "<?xml version='1.0' encoding='utf-8'?><xml></xml>"; exit(0);
    }
} else {
	$testcase->addFile("$root/tests/health.php");
	$testcase->addFile("$root/tests/proxy.php");
	$testcase->addFile("$root/tests/acl.php");
	$testcase->addFile("$root/tests/async.php");
	$testcase->addFile("$root/tests/weight.php");
	$testcase->addFile("$root/tests/authenticate.php");
	//$testcase->addFile("$root/tests/websocket-php-master/tests/websocket.php");
	$testcase->addFile("$root/tests/halfasync.php");
}

class StackTrace extends SimpleStackTrace {
    function __construct() {
        parent::__construct(Array('assert', 'expect', 'pass', 'fail', 'skip'));
    }

    function traceFileLine() {
        $stack = $this->captureTrace();
        foreach ($stack as $frame) {
            if ($this->frameLiesWithinSimpleTestFolder($frame)) {
                continue;
            }
            if ($this->frameMatchesPrefix($frame)) {
                return Array($frame['file'], $frame['line']);
            }
        }
        return false;
    }
}

class BscRest extends SimpleReporter {
    private $case;
    private $starttime;
    private $curdata;
    private $ret;
    private $curservice;

    function __construct() {
        parent::__construct();
    }

    function toParsedXml($text) {
        return $text;
    }

    function paintCaseStart($test_name) {
        parent::paintCaseStart($test_name);
        $this->case = $test_name;
        $this->curservice = $this->toParsedXml(strtolower(substr($this->case, 0, -8)));
        $this->curdata = array();
        $this->ret["$this->curservice"] = array();
        $this->ret["$this->curservice"]["status"] = "ok";
        $this->ret["$this->curservice"]["msg"] = array();
    }

    function paintMethodStart($test_name) {
        parent::paintMethodStart($test_name);
    }

    function paintMethodEnd($test_name) {
        parent::paintMethodEnd($test_name);
    }

    function _paint($errno, $info='') {
        if ($errno == 0) return;

        $trace = new StackTrace();
        list($filename, $line) = $trace->traceFileLine();
        $assert = $this->toParsedXml(trim(getline($filename, $line)));
        $info = $this->toParsedXml($info);
        $this->ret["$this->curservice"]["status"] = "error";
        $this->ret["$this->curservice"]["msg"][] = array("info" => $info, "code" => $assert);;
    }

    function paintPass($message) {
        parent::paintPass($message);
        $this->_paint(0);
    }

    function paintFail($message) {
        parent::paintFail($message);
        $this->_paint(1, $message);
    }

    function paintError($message) {
        parent::paintError($message);
        $this->_paint(1, $message);
    }

    function paintException($exception) {
        parent::paintException($exception);
        $name = "$this->current_case.$this->current_method";
        $message = 'Unexpected exception of type [' . get_class($exception) .
                '] with message ['. $exception->getMessage() .
                '] in ['. $exception->getFile() .
                ' line ' . $exception->getLine() . ']';
        $this->_paint(1, $message);
    }

    function paintSkip($message) {
        parent::paintSkip($message);
        print $this->toParsedXml($message);
    }

    function paintMessage($message) {
        parent::paintMessage($message);
        print $this->toParsedXml($message);
    }

    function paintFormattedMessage($message) {
        parent::paintFormattedMessage($message);
        print "<![CDATA[$message]]>";
    }

    function paintSignal($type, $payload) {
        parent::paintSignal($type, $payload);
        print "<![CDATA[" . serialize($payload) . "]]>";
    }

    function paintHeader($test_name) {
    }

    function paintFooter($test_name) {
        echo json_encode($this->ret);
        return;
    }
}

class BscXmlReporter extends SimpleReporter {
    private $case;
    private $starttime;

    function __construct() {
        parent::__construct();
    }

    function toParsedXml($text) {
        return str_replace(
                array('&', '<', '>', '"', '\''),
                array('&amp;', '&lt;', '&gt;', '&quot;', '&apos;'),
                $text);
    }

    function paintCaseStart($test_name) {
        parent::paintCaseStart($test_name);
        $this->case = $test_name;
    }

    function paintMethodStart($test_name) {
        parent::paintMethodStart($test_name);
        $service = $this->toParsedXml(strtolower(substr($this->case, 0, -8)));
        $item = $this->toParsedXml($test_name);
        print "
<resource>
<service>$service</service>
<item>$item</item>
<error>";
        $this->starttime = microtime(true);
    }

    function paintMethodEnd($test_name) {
        parent::paintMethodEnd($test_name);
        $timedelta = microtime(true) - $this->starttime;
        print "
</error>
<timedelta>$timedelta</timedelta>
</resource>";
    }

    function _paint($errno, $info='') {
        if ($errno == 0) return;

        $trace = new StackTrace();
        list($filename, $line) = $trace->traceFileLine();
        $assert = $this->toParsedXml(trim(getline($filename, $line)));
        if (!$assert) return;

        if (preg_match("/\], (.*) at \[\/data1\/www\/htdocs/", $info, $m)) {
            $e = $m[1];
            if (strlen($e) > 20) {
                $e = substr($e, 0, 20);
            }
            $assert .= ";$e";
        }
        $info = $this->toParsedXml($info);
        print "
<assert>
<source>$assert</source>
<info>$info</info>
</assert>";
    }

    function paintPass($message) {
        parent::paintPass($message);
        $this->_paint(0);
    }

    function paintFail($message) {
        parent::paintFail($message);
        $this->_paint(1, $message);
    }

    function paintError($message) {
        parent::paintError($message);
        $this->_paint(1, $message);
    }

    function paintException($exception) {
        parent::paintException($exception);
        $name = "$this->current_case.$this->current_method";
        $message = 'Unexpected exception of type [' . get_class($exception) .
                '] with message ['. $exception->getMessage() .
                '] in ['. $exception->getFile() .
                ' line ' . $exception->getLine() . ']';
        $this->_paint(1, $message);
    }

    function paintSkip($message) {
        parent::paintSkip($message);
        print $this->toParsedXml($message);
    }

    function paintMessage($message) {
        parent::paintMessage($message);
        print $this->toParsedXml($message);
    }

    function paintFormattedMessage($message) {
        parent::paintFormattedMessage($message);
        print "<![CDATA[$message]]>";
    }

    function paintSignal($type, $payload) {
        parent::paintSignal($type, $payload);
        print "<![CDATA[" . serialize($payload) . "]]>";
    }

    function paintHeader($test_name) {
        header('Content-type: text/xml');
        print "<?xml version=\"1.0\"?><xml>\n";
    }

    function paintFooter($test_name) {
        print "</xml>\n";
    }
}

$format = isset($_GET['format']) ? $_GET['format'] : 'html';

if ($format == 'html') {
    $reporter = new HtmlReporter();
} else if ($format == 'txt') {
    $reporter = new TextReporter();
} else if ($format == 'xml') {
    $reporter = new BscXmlReporter();
} else if ($format == 'json') {
    $reporter = new BscRest();
} else {
    echo "invalid argument: format=$format";
    exit(0);
}

$testcase->run($reporter);

?>
