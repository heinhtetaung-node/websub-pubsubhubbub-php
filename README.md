# websub-pubsubhubbub-php
Websub (pubsubhubbub) with php publish and scribe

Websub is a publish/scribe platform that allow scribers to receive feeds from publisher.

We can check and understand why we should use publish/scribe pattern by watching this video https://www.youtube.com/watch?v=l6S7i91nfSg


#Implementation
1. clone this repo
2. Subscribe feed to hub
```
curl -X POST 'http://pubsubhubbub.appspot.com/' -d'hub.verify=sync' -d'hub.topic=youdomain.com/feed.xml' -d'hub.callback=yourdomain.com/accept.php' -d'hub.mode=subscribe' -d'hub.verify_token=test_verify_token'  -D-
```
So, hub will check your callback url yourdomain.com/accept.php with get method, it should be echo hub_challenge

If everything is right, it will return 204, otherwise you have error

3. If success, publish your feed to hub

open url yourdomain.com/accept.php and submit, that will publish your feed to hub

4. After publishing is finished hub will inform to your subscriber's callback url

check log.txt file that will print your new xml object

And inside your folder it will create new xml file with current date which containing new item.



#Adding new item to feed.xml
1. If you want to add new item to feeds.xml file, you can add new entry tag under `<id>http://blog.superfeedr.com/</id>`  line no 8

2. After adding, please publish your changes to inform hub

open url yourdomain.com/accept.php and submit, that will publish your feed to hub

3. After publishing is finished hub will inform to your subscriber's callback url check log.txt file that will print your new xml object

And inside your folder it will create new xml file with current date which containing new item.


That's it, how easy! happy coding.

If you don't understand, can ask me by email heinhtetaung.itlife@gmail.com



This repo is referenced from the following links, Special thanks to these coding geeks

https://www.youtube.com/watch?v=l6S7i91nfSg

https://blog.superfeedr.com/howto-pubsubhubbub/

http://c-loft.com/blog/?p=2281  

https://github.com/joshfraser/pubsubhubbub-php/blob/master/publisher_example.php

