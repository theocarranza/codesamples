<?php

/**
 * Description of SeedCadastroBO
 *
 * Class that populates the database with dummy data for tests and demonstration.
 *
 * Methods
 *
 * Method: createNames: Invokes a clss of type BO that generages random names. Expects a numerical value
 * ranging from 1 to 3, to determine how many names it should generate for a single person (first, middle, and last)
 * 
 * Method: prepareSubscription: Expects an array of names, and prepares and returns an SQL insert statement.
 *
 * Method: insertSubscriptions: expects the SQL insert statement and inserts it into the database in a single operation.
 *
 * Method: grantCredentials: Grand administrative privileges to the first member, and user privileges to the remaining
 * members generated in the previous step.
 *
 * @todo : Improve parametrization and error handling.
 * @author Théo Carranza
 * theocarranza@gmail.com
 */
class Core_BO_Setup_GenerateSubscriptionBO extends Zend_Controller_Action_Helper_Abstract
{
    private $db;
    private $memberModel = new Application_Model_Table(array('name' => 'MEMBER'));
    private $userModel = new Application_Model_Table(array('name' => 'USER'));
    private $credentialModel = new Application_Model_Table(array('name' => 'U_CREDENTIALS'));
    private $areaModel = new Application_Model_Table(array('name' => 'AREA'));

    public $result = array();

    public function direct()
    {
        $this->result['success'] = false;
        $this->result['message'] = $this->getActionController()->_getMessage('MSGERAL001');

        $this->db = Zend_Db_Table::getDefaultAdapter();

        /* 1st step: prepares the SQL statement containing the dummy data for insertion */

        if ($sql = $this->prepareSubscription() == false) {
            return $this->result;
        }

        /* 2nd step:  Sends the SQL staement to the method that performs the insertion */
        if ($this->inserePessoas($sql) == false) {
            return $this->result;
        }

        /* 3rd step: Grants the necessary privileges and creates root user*/
        if ($this->grantCredenciais() == false) {
            return $this->result;
        }

        $this->result['success'] = true;
        $this->setMessage('MSGERAL003');
        return $this->result;
    }

    private function setMessage($msg)
    {
        return $this->result['message'] = $this->getActionController()->_getMessage($msg);
    }

    private function prepareSubscription()
    {
        $names = $this->getActionController()->getHelper('GenerateNomeBO')->direct(); // Invoca a classe que retorna uma lista de nomes
        // Define os modelos que serão usados para criar as entradas do cadastro
        $cityModel = new Application_Model_Table(array(
            'name' => 'CIDADE'
        ));
        $stateOfficeModel = new Application_Model_Table(array(
            'name' => 'STATE_OFFICE_MODEL'
        ));

        $cities = $cityModel->select()->query()->fetchAll(); // All the records from the city model table

        $cols = '(CPF, NAME, EMAIL, EMAIL_SEC, PLACE_OF_BIRTH, ID_CITY, ID_STATE_OFFICE, DT_BIRTH, DT_JOINED, DT_AFFILIATION)';

        foreach ($names as $name) {

            $idPlaceOfBirth = (mt_rand(1, (count($cities))));
            $idCity = (mt_rand(1, (count($cities))));
            $stateOffice = $stateOfficeModel->select()->where('ID_STATE = ' . $cities[$idCity]->ID_STATE)->query()->fetch();
            $cpf = $this->getActionController()->getHelper('cpf')->direct('generate');
            $fullName = $name['firstName'] . " " . $name['middleName'] . " " . $name['lastName'];
            $email = strtolower($nome['firstName'] . '@monolithresearch.net');
            $email_sec = strtolower($nome['middleName'] . '@monolithresearch.net');
            $place_of_birth = $idPlaceOfBirth;
            $id_city = $idCity;
            $id_state_office = $stateOffice->ID;
            $dt_birth = '1970-01-01';
            $dt_joined = '2000-01-01';
            $dt_affiliation = '2005-01-01';

            if (!isset($vals)) {
                $vals = '(' . "'$cpf'" . ',' . "'$fullName'" . ',' . "'$email'" . ',' . "'$email_sec'" . ','
                    . $place_of_birth . ',' . $id_city . ',' . $id_state_office . ',' . "'$dt_birth'"
                    . ',' . "'$dt_joined'" . ',' . "'$dt_affiliation'" . ')';
            }
            else {
                $vals = $vals . ',(' . "'$cpf'" . ',' . "'$fullName'" . ',' . "'$email'" . ',' . "'$email_sec'"
                    . ',' . $place_of_birth . ',' . $id_city . ',' . $id_state_office . ','
                    . "'$dt_birth'" . ',' . "'$dt_joined'" . ',' . "'$dt_affiliation'" . ')';
            }
        }
        try {
            $sql = "INSERT INTO MEMBER $cols VALUES $vals";
        } catch (PDOException $e) {
            $this->setMessage($e);
            return false;
        }

        return $sql;
    }

    private function insertSubscription($sql)
    {
        $this->db->query("CALL CLEARDEMO()")->execute();
        $this->db->beginTransaction();
        
        /*
         * Inserts the data into the table in a single operation.
         * 
         */

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->db->closeConnection();
            $this->setMessage($e);
            return false;
        }

        $this->db->commit();
        $this->db->closeConnection();
        return true;
    }

    private function grantCredentials()
    {

        try {
            $this->userModel->update(array(
                'STATUS' => 1, 'PASSWORD' => hash('sha256', '12345678'), 'ST_PASSWORD' => 1
            ), 'ID = 1');
        } catch (PDOException $e) {
            $this->setMessage($e);
            return false;
        }        
        
        /* Updates root user */

        try {
            $this->memberModel->update(array(
                'name' => 'ROOT'
            ), 'ID = 1');
        } catch (PDOException $e) {
            $this->setMessage($e);
            return false;
        }

        try {
            $this->credentialsModel->insert(array(
                'ID_AREA' => 1,
                'ID_USER' => 1
            ));
        } catch (PDOException $e) {
            $this->setMessage($e);
            return false;
        }

        return true;
    }

}

?>
