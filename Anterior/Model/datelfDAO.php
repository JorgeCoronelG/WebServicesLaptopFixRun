<?php
/**
 * Description of DateDAO
 *
 * @author Jorge Coronel González
 */
require_once 'connection.php';
require_once 'datelfBEAN.php';
class DateLFDAO extends Connection {

    function __construct() {
        parent::__construct();
    }
    
    public function insert($date){
        $query = "INSERT INTO DATE_LF VALUES(null,"
                . "'".$date->getIdCus()."',"
                . "'".$date->getDateCus()."',"
                . "'".$date->getHourCus()."',"
                . "'".utf8_decode($date->getDescProblem())."')";
        if(mysqli_query($this->connection, $query) === TRUE){
            $this->closeConnection();
            return $date;
        }else{
            $this->closeConnection();
            return FALSE;
        }
    }
    
    public function checkHourDates($date, $hour){
        $query = "SELECT * FROM DATE_LF WHERE dateCus = '$date' AND hourCus = '$hour'";
        $result = mysqli_query($this->connection, $query);
        if(mysqli_num_rows($result) < 10){
            return FALSE;
        }else{
            return TRUE;
        }
    }
    
    public function getDate($id){
        $query = "SELECT idDateLF, DATE_FORMAT(dateCus, '%d-%m-%Y') AS date, TIME_FORMAT(hourCus, '%h %i %p') AS hour, "
                . "descProblem, nameCus FROM DATE_LF d INNER JOIN CUSTOMER c ON d.idCus = c.idCus "
                . "WHERE idDateLF = $id";
        $result = mysqli_query($this->connection, $query);
        if(mysqli_num_rows($result) == 0){
            $this->closeConnection();
            return FALSE;
        }else{
            $this->closeConnection();
            $dataDate = mysqli_fetch_array($result);
            $date = new DatelfBEAN();
            $date->setIdDate($dataDate['idDateLF']);
            $date->setIdCus($dataDate['nameCus']);
            $date->setDateCus($dataDate['date']);
            $date->setHourCus($dataDate['hour']);
            $date->setDescProblem($dataDate['descProblem']);
            return $date;
        }
    }
    
    public function getDatesLaptopFix(){
        $query = "SELECT idDateLF, nameCus, DATE_FORMAT(dateCus, '%d-%m-%Y') AS date, TIME_FORMAT(hourCus, '%h %i %p') AS hour "
                . "FROM DATE_LF d INNER JOIN CUSTOMER c ON d.idCus = c.idCus "
                . "WHERE idDateLF IN (SELECT idDateLF FROM DATE_LF WHERE dateCus = curdate() AND hourCus >= curtime()) OR "
                . "idDateLF IN (SELECT idDateLF FROM DATE_LF WHERE dateCus = curdate() + 1) ORDER BY date, hour";
        $result = mysqli_query($this->connection, $query);
        if(mysqli_num_rows($result) == 0){
            $this->closeConnection();
            return FALSE;
        }else{
            $this->closeConnection();
            return $result;
        }
    }
    
    public function getDatesCustomer($id){
        $query = "SELECT DATE_FORMAT(dateCus, '%d-%m-%Y') AS date, TIME_FORMAT(hourCus, '%h %i %p') AS hour, descProblem "
                . "FROM DATE_LF WHERE idDateLF IN (SELECT idDateLF FROM DATE_LF WHERE dateCus >= curdate()) AND idCus = '$id' "
                . "ORDER BY date, hour";
        $result = mysqli_query($this->connection, $query);
        if(mysqli_num_rows($result) == 0){
            $this->closeConnection();
            return FALSE;
        }else{
            $this->closeConnection();
            return $result;
        }
    }
    
}