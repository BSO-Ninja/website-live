<?php

$content = \helper\Editor::loadContent($page['content_id']);

echo "{$content['html']}";
