<?php

class HelpController extends Controller {
    
    private $toc;
    
    function __construct()
    {
        $this->toc = <<<TOC
<div class="well table_of_contents">
    <ol>
        <li><a href="/help/todo">Feature Requests</a></li>
        <li><a href="/help/faq">FAQ</a></li>
        <li><a href="/help/contact">Contact Us</a></li>
    </ol>
</div>
TOC;
    }
    
    public function getTableOfContents()
    {
        return $this->toc;
    }
    
    public function isAuthorized()
    {
        return true;
    }
    
}