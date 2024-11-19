<?php
// Function to trim text  "..."
function trimText($text, $maxLength = 15)
{
    $text = htmlspecialchars($text);
    return mb_strlen($text) > $maxLength
        ? mb_substr($text, 0, $maxLength) . '...'
        : $text;
}
