# PHP Markdown Extra plugin for DokuWiki
    ---- plugin ----
    description: Parses PHP Markdown Extra blocks.
    author     : Joonas Pulakka
    email      : joonas.pulakka@iki.fi
    type       : syntax
    lastupdate : 2010-11-06
    compatible : 2009-12-25 “Lemming” and newer
    depends    : 
    conflicts  :
    similar    : markdown 
    tags       : formatting, markup_language
    downloadurl: http://madpropellerhead.com/projects/markdownextra.tgz
    ----

##Download and Installation

Download and install the plugin using the Plugin Manager using the following URL. Refer to [[:Plugins]] on how to install plugins manually.



##Usage

If the page name ends with ''.md'' suffix, it gets automatically parsed using PHP Markdown Extra. To use that markup in other pages, the content must be embedded in a markdown block. For example:

    <markdown>
    Header 1
    ========

    ~~~
    some code
    ~~~

    Paragraph

    Header 2
    --------

    - A
    - simple
    - list

    1. And
    2. numbered
    3. list

    Quite intuitive? *emphasis*, **strong**, etc.
    </markdown>


For syntax, refer to http://michelf.com/projects/php-markdown/extra/