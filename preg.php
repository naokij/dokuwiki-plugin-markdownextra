<?php
$test = "---
{{tag>test}}
---


fdsa
fdsa
fd
#title for kb/test.md
[link2](kb/test2)
fsafdsfafsa
fsa
ddsafdsfsa
fds

---
good
---";
preg_match('/^---\s*\n(.*?\n?)^---\s*$\n?(.+)/sm',$test, $match);
var_dump($match);