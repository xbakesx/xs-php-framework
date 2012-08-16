<h1>Documentation - Philosophy</h1>
<?php
    echo $controller->getTableOfContents(); 
?>
<div class="content">
<p>The philosophy is simple.  Make a framework that you can pick up in a day and get to writing the code you <em>really</em> wanted to be writing.</p>
<p>Of course there is going to be a learning curve, you're using someone else's code.  Hopefully by being transparent as possible and staying away from hand-waving PHP magic we all understand the ramifications of the code we write, so we end up with more maintainable, efficient, high-quality code.</p>
<p>Let's get into some details:</p>
<p>Rules are rules.  We understand the desire to make everything configurable, we are programmers after-all, however allowing 1,000 different ways to do everything means the poor sap to look at your code a year later has to check 1,000 different places for how something works.  So if we create a convention like the url <code>/docs/philosophy</code> will have it's view code in <code>/view/docs/philosophy.php</code> and it's controller at <code>/controller/DocsController.php</code>, then there won't be a place to configure that to be different.</p>
<p>Reduce Magic.  We all love a good show, however trying to remember the name of all those global variables that you have to dig deep into a framework to see where it's assigned is a recipe for disaster.  I know we have these cool IDEs that can help us out, but if we don't need to lean on them by being good programmers, let's not.  Just like unnecessary global variables are bad, so are unnecessary magic variables.  We are hoping the less memorization you have to do, the quicker you'll pick up on the framework.</p>
</div>