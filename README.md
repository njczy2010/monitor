# yunlian服务监控

## 如何写监控代码

首先在tests目录下新建一个文件xxx.php。其中xxx为你的服务名。

```php
class XxxTestCase extends UnitTestCase {
    function setUp() {
        // some setup work
    }

    function tearDown() {
        // some teardown work
    }

    function testFunction1() {
        $this->assertTrue(1 + 1 == 2);
    }

    function testFunction2() {
        // test the second function
    }
}
```

在index.php文件中加入该文件。 

```php
$testcase->addFile("$root/tests/xxx.php");
```

**setUp** 和 **tearDown** 分别会在本测试case所有的test method运行之前和之后运行，你可以将一些setup和cleanup的工作放在这两个方法里。

XxxTestCase中所有以 **test** 开头的方法都会成为测试用例。建议每个方法里中只对服务的某一个功能进行测试，而不是把所有的测试用例全写在一个方法里。

使用 **assert...()** 方法来添加测试。一些常用的assert方法如下表：

```
assertTrue($x)                  Fail if $x is false 
assertFalse($x)                 Fail if $x is true 
assertNull($x)                  Fail if $x is set 
assertNotNull($x)               Fail if $x not set 
assertIsA($x, $t)               Fail if $x is not the class or type $t 
assertNotA($x, $t)              Fail if $x is of the class or type $t 
assertEqual($x, $y)             Fail if $x == $y is false 
assertNotEqual($x, $y)          Fail if $x == $y is true 
assertWithinMargin($x, $y, $m)  Fail if abs($x - $y) < $m is false 
assertOutsideMargin($x, $y, $m) Fail if abs($x - $y) < $m is true 
assertIdentical($x, $y)         Fail if $x == $y is false or a type mismatch 
assertNotIdentical($x, $y)      Fail if $x == $y is true and types match 
assertReference($x, $y)         Fail unless $x and $y are the same variable 
assertClone($x, $y)             Fail unless $x and $y are identical copies 
assertPattern($p, $x)           Fail unless the regex $p matches $x 
assertNoPattern($p, $x)         Fail if the regex $p matches $x 
expectError($x)                 Fail if matching error does not occour 
expectException($x)             Fail if matching exception is not thrown 
ignoreException($x)             Swallows any upcoming matching exception 
assert($e)                      Fail on failed expectation object $e  
```

所有的 **assert...()** 方法都可以传一个可选的 **description** 作为最后一个参数，如果不传这个参数，只有默认的信息会被显示（一般足够了），如果你想添加一些额外的信息，传这个参数给 **assert...()** 方法就行了。

更多使用方法请参见 `simpletest/docs/en/unit_test_documentation.html`

## 如何使用

```
http://monitor.yunlian.io/[?format={html|txt|xml}][&service=xxx]
（这是之前的，看下面这个）

http://139.196.141.219/yunlian-ops/monitor/index.php[?mode2={debug}][&nginx_ip={123.59.102.49}][?format={html|txt|xml}][&service=xxx]
```

直接打开时显示格式为html，此时只显示fail的信息，必须出现 <font style="background-color: green">green bar</font> 才表示所有的测试通过，如果出现 <font style="background-color: red">red bar</font> 或者什么bar也没有出现说明有测试失败或者出现了fatal error。

mode2=debug 表示看输出的信息
nginx_ip=123.59.102.49表示测试某个nginx

## 部署monitor  
### 1）修改/simpletest/[unit_tester.php][1]  
在 class UnitTestCase 中（22行左右）加入三个函数：    
execUnknown,execSuccess,execFail ，详见代码  

### 2）准备后端
在/backend下创建以下php:  
for_half_async1.php  
for_half_async2.php  
simple2.php  
simple3.php  
record_redis.php  

### 3) 部署yunlian  
详见后面测试点  

### 4)修改配置文件  
修改/config.php下的  
$G_CONFIG["yunlian"]["domain"]  

$G_CONFIG["backend"]["root"]  

### 5)配置WebSocket  

## 测试点

### 透明代理：  
设置yunlian：  
/test_proxy  
设置后端:http://139.196.141.219/yunlian-ops/monitor/backend/simple2.php 选择透明代理  

1、测试后端访问正常，HTTP200，得到访问内容；  
2、测试云链链接访问正常，HTTP200，得到访问内容；  
3、比较二者访问内容相同（MD5）；  

### 半异步：  
设置yunlian：  
/Half_Async  
后端：http://139.196.141.219/yunlian-ops/monitor/backend/for_half_async1.php  
选择半异步  
<font style="background-color: red">设置异步化</font> ->访问后端最大并发数：8  
<font style="background-color: red">设置后端</font> ->超时时间（秒）：10  

/test_proxy_for_half  
后端：http://139.196.141.219/yunlian-ops/monitor/backend/for_half_async1.php  
选择透明代理  

1-3同上；  
4、使用ab访问云链链接，同时访问后端链接，透明代理下，返回<font style="background-color: red">503</font>，半异步下返回200。  

ps :   
503 (Service Unavailable/服务无法获得)
状态码503 (SC_SERVICE_UNAVAILABLE)表示服务器由于在维护或已经超载而无法响应。  

### 全异步：  
设置yunlian：  
/test_async  
后端：http://139.196.141.219/yunlian-ops/monitor/backend/record_redis.php  
选择全异步  

1、测试后端访问正常，HTTP200，得到访问内容；  
2、测试云链链接访问正常，HTTP200，测试请求加入队列成功，返回JSON（比较MD5）；  
3、测试能正确调用回调URL（目前打算用Redis记录访问信息）；  
4、测试请求内容，与正常访问后端内容相同。  

### WebSocket：  
设置yunlian:  
/websocket  
后端：http://139.196.141.219/yunlian-ops/monitor/backend/record_redis.php  
选择WebSocket  

1、测试后端访问正常，HTTP200，得到访问内容；  
2、开启两个or多个客户端访问Web Socket，得到相同访问内容；  
3、判断资源访问数为1；  
4、断开所有客户端，判断资源访问数为0；  

### Tunnel:
设置yunlian:  
/test_tunnel  
后端->接入方式：安全隧道，根据 接入主机中->内网穿透程序，修改/libs/ConnectTunnel.sh      

测试前运行/libs/ConnectTunnel.sh，确保Tunnel 连接    

1、测试后端访问正常，HTTP200，得到访问内容；  
2、访问<yunlian_url>/test_tunnel/yunlian-ops/monitor/backend/simple2.php，返回200  

note:如果隧道连接不成功，返回<font style="background-color: red">502</font>  

502 (Bad Gateway/错误的网关)  
502 (SC_BAD_GATEWAY)被用于充当代理或网关的服务器；该状态指出接收服务器接收到远端服务器的错误响应。  

### ACL：  
设置yunlian：  
#### a)访问次数：  
/test_visitcount  
后端：http://139.196.141.219/yunlian-ops/monitor/backend/simple2.php  
设置ACL->访问频次限制->次/分钟：6  
选择透明代理  

#### b)black_list  
使用的访问ip为 139.196.141.219  
选择透明代理  
后端：http://139.196.141.219/yunlian-ops/monitor/backend/simple2.php  

/test_black_list_success  
设置ACL->访问IP限制->黑名单：  
37.110.61.124  
35.110.61.124  
140.196.141.219  
138.196.141.219  

/test_black_list_fail  
（禁止访问，报） 
设置ACL->访问IP限制->黑名单：  
139.196.141.219   

#### c)white_list  
使用的访问ip为 139.196.141.219  
选择透明代理  
后端：http://139.196.141.219/yunlian-ops/monitor/backend/simple2.php  

/test_white_list_success  
设置ACL->访问IP限制->白名单：  
139.196.141.219  
36.110.61.124  

/test_white_list_fail  
（禁止访问，报） 
设置ACL->访问IP限制->白名单：  
139.196.141.220  

1-3同上；  
4、设置每分钟访问次数上限，比如100，那么测试前100次访问成功，101次访问失败,返回<font style="background-color: red">403</font>（第101访问 后端成功，但是访问代理失败）。  
5、验证黑白名单。分别把公司IP加入黑名单或白名单中，判断是否访问失败(返回<font style="background-color: red">403</font>)或成功。  

ps:  
403 (Forbidden/禁止)  
403 (SC_FORBIDDEN)的意思是除非拥有授权否则服务器拒绝提供所请求的资源。这个状态经常会由于服务器上的损坏文件或目录许可而引起。   

#### d) 测试权重
/test_weight  
后端1：http://139.196.141.219/yunlian-ops/monitor/backend/simple2.php，设置较大权重（如70）  
后端2：http://139.196.141.219/yunlian-ops/monitor/backend/simple3.php，设置较大权重（如30） 
选择透明代理 

测试：
访问yunlian一定次数（如20），发现返回后端1的次数 >= 返回后端2的次数  

### 调用者：  
#### Basic：  
设置yunlian：  
/test_basic  
后端：http://139.196.141.219/yunlian-ops/monitor/backend/simple2.php  
设置调用者管理（右上角的三个点）->添加：  
名称：czy2 ， 选择认证方式 ： Basic认证 ， Usename ： xxxx , Password : xxxxx， 读写控制：允许读写  
设置ACL->调用者绑定：czy2 ， 关闭允许匿名访问    

1、验证用户名密码正确时访问成功，错误时访问失败，返回<font style="background-color: red">401</font>；  
2、验证允许读写时操作均可，只读时只能进行GET操作, 错误时返回<font style="background-color: red">405</font>；  

#### ApiKey：  
设置yunlian：  
/test_apikey  
后端：http://139.196.141.219/yunlian-ops/monitor/backend/simple2.php  
设置调用者管理（右上角的三个点）->添加：  
名称：njczy2010 ， 选择认证方式 ： ApiKey认证 ，允许读写  
设置ACL->调用者绑定：njczy2010 ， 关闭允许匿名访问    

1、验证请求时所加参数或请求头中参数是否正确,错误时返回<font style="background-color: red">401</font>；  
2－4同Basic；  

#### IP：   
/test_ip  
设置调用者管理（右上角的三个点）->添加：  
名称：sonia ， 选择认证方式 ： IP认证 （IP列表：139.196.141.219） ，只允许读     
设置ACL->调用者绑定：sonia ， 关闭允许匿名访问 
/test_ip2  
设置调用者管理（右上角的三个点）->添加：  
名称：sonia2 ， 选择认证方式 ： IP认证 （IP列表：139.196.141.220） ，允许读写   
设置ACL->调用者绑定：sonia2 ， 关闭允许匿名访问 

类似黑白名单 , 错误时返回<font style="background-color: red">401</font> 。    

### ps:  
401 (Unauthorized/未授权)  
405 (Method Not Allowed/方法未允许)  
405 (SC_METHOD_NOT_ALLOWED)指出请求方法(GET, POST, HEAD, PUT, DELETE, 等)对某些特定的资源不允许使用。该状态码是新加入 HTTP 1.1中的。  

### 附录：
[http状态码][2]  
这些状态码被分为五大类：   

100-199 用于指定客户端应相应的某些动作。   
200-299 用于表示请求成功。   
300-399 用于已经移动的文件并且常被包含在定位头信息中指定新的地址信息。   
400-499 用于指出客户端的错误。   
500-599 用于支持服务器错误。   

  [1]: https://github.com/sprewellkobe/yunlian-ops/blob/master/monitor/simpletest/unit_tester.php
  [2]: http://www.cnblogs.com/lxinxuan/archive/2009/10/22/1588053.html
  
  
  