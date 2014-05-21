<?php

$I = new ApiGuy($scenario);
$I->wantTo('check if [GET /_bebop/api/pages] is returning JSON with the correct structure');
$I->sendGET('pages');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('"meta":{');
$I->seeResponseContains('"items":[');
