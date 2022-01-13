<?php

function sanitize_string($dirty)
{
    $dirty = trim($dirty);
    $dirty = trim($dirty, '"');
    $bad = array(";", '"', "'");
    $dirty = str_replace($bad, " ", $dirty);
    return $dirty;
}

?>
