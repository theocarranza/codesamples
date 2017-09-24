<?php

/**
 * Description of SetupController
 * Creates dummy data for testing and demo purposes. Creates one of all types of elections currently available,
 * in different states, including a closed, already audited election.
 *
 * @author Théo Carranza
 * theocarranza@gmail.com
 */
class SetupController extends Core_Controller_Action
{
    /* Loads the inicial subscription user base and grants root privileges to the first member */

    public function indexAction()
    {
        $result = array();
        try {
            $this->generatesubscriptionAction();
        } catch (Exception $ex) {
            $result['success'] = false;
            $result['message'] = "Houve um erro na rotina de criação de cadastro.";
            $result['ex_message'] = $ex->getMessage();
            $result['error'] = error_get_last();
        }
        try {
            $this->generateelectionAction();
            $result['success'] = true;
            $result['message'] = "Setup concluído com sucesso!";
        } catch (Exception $ex) {
            $result['success'] = false;
            $result['message'] = "Houve um erro na rotina de criação de eleições.";
            $result['ex_message'] = $ex->getMessage();
            $result['error'] = error_get_last();
        }

        $this->_helper->layout->disableLayout();
        $this->view->assign($result);

    }

    private function generatecadastroAction()
    {
        return $this->_helper->GenerateSubscriptionoBO();
    }

    private function generateeleicaoAction()
    {
        return $this->_helper->GenerateEleicaoBO();

    }

}

?>
