<?php
	include('publisher.php');
    main();

    function main(){
        // LogPut("main() start");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == 'GET') {
            //curl -X POST 'http://pubsubhubbub.appspot.com/' -d'hub.verify=sync' -d'hub.topic=http://yourdomain.com/feed.xml' -d'hub.callback=http://yourdomain.com/accept.php' -d'hub.mode=subscribe' -d'hub.verify_token=test_verify_token'  -D-
        	doGet();  // verify feed from pubsubhubbub when scribe

        } else if ($method == 'POST') {
        	if (isset($_POST['sub']) && $_POST['sub'] == 'Publish') {
        		doPublish();  // publish post
        	}else{
        		// get new feeds
        		doPost();
        	}
        }
        // LogPut("main() end");
    }
    function doGet(){
        // LogPut("doGet() start");
        $verify_token = "test_verify_token";
        // subscribe (or unsubscribe) a feed from the HUB
        // echo "<pre>";
        // var_dump($_REQUEST['hub_challenge']); 
        // echo "<br>";

        // exit;

        $hubmode = $_REQUEST['hub_mode'];
        $hubchallenge = $_REQUEST['hub_challenge'];
        if ($hubmode == 'subscribe' || $hubmode == 'unsubscribe') {
            if ($_REQUEST['hub_verify_token'] != $verify_token) {//verify_tokenのチェック
                // LogPut("doGet() hub_verify_token unmatch");
                header('HTTP/1.1 404 "Unknown Request"', null, 404);
                exit("Unknown Request");
            }
            // response a challenge code to the HUB
            header('HTTP/1.1 200 "OK"', null, 200);
            header('Content-Type: text/plain');
            echo $hubchallenge;
        } else {
            header('HTTP/1.1 404 "Not Found"', null, 404);
            // LogPut("doGet() hubmode unmatch");
            // exit("Unknown Request");

            // if simple call from url just show form
            echo "<form method='POST'>";
		    echo "hub url: <input name='hub_url' type='text' value='http://pubsubhubbub.appspot.com/publish' size='50'/><br />";
		    echo "topic url: <input name='topic_url' type='text' value='http://yourdomain.com/feed.xml' size='50' /><br />";
		    echo "<input name='sub' type='submit' value='Publish' /><br />";
		    echo "</form>";

        }
        // LogPut("doGet() end");
    }
     
    function doPost(){
        LogPut("doPost() start");
        // receive a feed from the HUB
        // feed Receive
        $string = file_get_contents("php://input");         
        //ファイル保存とか
        $fp = fopen(date('YmdHis') . "_atom" . ".xml", "w");
        fwrite($fp, $string);
        fclose($fp);         
        //後は適当にParseしよう
        if (FALSE === ($feed = simplexml_load_string($string))) {
            LogPut("doPost() feed Parse ERROR");
            exit("feed Parse ERROR");
        }
        LogPut(var_export($feed,true));         
        foreach ($feed->entry as $entry) {
            $url = $entry->link['href'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $fp = fopen(basename($url), "w");
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
        }
        LogPut("doPost() end");
    }     
    //標準出力及びログファイルへ出力
    function LogPut($buf){
        $date = date('Y/m/d(D) H:i', time());
        $buf = $date." ". $buf;         
        //標準出力
        //echo mb_convert_encoding($buf . "<br>\n", "SJIS", "UTF-8");         
        //ログファイルへ出力
        $fp = fopen("log.txt", "a+");
        fputs($fp, $buf. "\n");
        fclose($fp);
    }

    function doPublish() {
	    
	    $hub_url = $_POST['hub_url'];
	    $topic_url = $_POST['topic_url'];
	    
	    // check that a hub url is specified
	    if (!$hub_url) {
	        echo "Please specify a hub url.<br /><br /><a href='publisher_example.php'>back</a>";
	        exit();
	    }
	    // check that a topic url is specified
	    if (!$topic_url) {
	        echo "Please specify a topic url to publish.<br /><br /><a href='publisher_example.php'>back</a>";
	        exit();
	    }         
	    
	    // $hub_url = "http://pubsubhubbub.appspot.com/publish";
	    $p = new Publisher($hub_url);
	    if ($p->publish_update($topic_url)) {
	        echo "<i>$topic_url</i> was successfully published to <i>$hub_url</i><br /><br /><a href='accept.php'>back</a>";
	    } else {
	        echo "ooops...";
	        print_r($p->last_response());
	    }
	}
?>
