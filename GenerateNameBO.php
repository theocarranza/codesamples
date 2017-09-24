<?php

/**
 * Classe que popula a tabela de empresas, cadastra tipos de combustivel quando pertinente, e acrescenta contatos.
 *
 * @author Theo Carranza <theocarranza@gmail.com>
 */

class Core_BO_Setup_GenerateNameBO extends Zend_Controller_Action_Helper_Abstract
{

    public function direct()
    {
        return $this->genName();
    }

    private function genName()
    {

        /* Generates random names using csv files */

        /* Loads the contents of the CSV files into variables */
        $firstNameCSV = fopen("/var/www/sigs-demo/database/firstNames.csv", "r");
        $middleNameCSV = fopen("/var/www/sigs-demo/database/middleNames.csv", "r");
        $lastNameCSV = fopen("/var/www/sigs-demo/database/lastNames.csv", "r");

        /* Creates arrays with the values from the files*/

        $firstNameList = fgetcsv($firstNameCSV, 0, "\r");
        $middleNameList = fgetcsv($middleNameCSV, 0, "\r");
        $lastNameList = fgetcsv($lastNameCSV, 0, "\r");

        /*
         * Defines the size of the subscription array, and gets a random number within that range to be used
         * to pick a value (name) from each array to be used as first, middle, and last name. 
         */

        $numSubscriptions = 100;
        $i = 0;
        $fullName = array();

        while ($i < $numSubscriptions) {
            $firstName = rand($i, sizeof($firstNameList));
            $middleName = rand($i, sizeof($middleNameList));
            $lastName = rand($i, sizeof($lastNameList));

            $fullName[$i]['firstName'] = $firstNameList[$firstName];
            $fullName[$i]['middleName'] = $middleNameList[$middleName];
            $fullName[$i]['lastName'] = $lastNameList[$lastName];
            $i++;
        }
        /* Sorts the list alphabetically */

        sort($fullName);

        fclose($firstNameCSV);
        fclose($middleNameCSV);
        fclose($lastNameCSV);
        return $fullName;
    }

}

?>
