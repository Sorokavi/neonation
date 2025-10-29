<?php
// Parsedown - Markdown parser for PHP
// Source: https://github.com/erusev/parsedown (MIT License)
// This is a minimal version for guestbook rendering.

class Parsedown {
    public function text($text) {
        // Very basic Markdown: bold, italic, code, links, line breaks
        $text = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);
        $text = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $text);
        $text = preg_replace('/`(.+?)`/s', '<code>$1</code>', $text);
        $text = preg_replace('/\[(.+?)\]\((.+?)\)/s', '<a href="$2" rel="nofollow">$1</a>', $text);
        $text = nl2br($text);
        return $text;
    }
}
