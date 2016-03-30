<?php

include(__DIR__.'/config.php');


// Define what to include to make the plugin to work
$imperium['stylesheets'][]        = 'css/slideshow.css';
$imperium['javascript_include'][] = 'js/slideshow.js';

$imperium['title'] = "Slideshow för att testa JavaScript i Anax";


$imperium['header'] = <<<EOD
<img class="sitelogo" src="img/imperium.png" alt="Imperium logo"/>
<span class="sitetitle">Imperium webbtemplate</span>
<span class="siteslogan">Återanvändbara moduler för webbutveckling med PHP</php>
EOD;

$imperium['navbar'] = CNavigation::GenerateMenu($menu, 'navbar');

$imperium['main'] = <<<EOD
<div id="slideshow" class='slideshow' data-host="" data-path="img/me/" data-images='["me1.jpg", "me2.jpg", "me3.jpg"]'>
<img src='img/me/me1.jpg' width='950px' height='180px' alt='Me'/>
</div>

<h1>En slideshow med JavaScript</h1>
<p>Detta är en exempelsida som visar hur Anax fungerar tillsammans med JavaScript.</p>
EOD;

$imperium['footer'] = <<<EOD
<footer><span class="sitefooter">Copyright (c) Philip Esmailzade (philipesmailzade@gmail.com) | <a href='http://validator.w3.org/unicorn/check?ucn_uri=referer&amp;ucn_task=conformance'>Unicorn</a></span></footer>
EOD;

// Finally, leave it all to the rendering phase of Anax.
include(IMPERIUM_THEME_PATH);
