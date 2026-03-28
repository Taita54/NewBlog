<?php

/*
 * esegue ricerche specifiche nelle tabelle deputate a contenere i dati
 * delle pubblicazoni
 */

namespace app\models\services;

use \PDO;

/**
 * Description of GestPublications
 *
 * @author Giovanni
 */
class Firstmenu {

    protected $conn;
    
    public function __construct(PDO $conn){
       $this->conn = $conn;
    }

    public function all(){
        $result = [];
        $cats=array_keys(getconfig('sectionsBlog'));

        $ct='';
        foreach ($cats as $c){
           $ct.="t.categoria='".$c."' OR "; 
        }
        $cat=substr($ct,0,-4);
        
        $sql="SELECT p.id,p.section,
		        p.title,p.alternative_txt,
                p.date_created,t.categoria,tp.nome_tag
            FROM gdmsoftandpict.pubblicazioni  AS p 
            JOIN gdmsoftandpict.tags_pubblicazioni  AS tp 
            ON tp.id_pubblicazione=p.id
            JOIN gdmsoftandpict.tags as t
            ON t.nome=tp.nome_tag
            WHERE p.section='T' AND ($cat)  
            GROUP BY t.categoria 
            ORDER BY p.date_created DESC;";
        
        $stm = $this->conn->query($sql);
        if ($stm && $stm->rowCount()) {
            $result = $stm->fetchAll(PDO::FETCH_OBJ);
        }
        return $result;
    }

    public function generateFile($f){
        $result=[];
        switch($f){
            case 'terms':
                $result['filename']="Terms.htm";
                break;
            case 'privacy':
                $result['filename']="Privacy.htm";
                break;
            case 'cookie':
                $result['filename']="Cookie.htm";
                break;
        }
        
        return $result;
    }
}