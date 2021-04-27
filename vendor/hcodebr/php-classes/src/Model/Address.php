<?php
namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class Address extends Model{
    const SESSION_ERROR = "AddressError";
    // criando um método chamado de getCEP
    public static function getCEP($nrcep){
        // retira o ifem e troca por nada "" do campo $nrcep
        $nrcep = str_replace("-", "", $nrcep);
        
        // curl_init informa ao PHP que estamos iniciando o "resource" processo de consulta a uma URL
        $ch = curl_init();
        
        // primeiro paramento é o resource "$ch"
        // segundo é o método de consulta à uma URL
        // terceiro é o endereço da url onde ativaremos a web server para pescar o CEP.
        curl_setopt($ch, CURLOPT_URL, "http://viacep.com.br/ws/$nrcep/json/");
        // a opção abaixo informa se a web server tem que devolver os resultados para nós.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //est� esperando retorna os resultados
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //false n�o exige ssl
        
        // o true no final é para o resultado vir como array e não como objeto.
        $data = json_decode(curl_exec($ch), true);
        // aqui fecha o curl, pois trata-se de um ponteiro que abre lá em cima e fecha.
        curl_close($ch);
        
        return $data;
    }
    
   // Obs.: Esta função não é estática, por que abaixo utilizamos o $this que faz conexão direta com os respectivos objetos.
   public function loadFromCEP($nrcep){
        
        $data = Address::getCEP($nrcep);

        // vefifica se tem contéudo na localidade e se a localidade não esta vazio.
        if(isset($data['logradouro']) && $data['logradouro']){
            $this->setdesaddress($data['logradouro']);
            $this->setdescomplement($data['complemento']);
            $this->setdesdistrict($data['bairro']);
            $this->setdescity($data['localidade']);
            $this->setdesstate($data['uf']);
            $this->setdescountry('Brasil');
            $this->setdeszipcode($nrcep);
        }
    }
    
    public function save()
    {
        
        $sql = new Sql();
        
        $results = $sql->select("CALL sp_addresses_save(:idaddress, :idperson, :desaddress, :descomplement, :descity, :desstate, :descountry, :deszipcode, :desdistrict)", [
            ':idaddress'=>$this->getidaddress(),
            ':idperson'=>$this->getidperson(),
            ':desaddress'=>utf8_decode($this->getdesaddress()),
            ':descomplement'=>utf8_decode($this->getdescomplement()),
            ':descity'=>utf8_decode($this->getdescity()),
            ':desstate'=>utf8_decode($this->getdesstate()),
            ':descountry'=>utf8_decode($this->getdescountry()),
            ':deszipcode'=>$this->getdeszipcode(),
            ':desdistrict'=>utf8_decode($this->getdesdistrict())
        ]);
        
        if(count($results) > 0){
            $this->setData($results[0]);
        }
    }
    
    public static function setMsgError($msg){
        $_SESSION[Address::SESSION_ERROR] = $msg;
    }
    
    public static function getMsgError(){
        $msg = (isset($_SESSION[Address::SESSION_ERROR])) ? $_SESSION[Address::SESSION_ERROR] : "";
        
        Address::clearMsgError();
        
        return $msg;
    }
    
    public static function clearMsgError(){
        $_SESSION[Address::SESSION_ERROR] = NULL;
    }
}