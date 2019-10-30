<?php

namespace GlobalInitiative\XSSProtection\Middleware;

use Illuminate\Http\Request;
use Closure;

class XSSProtection
{
    /**
     * https://www.php.net/manual/en/function.strip-tags.php#86964
     */
    private function strip_tags_content($text, $tags = '', $invert = FALSE)
    {
        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if (is_array($tags) and count($tags) > 0) {
            if ($invert == FALSE) {
                return preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
            } else {
                return preg_replace('@<(' . implode('|', $tags) . ')\b.*?>.*?</\1>@si', '', $text);
            }
        } elseif ($invert == FALSE) {
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
        }
        return $text;
    }

    /**
     * The following method loops through all request input and strips out all tags from
     * the request. This to ensure that users are unable to set ANY HTML within the form
     * submissions, but also cleans up input.
     *
     * @param Request $request
     * @param callable $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        if (!$request->isMethod('post') && !$request->isMethod('put')) {
            return $next($request);
        }

        $input = $request->all();

        array_walk_recursive($input, function (&$input) {
            if ($input) {
                $input = $this->strip_tags_content($input, '<iframe><script>', true);
            }
        });
        $request->merge($input);

        return $next($request);
    }
}
