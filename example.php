<?php

include_once("UrlDiffer.php");

$obj = new UrlDiffer("http://test.examplecom/?foo=bar&cake=lie&you=monster&beer=good&speech=free&breaking=bad&task=edit","http://example.com/?&speech=free&beer=good&aperture=science&task=add&foo=bar&you=here&breaking=over",true);
$obj->getDifference();

echo "<br><br>";

$obj2 = new UrlDiffer("http://www.test.com?a=123&task=edit&frame=cake&news=brakingnews&order=top20&type=premium&history=allpast","http://www.test.com?task=edit&history=lastmonth&news=brakingnews&a=123&type=premium&order=top20",true);
$obj2->getDifference();
?>