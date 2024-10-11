<?php
declare(strict_types=1);
namespace IBG {

class APIException extends \Exception {

    private $details=[];
    
    public function __construct($message, $args, $code=0, Exception $previous = null) {

            
        $this->details = [];
        
        parent::__construct(vsprintf($message, $args), $code, $previous);

    }
    
    public function addDetails($item){
        $this->details[] = $item;
    }
    
    public function countDetails(){
        return count($this->details);
    }
    
    public function getDetails(){
        return $this->details;
    }
    
    public function addAllDetails(array $details){
        foreach($details as $detail){
            $this->details[] = $detail;
        }   
    }
    
}


set_exception_handler(function(\Throwable $exception){

    global $_output_filepath, $_is_service;

    $log = [
        "id" => uniqid('', true),
        "Timestamp" => (string) date("Y-m-d H:i:s"),
        "Message" => $exception->getMessage (),
        "Previous" => $exception->getPrevious (),
        "Code" => (string) $exception->getCode (),
        "File" => $exception->getFile (),
        "Line" => (string)$exception->getLine (),
        "TraceAsString" => $exception->getTraceAsString (),
        "Class" => get_class($exception),
    ];

    $log['details'] = [];

    if($_is_service == '1') {
        if ($exception instanceof APIException) {
            foreach($exception->getDetails() as $value){
                $log['details'][]  = $value;
            }
        }
        $ret = ['success'=>false, 'title'=>'Error!', 'icon'=>'error', 'message'=>$exception->getMessage(), 'detail'=>$log];
        // echo $ret;
        echo json_encode($ret, JSON_PRETTY_PRINT);
        file_put_contents($_output_filepath, json_encode($ret));
        exit(0);
    } else {
        // echo $ret;
        echo json_encode($ret, JSON_PRETTY_PRINT);
        file_put_contents($_output_filepath, json_encode($log));
        exit(1);
    }

});

function service_return(array $param){
    $success = isset($param['success']) ? $param['success'] : true;
    $title = isset($param['title']) ? $param['title'] : 'Genial!';
    $icon = isset($param['icon']) ? $param['icon'] : 'success';
    $message = isset($param['message']) ? $param['message'] : 'success';
    $data = isset($param['data']) ? $param['data'] : [];

    $ret = ['success'=>$success, 'title'=>$title, 'icon'=>$icon, 'message'=>$message, 'detail'=>$data];

    $raw_bytes = json_encode($ret,JSON_PRETTY_PRINT);
    
    echo $raw_bytes;

    exit(0);
}

function rlog(...$data){
    $user = 'ANCIZAR_LOPEZ';
    $_app_name = 'parametros_cartera';
    foreach($data as $item){
        $debug_arr = debug_backtrace();
        file_put_contents('debug.log',"\n[ ".$user. " ]\n"."[ ".date("Y-m-d H:i:s")." ]\n"."[ File: ".$debug_arr[0]['file']." ]\n[ Line: ".$debug_arr[0]['line']." ]\n[ Data: ".var_export($item,true)." ]\n\n",FILE_APPEND);
    }
}

function st($string, array $param){
  return vsprintf($string, $param);
}

function mfex($field){
    $trace = debug_backtrace();
    throw new \Exception("Parametro obligatorio '$field' no hallado en la funcion '".$trace[1]['function']."' Ubucada en :".$trace[1]['file'].':'.$trace[1]['line']."'"); 
    return "";
}

function createJSON(array $param){
    $idTable = isset($param['idTable']) ? $param['idTable'] : 'idTable';
    $data = isset($param['data']) ? $param['data'] : [];
    foreach (glob($idTable.".json") as $filename) {
       unlink($filename);
    }
    file_put_contents($idTable.".json",json_encode($data));
}

function crearSentenciaInsert(array $param)
{
    $tabla = isset($param['tabla']) ? $param['tabla'] : '';
    $conten = isset($param['conten']) ? $param['conten'] : [];

    $insert = " INSERT INTO $tabla ";
    $claves = "(" . implode(', ', array_keys($conten)) . ")";
    $valores =" values (" .implode(',', array_map(function($item){return trim(sprintf("'%s'", $item));}, $conten)). ")";

    $insert = $insert . $claves . $valores;
    if (empty($tabla) || empty($conten)) {
        $insert = '';
    }
    
    return $insert;
}

function crearSentenciaUpdate(array $param)
{
    $tabla = isset($param['tabla']) ? $param['tabla'] : '';
    $sets = isset($param['sets']) ? $param['sets'] : [];
    $where = isset($param['where']) ? $param['where'] : [];

    $update = " UPDATE $tabla ";
    foreach ($sets as $column => $value) {
        $filed_groups[] = " $column = '".addslashes($value)."'"; 
    }
    $columns_str = implode(',', $filed_groups);

    foreach ($where as $column => $value) {
        $where_groups[] = " $column = '".addslashes($value)."'"; 
    }
    $clause_where = implode(' AND ', $where_groups);
    // $clause_where = 

    $update = $update .' SET '. $columns_str . ' WHERE '.$clause_where;
    return $update;
}

function dateFormat($date) 
{
    $newDate = $date;
    $d = new \DateTime();
    $dateVal = $d->createFromFormat('Y-m-d', $date);
    if ($dateVal) {
        $newDate = $dateVal->format('m-d-Y');
    }
    return $newDate;
}

function mes_texto($mes)
  {
    switch($mes)
    {
      case 1 :$cad="Enero";
           break;
      case 2 :$cad="Febrero";
           break;
      case 3 :$cad="Marzo";
           break;
      case 4 :$cad="Abril";
           break;
      case 5 :$cad="Mayo";
           break;
      case 6 :$cad="Junio";
           break;
      case 7 :$cad="Julio";
           break;
      case 8 :$cad="Agosto";
           break;
      case 9 :$cad="Septiembre";
           break;
      case 10 :$cad="Octubre";
           break;
      case 11 :$cad="Noviembre";
           break;
      case 12 :$cad="Diciembre";
           break;
      default:exit();
    }
    return $cad;
  }


global $_output_filepath;
global $_is_service;



$_output_filepath = "log.json";
$_is_service = "1";
   
   class JSCode{
    public $code;
    
    public function __construct($code){
        $this->code = $code;
    }
    
    public function __toString(){
        return (string) $this->code;
    }
    
    public static function enclose($value){
        if($value instanceof JSCode )
            return (string) $value;
        elseif(gettype($value) == "string"){
        $value = addslashes($value);
            return "'$value'";
        }else{
            return json_encode($value);
        }
    }
}


}



?>
