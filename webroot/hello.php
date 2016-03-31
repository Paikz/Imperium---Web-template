<?php

include(__DIR__. '/config.php');

$imperium['title'] = "Hello world!";

$imperium['header'] = <<<EOD
<img class="sitelogo" src="img/imperium.png" alt="Imperium logo"/>
<span class="sitetitle">Imperium webbtemplate</span>
<span class="siteslogan">Återanvändbara moduler för webbutveckling med PHP</php>
EOD;

$imperium['navbar'] = CNavigation::GenerateMenu($menu, 'navbar');

$imperium['main'] = <<<EOD
<h1>Hej världen!</h1>
<p>Detta är en exempelsida som visar hur Imperium ser ut och fungerar.</p>
EOD;

$imperium['footer'] = <<<EOD
<footer><span class="sitefooter">Copyright (c) Philip Esmailzade (philipesmailzade@gmail.com) | <a href='http://validator.w3.org/unicorn/check?ucn_uri=referer&amp;ucn_task=conformance'>Unicorn</a></span></footer>
EOD;

include(IMPERIUM_THEME_PATH);
