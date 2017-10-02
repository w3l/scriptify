<?php
namespace w3l;

class scriptify
{
    
    /** @var string $scriptAttributes Any additional script attributes */
    protected $scriptAttributes = "";
    
    public function __construct() { }
    
    /**
     *
     */
    protected function extractJSandAttributes($input)
    {

        $openingChars = strtolower(substr($input, 0, 8));

        if($openingChars == '<script>') {
            $input = str_ireplace(array("<script>", "</script>"), "", $input);
        /*
         * Possible attributes, do resource demanding DOMDocument stuff. :)
         */
        } elseif($openingChars == '<script ') {
            
            $doc = new \DOMDocument();
            if ($doc->loadHTML(mb_convert_encoding($input, "ISO-8859-1"))) { // Needed because loadHTML expects ISO-8859-1

                $tags = $doc->getElementsByTagName('script')->item(0);

                $input = $tags->nodeValue;
                
                if ($tags->hasAttributes()) {
                    foreach ($tags->attributes as $attr) {
                        if ($attr->nodeName != "src" && $attr->nodeName != "integrity") {
                            $this->scriptAttributes .= " " . $attr->nodeName . ($attr->nodeValue != "" ? '="'.$attr->nodeValue.'"' : '');
                        }
                    }
                }
            }
        }
        /* Fallback */
        if(strtolower(substr($input, 0, 7)) == '<script') {

            preg_match_all("|<script[^>]+>(.*)</[^>]+>|Usi", $input, $result, PREG_SET_ORDER);
            
            if(isset($result[0][1])) {
                $input = $result[0][1];
            }
        }
        
        return $input;
    }
    
    /**
     * Takes content of a script, encode it and return a script tag.
     */
    public function encode(...$params)
    {
        /*
         * Default values
         */
        $scriptURI = "scriptify.php?js="; // URI for output
        $queryStringLimit = 3800; // Preconfigurated for apache Apache
        
        switch (count($params)) {
            // Javascript is set
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

        /**
         * Removing script tag and copying any attributes.
         */
        if (strtolower(substr(ltrim($js), 0, 7)) == '<script') {
            
            $js = $this->extractJSandAttributes(ltrim($js));
        }

        if (class_exists('\\MatthiasMullie\\Minify\\JS')) {
        
            $minifier = new \MatthiasMullie\Minify\JS($js);
            $js = $minifier->minify();

        }
        
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
        
        return '<script src="'.$scriptURI.urlencode($js_base64).'" integrity="sha384-'.$hash_base64.'"'.$this->scriptAttributes.'></script>';
    }
    
    public function decode($js)
    {
        return base64_decode($js);
    }
}
