<?php

function StringToEmailBody($text) {
    if(isset($text))
    {
        $text = trim($text);
        $text = trim($text, '"');
        $text = str_replace(" ", "%20", $text);
        $text = str_replace("\r", "", $text);
        $text = str_replace("\n", "%0A%0D", $text);
        return $text;
    }
    else
    {
        return "";
    }
}

?>