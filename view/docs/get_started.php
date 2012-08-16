<h1>Documentation - Get Started</h1>
<?php
    echo $controller->getTableOfContents(); 
?>
<div class="content">
<h2><a name="get_started">Getting Started</a></h2>
<p>First you will need to understand that this is a PHP framework, so you'll need a working knowledge of PHP.  Also a rudimentary knowledge of a webserver will be important, so we can get your new website... servied.</p>
<p>As for software git will be helpful, but you'll be fine downloading a zip file from github and working with that.  So let's start there.</p>

<h2><a name="download">Download The Source</a></h2>
<p>I will give you <a href="https://github.com/xbakesx/xs-php-framework">this link</a> and hope you can figure the rest out.</p> 

<h2><a name="webserver">Set Up Your Webserver</a></h2>
<p>I will assume you already have a webserver up and running.  If you do not, may I suggest setting up an nginx webserver or apache webserver?  You can find great information about how to do that <a href="https://www.google.com/search?q=how+to+install+nginx">here</a> and <a href="https://www.google.com/search?q=how+to+install+apache">here</a> respectively.</p>
<p>For the sake of argument, I will assume you have nginx configured.  Great!  There's a sample nginx config file in <code>/nginx/xs-php-framework.conf</code>.  Make sure to replace the root and <code>include ... php.conf</code> lines to suit your environment, and make sure to update your <code>/etc/hosts</code> file to point xs-php-framework.local at your webserver.</p>
<p>Oh, you have apache (or something else...  really?  something else?), don't worry, the only real configuration we made to nginx is to redirect all urls to <code>index.php</code>, while making sure that <code>$_SERVER['REQUEST_URI']</code> has the real url requested in it.</p>
<p>Hopefully now that your webserver is configured, you can open up the website at <a href="http://xs-php-framework.local/">xs-php-framework.local</a>.</p>

<h2><a name="structure">Directory Structure</a></h2>
<p>Below I will describe how the directory structure is setup.  I will use a <code>*</code> to indicate that these are files you create, and they match up accordingly.  So, <code>/view/*/index.php</code> is the view that corresponds to the controller <code>/controller/*Controller.php</code> and the model <code>/model/*.php</code></p>
<ul class="file_browser">
    <li class="folder">
        /
        <ul>
            <li class="folder">
                <code>conf</code> - Directory for app level configurations
                <ul>
                    <li class="file"><code>app.php</code> - Contains a class that extends App, that has html configurations for your app</li>
                    <li class="file"><code>header.php</code> - This is included at the top of each of your pages</li>
                    <li class="file"><code>footer.php</code> - This is included at the bottom of each of your pages</li>
                </ul>
            </li>
            <li class="folder">
                <code>controller</code> - Directory containing all your controllers
                <ul>
                    <li class="file"><code>*Controller.php</code> - This is a controller you write.  It should contain 1 class called <code>*Controller</code> and it should extend <code>Controller</code> (and therefore implement <code>isAuthorized</code>.  It contains configurations for all urls going to <code>/*</code></li>
                    <li class="system_file"><code>UserController.php</code> - This is a premade controller to handle authentication &quot;out of the box&quot;</li>
                </ul>
            </li>
            <li class="system_folder"><code>framework</code> - Magical code, enter at your own risk</li>
            <li class="folder">
                <code>model</code> - Directory containing all your models
                <ul>
                    <li class="file"><code>*.php</code> - TODO</li>
                </ul>
            </li>
            <li class="folder">
                <code>view</code> - Directory of your views, should be filled with directories, each directory contains one &quot;html&quot; page per url
                <ul>
                    <li class="file"><code>*/index.php</code> - This is the view you write.  This file is included between <code>/conf/header.php</code> and <code>/conf/footer.php</code> and correlates to the url <code>/*/index.php</code> (index.php can be &lt;anything_you_want&gt;.php)</li>
                </ul>
            </li>
            <li class="folder">
                <code>www</code> - The web directory
                <ul>
                    <li class="folder"><code>css</code> - put your css in here</li>
                    <li class="folder"><code>img</code> - put your images in here</li>
                    <li class="folder"><code>js</code> - put your javascript in here</li>
                    <li class="system_file"><code>index.php</code> - this really has to be index.php and editing this is as dangerous as editing things in <code>/framework</code></li>
                </ul>
        </ul>
    </li>
</ul>
</div>