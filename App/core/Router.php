<?php
namespace core;

use Exception;

//use App\Models\Exceptions\Swoole\Exception;

class Router
{

    public function __construct(protected array $routes = ['POST' => [],'GET' => []])
    {

    }

    public function loadRoutes(array $routes): void{
        $this->routes = $routes;
    }

    public function getRoutes(): array{
        return $this->routes;
    }

    public function dispatch(): array{
        
        $url = $_SERVER["REQUEST_URI"] ?? $_SERVER["REDIRECT_URL"]??'';

        $sanUrl = $this->sanitizeUrl($url);
        
        $segment = trim(parse_url((string)$sanUrl, PHP_URL_PATH), '/');//PHP_URL_PATH
        $segment = $segment ?: '/';

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        $urls = $this->routes[$method];

        if (array_key_exists($segment, $urls)) {
            return  $urls[$segment];
        }
        $ret =  $this->matchRoute($urls, $segment);

        if (!$ret) {
            if (ENV == 'develop') {
                // Prima di generare un'eccezione, aggiungi un log di debug
                error_log("No route matched url: $sanUrl method: $method segment: $segment");
                // Genera un'eccezione
                throw new Exception("No route matched url: $sanUrl method: $method segment: $segment");
            } else {
                die('Error. Push back button!');
            }
        }
        return $ret;
    }
    
    /**
     * sanitizeUrl function
     * elimina alcuni caratteri che vengono immessi dal browser nelle stringhe di ricerca
     * prima di potere avviare il confronto dell?url
     * 
     * @param  [type] $url
     * @return void
     */
    protected function sanitizeUrl($url=''):string
    {
        $sanitUrl=$url;
        if (str_contains($sanitUrl, 'search')) {
            if ( stripos($sanitUrl, '?search=') !== false ) {
                //l'invio tramite form del campo search produce un path
                //del tipo: contr/action/search/?search=valoredacercare
                //per cui � necessario elimminare ?search= finale 
                //lascinado solo contact/action/search/valoredacercare
                $start = stripos($sanitUrl, '?search=');
                $verb = substr($sanitUrl, $start + 8);

                if (stripos($verb, '%2F') !== false) {
                    $verb = str_replace("%2F", "-", $verb);
                }

                $sanitUrl = substr($sanitUrl, 0, $start);
                $verb = urldecode($verb);
                $pattern2 = "/[^a-zA-Z0-9àèìòù@\s'.\/\-%+]+/";
                $verb = htmlspecialchars($verb);
                $verb = preg_replace($pattern2, '', $verb);
                $verb = str_replace('.', '_DOT_', $verb);//necessario per ripristinare il valore da cercare '.' è presente nelle ricerche url
                $verb = str_replace('@','_AMPER_',$verb);//necessario per ripristinare il valore da cercare @ è presente nelle ricerche email
                $sanitUrl = $sanitUrl . $verb;
            }
            // return $sanitUrl;
        }
        if (str_contains($sanitUrl, '?email=')) {
            $sanitUrl = str_replace('?email=', '', $sanitUrl);//elimina ?email=
        }
        return $sanitUrl;
    }
    protected function matchRoute(array $urls, string $segment): array
    {
        $ret = [];
        foreach ($urls as $seg => $classArray) {
            if (!str_contains($seg, ':')) {
                continue;
            }
            $seg = preg_quote($seg);
            $pattern = preg_replace('/\\\:[a-zA-Z0-9\-\_\ \']+/', '([a-zA-Z0-9\-\_\ \']+)', $seg);

            if (preg_match('@^' . $pattern . '$@', $segment, $matches)) {

                array_shift($matches);
                $classArray[] = $matches;
                $ret = $classArray;
                break;
            }
        }
        return $ret;
    }
}