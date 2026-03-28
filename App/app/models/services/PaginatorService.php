<?php

/**
 * questo servizio prende il posto di Models\model\PaginatorVM della versione precedente
 */

 namespace app\models\services;

 use PDO;
 use PDOException;

 class PaginatorService
 {
    protected int $Page; //pagina corrente sul paginatore
    protected string $Search=''; //valore da cercare (in tutti i campi
    protected string $OrderBy; //cammpo da ordinare
    protected string $OrderDir; //direzione ordinamento
    protected int $Offset; //startrecordPage nel database
    protected int $Limit=20; //recordsxPage nel database
    protected int $Links=5; //numero di link preselezionato def 5
    protected ?string $NumLinks=null; //numero di link nel paginatore (5+1+5)
    protected ?string $SessionLink=null; //il link al controller corrente ??
    protected int $TotalRows=0; //numero totale dei records nel DB
    protected ?string $SearchUrl=null; //url in cui effettuare le ricerche
    protected ?string $CreateUrl=null; //url per le form di creazione
    protected string $Direction='up';
    protected ?string $Query=null;
    protected ?string $Controller=null;
    protected PDO $conn;
    protected array $columns=[];
    protected string $listName='list'; //nome della lista per il paginatore


    public function __construct (PDO $conn ,?int $page=1,?string $ob='',?string $od='', ?string $context = '',?bool $urlCreate=false,string $listName='list'){
        $this->conn=$conn;
        $this->Page=max(1,$page);
        $this->OrderBy=$ob;
        $this->OrderDir =  $od;
        $this->Links = max(1,$this->getLinks());
        $this->setController($context, $urlCreate, $listName);
        $this->setOffset();
    }
    
    /**
     * initializeSession function
     * pone i dati di partenza nella sessione
     * (creato da OpenAI)
     * @return void
     */
    protected function initializeSession(): void
    {
        $context=$this->Controller;
        $sessionKey = "curPagination_$context"; 
        if (!isset($_SESSION[$sessionKey])) {
            $this->setSessionData($context, 'curPage', $this->Page);
            $this->setSessionData($context, 'curOrdBy', $this->OrderBy);
            $this->setSessionData($context, 'curOrdDir', $this->OrderDir);
            $this->setSessionData($context, 'curSearch',$this->Search);
        }
    }

    public function getPage():int
    {
        return $this->Page;
    }

    public function getOrderBy():string
    {
        return  $this->OrderBy;
    }

    public function getOrderDir():string
    {
       return $this->OrderDir;
    }

    public function getOffset():string
    {
        return $this->Offset;
    }

    public function getTotalRows(): int
    {
        return $this->TotalRows;
    }

    public function getLimit():int
    {
        return $this->Limit;
    }

    public function getLinks():int
    {
        return $this->Links;
    }
    public function getSessionLink():string
    {
        return $this->SessionLink;
    }

    public function getDirection():string
    {
        return $this->OrderDir==='ASC'?'up':'down';
    }

    public function getSearch():string
    {
        if(!empty($this->getSessionData($this->Controller,'curSearch'))){
            return $this->getSessionData($this->Controller, 'curSearch');
        }else{
            return $this->Search;
        }
    }

    public function getController():string{
        if($this->Controller){
           return $this->Controller;
        }else{
            return '';
        }   
    }
    public function getCreateUrl():string
    {
        return !is_null($this->CreateUrl)? $this->CreateUrl:'';
    }

    public function getDataPage(string $ser)
    {
        $list=[];
        $query ="{$this->Query} ORDER BY  {$this->OrderBy}  {$this->OrderDir} LIMIT {$this->Offset}, {$this->Limit}";
            
        $stm = $this->conn->prepare($query);
        
        $stm->bindParam('se', $ser, PDO::PARAM_STR);
        
        $stm->execute();
        if ($stm && $stm->rowCount()) {
            $list = $stm->fetchAll(PDO::FETCH_OBJ);
        }

        return $list;

    }

    protected function getSessionData(string $context, string $key)
    {
        $sessionKey = "curPagination_$context";
        return $_SESSION[$sessionKey][$key] ?? null;
    }

    protected function setSessionData(string $context, string $key, $value): void
    {
        $sessionKey = "curPagination_$context";
        $_SESSION[$sessionKey][$key] = $value;
        
    }
   
    public function setPage(int $page):void
    {
        $this->Page = $page;
        $this->setSessionData($this->Controller,'curPage',$page);
        $this->setOffSet();
    }

    public function setOrderBy($ob):void
    {
        if(is_null($this->getSessionData($this->Controller,'curOrdBy'))){
            $this->OrderBy = $ob;
            $this->setSessionData($this->Controller,'curOrdBy',$ob);
            $this->OrderDir  = 'ASC';
            $this->setSessionData($this->Controller,'curOrdDir','ASC');
            $this->Direction='up';
        }else{
            if ($ob == $this->getSessionData($this->Controller,'curOrdBy')) {
                    $this->OrderBy = $ob;
                    $this->setSessionData($this->Controller,'curOrdBy',$ob);
                    $this->toggleOrderDir($this->getSessionData($this->Controller,'curOrdDir')); // Only toggle if the same column is selected
                } else {
                    $this->OrderBy = $ob;
                    $this->setSessionData($this->Controller,'curOrdBy', $ob);
                    $this->OrderDir  = 'ASC';
                    $this->setSessionData($this->Controller,'curOrdDir', 'ASC'); // Reset to default sort direction
                    $this->Direction='up';
                }    
        }
    }

    protected function toggleOrderDir(string $od): void
    {
        $this->OrderDir = $od == 'ASC' ? 'DESC' : 'ASC';
        $this->setSessionData($this->Controller,'curOrdDir', $this->OrderDir);
        $this->Direction = $this->OrderDir == 'ASC' ? 'up' : 'down';
    }

    public function setOffset():void
    {
        $this->Offset = ($this->Page - 1) * $this->Limit;
    }

    /**
     * Reset the pagination and search settings.
     */
    public function reset(): void
    {
        $this->Search = '';
        $this->setSessionData($this->Controller,'curSearch','');
        $this->setPage(1);
        $this->setOrderBy($this->OrderBy); // Reset to the default or initial value if needed
        $this->OrderDir = 'ASC';
        $this->setSessionData($this->Controller,'curOrdDir', $this->OrderDir);
    }

    public function setController(string $context, ?bool $dontCreate=false,string $listName='list'):void
    {          
        $this->Controller = $context;
        if($dontCreate){
            $this->CreateUrl="/{$context}/create/";
        }
        $this->SessionLink="/{$context}/{$listName}/";
    }
        
    /**
     * Summary of setSearch controlla se si st� effettuando una ricerca su una data e,
     * eventualmente formatta in modo adeguato il valore immesso per poterlo confrontare
     * con quello contenuto nel database
     * @param string $search
     */
    public function setSearch(string $search)
    {
        if (validateDate($search)) {
            $format = 'd/m/Y';
            $dateObject = \DateTime::createFromFormat($format, $search);
            if ($dateObject !== false){
                $search=$dateObject->format('Y-m-d');
            }
        }
        $this->Search = $search;
        $this->setSessionData($this->Controller,'curSearch',$search);
    }

    public function paginationSet( string $action, $val): void
    {
        // Use context-specific session keys
        $this->initializeSession();
        $context=$this->Controller;
        switch ($action) {
            case 'page':
                $this->setPage((int)$val);
                if(isset($_SESSION['curPagination_'.$context])){
                    $this->OrderBy=$_SESSION['curPagination_'. $context]['curOrdBy'];
                    $this->OrderDir = $_SESSION['curPagination_' . $context]['curOrdDir'];
                }
                break;
            case 'ordBy':
                $this->setOrderBy($val);
                break;
            case 'search':
                $this->setSearch((string)$val);
              //  $this->setPage(1); // Reset to page 1 on a new search
                break;
            case 'reset':
                $this->reset();
                break;
        }
        //$this->initializeSession($context);
        // Sync context session with current state
        // $this->setSessionData($context, 'curPage', $this->Page);
        // $this->setSessionData($context, 'curOrdBy', $this->OrderBy);
        // $this->setSessionData($context, 'curOrdDir', $this->OrderDir);
        // $this->setSessionData($context, 'curSearch', $this->Search);
    }

    public function setQuery(string $sql, ?array $params = [], ?array $values = [])
    {
        $this->columns = $this->getQueryColumns($sql);
        $this->Query = $sql;

        try {
            $stm = $this->conn->prepare($sql);

            // Bind parameters if any
            if (!empty($params) && !empty($values)) {
                foreach ($params as $key => $param) {

                 //   if (isset($values[$key])) {
                        if(is_numeric($values[$key])){
                            $stm->bindParam($param, $values[$key], PDO::PARAM_INT);
                        } else {
                            $stm->bindParam($param, $values[$key], PDO::PARAM_STR);
                        }
                 //   }
                }
            }

            $stm->execute();
            $this->TotalRows = $stm->rowCount();

        } catch (PDOException $e) {
            // Log the error message
            error_log("Database error: " . $e->getMessage() . " SQL: " . $sql . " Params: " . json_encode($params) . " Values: " . json_encode($values));
            echo "Database error: " . $e->getMessage() . " SQL: " . $sql . " Params: " . json_encode($params) . " Values: " . json_encode($values);
            // Optionally re-throw the exception or return an error code
            throw $e;  // Or return false; or set an error property in the class
        }
    }

public function setQuery2(string $sql, ?array $params = [], ?array $values = []): array
{
    $list = [];
    $this->Query = $sql;

    try {
        $countSql = "SELECT COUNT(*) FROM ({$sql}) AS count_table";// 1. Conta il numero totale di righe (senza LIMIT e ORDER BY)
        $countStmt = $this->conn->prepare($countSql);

        if(is_countable($params) && is_countable($values)){
            foreach ($params as $key => $param) {
                if (isset($values[$key])) {
                    if(is_numeric($values[$key])){
                        $countStmt->bindParam($param, $values[$key], PDO::PARAM_INT);
                    } else {
                        $countStmt->bindParam($param, $values[$key], PDO::PARAM_STR);
                    }
                }
            }
        }
        $countStmt->execute();
        $this->TotalRows = (int)$countStmt->fetchColumn(); // Ottieni il conteggio totale

        // 2. Costruisci la query con ORDER BY e LIMIT
        $pagedSql = "{$this->Query} ORDER BY {$this->OrderBy} {$this->OrderDir} LIMIT {$this->Offset}, {$this->Limit}";
        $stmt = $this->conn->prepare($pagedSql);

        if(is_countable($params) && is_countable($values)){
            foreach ($params as $key => $param) {
                if (isset($values[$key])) {
                    if(is_numeric($values[$key])){
                        $stmt->bindParam($param, $values[$key], PDO::PARAM_INT);
                    } else {
                        $stmt->bindParam($param, $values[$key], PDO::PARAM_STR);
                    }
                }
            }
        }

        // 3. Esegui la query paginata
        $stmt->execute();
        if ($stmt && $stmt->rowCount()) {
            $list = $stmt->fetchAll(PDO::FETCH_OBJ);
        }
    } catch (PDOException $e) {
        // Log the error message
        error_log("Database error: " . $e->getMessage() . " SQL: " . $sql . " Params: " . json_encode($params) . " Values: " . json_encode($values));
        echo "Database error: " . $e->getMessage() . " SQL: " . $sql . " Params: " . json_encode($params) . " Values: " . json_encode($values);
        // Optionally re-throw the exception or return an error code
        throw $e;  // Or return false; or set an error property in the class
    }

    return $list;
}

    private function getQueryColumns( string $query): array {
    try {
        $stmt = $this->conn->prepare($query);

        $stmt->execute();
        
        // Fetch column metadata
        $columns = [];
        for ($i = 0; $i < $stmt->columnCount(); $i++) {
            $colMeta = $stmt->getColumnMeta($i);
            if (isset($colMeta['name'])) {
                $columns[] = $colMeta['name'];
            }
        }

        return $columns;
    } catch (PDOException $e) {
        // Handle any errors
        if($e->errorInfo[1] !== 1064){
            echo "Error: " . $e->getMessage();
        }
        return [];
    }
    }


}