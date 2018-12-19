<?php
/**
 * User: zjl
 * Date: 2018/08/08 10:00
 */
namespace src\model\request\operation;
use \src\model\request\operation\BaseOperation;
use \src\common\OperationType;

class LogCreateOperation extends BaseOperation {
    private  $topic; //string
    private  $datas = array(); //List<String>

    public function __construct() {
        $this->operationType = OperationType::LOG_CREATE;
    }

    /**
     * 
     */
    public function getOperationType() {
        return $this->operationType;
    }

    /**
     * @return mixed
     */
    public function getTopic() {
        return $this->topic;
    }

    /**
     * @param mixed $topic
     *
     * @return self
     */
    public function setTopic($topic) {
        $this->topic = $topic;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDatas() {
        return $this->datas;
    }

    /**
     * @param mixed $datas
     *
     * @return self
     */
    public function setDatas($datas) {
        if($datas){
            foreach ($datas as $key => $value) {
                array_push($this->datas, $value);
            }
        }
        return $this;
    } 

    /**
     * @param mixed $datas
     *
     * @return self
     */
    public function addData($data) {
        if($data) {
            array_push($this->datas, $data);
        }
        return $this;
    }
}
?>
