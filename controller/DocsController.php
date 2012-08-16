<?php

class DocsController extends Controller {
    
    private $toc;
    
    function __construct()
    {
        $this->toc = <<<TOC
<div class="well table_of_contents">
    <ol>
        <li><a href="/docs/philosophy">Philosophy</a></li>
        <li>
            <a href="/docs/get_started">Getting Started</a>
            <ol>
                <li><a href="/docs/get_started#download">Download Source</a></li>
                <li><a href="/docs/get_started#webserver">Web Server</a></li>
                <li><a href="/docs/get_started#structure">Directory Structure</a></li>
                <li><a href="/docs/get_started#mvc">&quot;MVC&quot;</a></li>
                <li><a href="/docs/get_started#controllers">Controllers</a></li>
                <li><a href="/docs/get_started#views">Views</a></li>
                <li><a href="/docs/get_started#models">Models</a></li>
                <li><a href="/docs/get_started#authentication">Authentication</a></li>
            </ol>
        </li>
        <li>
            <a href="/docs/api">APIs</a>
            <ol>
                <li><a href="/docs/api#controllers">Controllers</a></li>
                <li><a href="/docs/api#views">Views</a></li>
                <li><a href="/docs/api#models">Models</a></li>
            </ol>
        </li>
        <li><a href="/docs/examples">Examples</a></li>
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