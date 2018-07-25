<?php
//debug
defined('DEBUG') or define('DEBUG', true);
if (DEBUG === true) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 'Off');
    ini_set('log_errors', 'On');
}

if (false == function_exists('p')) {
    function p()
    {
        echo '<pre>';
        print_r(func_get_arg(0));
        echo '</pre>';
        exit();
    }
}

require __DIR__ . '/vendor/autoload.php';

use core\db\Db;
use QL\QueryList;

class Base
{
    public $url;
    public $fileName      = 'shoot.html';
    public $shoot_arr     = [];
    public $new_shoot_arr = [];
    private $_db;

    public function __construct($url)
    {
        $this->url = $url;
        $this->_db = new Db(parse_ini_file("db.ini"));
    }

    public function test1()
    {
        $html = file_get_contents($this->url);
        return file_put_contents($this->fileName, $html);
    }

    /*public function test2()
    {
        $html = file_get_contents($this->fileName);
        preg_match_all("/<td>(.*?)<\/td>/iU", $html, $matches);
        $data = [];
        foreach ($matches[1] as $key => $value) {
            #$value = preg_replace("#<(\/?[a-zA-Z].*?)>#is"," ", $value);
            $value = preg_replace('#</?[a-zA-Z][^>]*>#is'," ", $value);
            $value = preg_replace( "/\s(?=\s)/","\\1", $value);
            $data[$key] = explode(' ', $value);
        }
        p($data);
    }*/

    public function test2()
    {
        $html = file_get_contents($this->fileName);
        //然后可以把页面源码或者HTML片段传给QueryList
        $data = QueryList::html($html)->rules([
            //设置采集规则
            ['tr', 'text', '', function ($content) {
                return explode(PHP_EOL, $content);
            }],
        ])->query()->getData();
        //p($data->all());
        $buf = [];
        foreach ($data->all() as $key => $value) {
            $buf[$key] = $value[0];
        }
        //p($buf);
        $label = array_shift($buf);
        //p($label);
        foreach ($buf as $key => $value) {
            $value             = array_combine($label, $value);
            $this->shoot_arr[] = $value;
        }
        return $this->shoot_arr;

        /*$text = '<?php' . PHP_EOL . 'return '.var_export($this->shoot_arr, true).';';
    $file = 'shoot_arr.php';
    if(false !== fopen($file,'w+')){
    return file_put_contents($file, $text);
    }else{
    return false;
    }*/
    }

    public function test3($start, $length)
    {
        $new_shoot_arr = array_slice($this->shoot_arr, $start, $length);
        shuffle($new_shoot_arr);
        $this->new_shoot_arr = $new_shoot_arr;
        return $this->new_shoot_arr;
    }

    public function test4()
    {
        //p($this->_db);
        //排名、姓名、球队、射门数、左脚、右脚、头球、其它部位
        //rank,name,team,shots,left,right,head,other
        $insertValue = '';
        foreach ($this->new_shoot_arr as $key => $value) {
            foreach ($value as $k => $val) {
                if (!is_numeric($val)) {
                    $value[$k] = '"' . $val . '"';
                }
            }
            $str = '(' . implode(',', $value) . '),';
            $insertValue .= $str;
        }

        $sql = 'INSERT INTO `shoot` (`rank`,`name`,`team`,`shots`,`left`,`right`,`head`,`other`) VALUES ' . rtrim($insertValue, ',');
        //p($sql);
        $this->_db->query($sql);
    }

    public function test5()
    {
        $sql    = 'SELECT `rank`,`name`,`team`,`shots`,`left`,`right`,`head`,`other` FROM `shoot` ORDER BY `shots` DESC,`left` DESC, `right` DESC';
        $player = $this->_db->query($sql);
        return $player;
    }

    public function add1()
    {
        $order = ['left' => SORT_DESC];
        return $this->listOrder($order);
    }

    public function add2()
    {
        $order = ['left' => 'DESC', 'right' => 'DESC'];
        return $this->listOrder($order);
    }

    public function add3()
    {
        $order = ['left' => 'DESC', 'right' => 'DESC', 'head' => 'DESC'];
        return $this->listOrder($order);
    }

    private function listOrder($order = [])
    {
        if (empty($order)) {
            $sort = 'id DESC';
        } else {
            $buf = '';
            foreach ($order as $k => $val) {
                $buf .= '`' . $k . '` ' . $val . ',';
            }
            $sort = rtrim($buf, ',');
        }

        $sql = 'SELECT `rank`,`name`,`team`,`shots`,`left`,`right`,`head`,`other` FROM `shoot` ORDER BY ' . $sort;
        //p($sql);
        return $this->_db->query($sql);
    }
}
// 排行榜页面地址为
$url = 'http://match.sports.sina.com.cn/football/csl/opta_rank.php?item=shoot&year=2014&lid=8&type=1&dpc=1';

#$base = new Base($url);
// 一、请编写一个php函数，获取排行榜页面内容，然后保存在工作目录的shoot.html。
//p($base->test1());
// 二、请编写一个php函数，获取shoot.html的内容，提取球员的数据：排名、姓名、球队、射门数、左脚、右脚、头球、其它部位，保存到二维数组$shoot_arr。
#p($base->test2());
//三、请编写一个php函数，把二维数组$shoot_arr的第10-20提取到一个新的二维数组$new_shoot_arr，将其随机打乱顺序后保存到shoot_arr.php。
#$base->test3(10, 10); //需要先执行test2()赋值
// 四、请编写一个php函数，把二维数组$new_shoot_arr的每个一维数组对应一行记录保存在mysql数据库，请自行设计数据表名、字段等内容。
#$base->test4(); //需要先执行test2() 与 test3() 赋值
// 五、请编写一个php函数，从题目四的mysql数据表中获取数据，并且按照球员的射门数、左脚、右脚倒序输出。
#$base->test5();

//附加题
//请编写一个php函数，把题目3的二维数组$shoot_arr按照球员的射门数、左脚、右脚进行排序，然后打印出该二维数组。排序规则如下：
//1、所有球员按左脚射门次数从高到低排列；
#p($base->add1());
//2、左脚射门次数相同的，再以右脚射门次数从高到低排序；
#p($base->add2());
//3、左右脚射门次数均相同的，再以头球次数从高到低排序；
#p($base->add3());

class Advanced
{
	public $shoot_arr;
	public function __construct($data = [])
	{
		$this->shoot_arr = $data;
	}

	public function test1()
	{
		//echo getStrToNumFormat('德杨');
		$new_shoot_arr = [];
		foreach ($this->shoot_arr as $key => $value) {
			$new_shoot_arr[$key]['rank'] = $value['排名'];
			$new_shoot_arr[$key]['name'] = $this->getStrToNumFormat($value['球员']);
			$new_shoot_arr[$key]['team'] = $value['球队'];
			$new_shoot_arr[$key]['shots'] = $value['射门数'];
			$new_shoot_arr[$key]['left'] = $value['左脚'];
			$new_shoot_arr[$key]['right'] = $value['右脚'];
			$new_shoot_arr[$key]['head'] = $value['头球'];
			$new_shoot_arr[$key]['other'] = $value['其它部位'];
		}

		return $new_shoot_arr;
	}

	public function test2()
	{
		//array_multisort(array_column($this->shoot_arr,'左脚'), SORT_DESC, $this->shoot_arr);
		//array_multisort(array_column($this->shoot_arr, '左脚'), SORT_DESC, array_column($this->shoot_arr,'右脚'), SORT_DESC, $this->shoot_arr);
		array_multisort(array_column($this->shoot_arr,'左脚'), SORT_DESC, array_column($this->shoot_arr,'右脚'), SORT_DESC,array_column($this->shoot_arr,'头球'), SORT_DESC, $this->shoot_arr);
		return $this->shoot_arr;
	}

	private function getStrToNumFormat($str, $space = 3, $separate = '=')
	{
		if(empty($str)) return '';
	    $mdv = md5(uniqid(md5($str)) . microtime(true));
	    $mdv1 = substr($mdv,0,16);
		$mdv2 = substr($mdv,16,16);
		$crc1 = abs(crc32($mdv1));
		$crc2 = abs(crc32($mdv2));
		$buf = bcmul($crc1, $crc2);
		$nStr = chunk_split($buf, $space, $separate);
		return rtrim($nStr, $separate);
	}

}
$shoot_arr = require __DIR__ . '/shoot_arr.php';
$advanced = new Advanced($shoot_arr);
//一、请编写一个php函数，获取C:/phpstudy/www/shoot_arr.php的内容转换为二维数组$new_shoot_arr，把姓名转换为唯一数字后每3位加上一个等号。
//例如“德扬”转换为123=456=789=0
#p($advanced->test1());
//二、请编写一个php函数，把题目一的二维数组$shoot_arr按照球员的射门数、左脚、右脚进行排序，然后打印出该二维数组。排序规则如下：
//1、所有球员按左脚射门次数从高到低排列；
//2、左脚射门次数相同的，再以右脚射门次数从高到低排序；
//3、左右脚射门次数均相同的，再以头球次数从高到低排序；
#p($advanced->test2());
//三、请编写一个php函数，输入球员参数1和球员参数2，根据两者的数据（排名、射门数、左脚、右脚、头球、其它部位），输出这两个球员的相似百分比。

//四、请编写一个php函数，加载C:/phpstudy/www/shoot_sql.txt到mysql数据库test.user，然后查询test.user，获取最多球员的前5个球队和对应的球员数，保存到二维数组$top_team5。

//五、请编写一个php函数，输入一个php文件路径，返回该文件有多少个类，每个类有多少个方法，每个方法有多少行代码，每个方法有多少行注释，是否存在exec、system、eval等高危函数的调用，判断是否存在语法错误，用于代码审计。
#Zuzuche Computer test answer example.