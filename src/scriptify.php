<?php
namespace w3l;

class scriptify
{
    public function __construct() { }
    
    /**
     *

     */
    static function encode(...$params)
    {
        /*
         * Default values
         */
        $scriptURI = "scriptify.php?js="; // URI for output
        $queryStringLimit = 3800; // Preconfigurated for apache Apache
        
        switch (count($params)) {
            // URI and Javascript is set
            case 1:
                $js = $params[0];
                break;
            // URI and Javascript is set
            case 2:
                $scriptURI = $params[0];
                $js = $params[1];
                break;
            // URI, limit and Javascript is set
            case 3:
                $scriptURI = $params[0];
                $queryStringLimit = $params[1];
                $js = $params[2];
                break;
            default:
                throw new Exception('Wrong number of parameters');
                break;
        }
        
        $js = str_replace(array("<script>", "</script>"), "", $js); // @todo: Should keep any additional Javascript tags.
        
        $minifier = new MatthiasMullie\Minify\JS($js);
        $js = $minifier->minify();

        $js_base64 = base64_encode($js);
        
        /**
         * Check length of query to avoid any problems with Apache.
         * @link: http://stackoverflow.com/a/812962/1729850
         * Limit to ~2000 characters for Internet Explorer support(Notice, not Edge).
         * For IIS(and no legacy Internet Explorer support) the limit should be around 16000 characters.
         */
        $strlen = strlen($js_base64);

        if($strlen > $queryStringLimit) {
            $js = 'alert("Over the script-length limit!\nLimit: '.($queryStringLimit*0.75).' chr(s).\nJavascript: '.$strlen.' chr.\n🐱‍​💻» Please contact your friendly webmaster!")';
            $js_base64 = base64_encode($js);
        }
        
        $hash = hash('sha384', $js, true);
        $hash_base64 = base64_encode($hash);
        
        return '<script src="'.$scriptURI.urlencode($js_base64).'" integrity="sha384-'.$hash_base64.'"></script>';
    }
    
    static function decode($js)
    {
        return base64_decode($js);
    }
}
