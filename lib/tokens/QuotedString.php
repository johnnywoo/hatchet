<?

namespace hatchet\tokens;

use hatchet\Exception;

class QuotedString extends Token
{
    public function scan(&$text, $whitespace_mode_regexp)
    {
        // implicit whitespace
        if ($whitespace_mode_regexp) {
            $text = preg_replace($whitespace_mode_regexp, '', $text);
        }

        if (preg_match('/^"(\\\\["trn\\\\]|\\\\x[0-9A-Za-f]{2}|[^\\\\"]+)*"/', $text, $m)) {
            $text = substr($text, strlen($m[0]));
            return array(
                'name'        => $this->name,
                'child_nodes' => array(),
                'text'        => $m[0],
            );
        }
        return null;
    }

    public static function decode($quoted_string)
    {
        return preg_replace_callback(
            '/\\\\(["trn\\\\])|\\\\x([0-9A-Za-f]{2})/',
            function ($m) {
                if (!empty($m[1])) {
                    switch ($m[1]) {
                        case '\\': return '\\';
                        case '"':  return '"';
                        case 'n':  return "\n";
                        case 'r':  return "\r";
                        case 't':  return "\t";
                        default:
                            throw new Exception('Really weird symbol encountered: ' . $m[1]);
                    }
                } else {
                    return chr(hexdec($m[2]));
                }
            },
            substr($quoted_string, 1, -1) // removing the quotes
        );
    }
}
