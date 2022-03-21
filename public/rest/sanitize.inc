<?php

function sanitize_string($dirty)
{
    if(isset($dirty))
    {
        $dirty = trim($dirty);
        $dirty = trim($dirty, '"');
        $bad = array(";", '"', "\n", "\r");
        $dirty = str_replace($bad, " ", $dirty);
        return $dirty;
    }
    else
    {
        return "";
    }
}

?>
